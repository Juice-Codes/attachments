<?php

namespace Juice\Attachments\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Juice\Attachments\Attachment;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentController
{
    /**
     * Attachment eloquent model.
     *
     * @var Attachment
     */
    protected $attachment;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->attachment = new Attachment;
    }

    /**
     * Upload attachments.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        dd($request->user());
        /** @var UploadedFile $file */

        foreach ($request->file('ja_file') as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $this->attachment = $this->attachment->newInstance([
                'user_id' => config('auth') ? optional($request->user())->getKey() : null,
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType() ?: 'text/plain',
                'size' => $file->getSize(),
                'trash_token' => str_random(8),
            ]);

            $this->attachment->save();

            $file->move($this->attachment->path, $this->attachment->filename);

            $attachments[] = [
                'filename' => $this->attachment->filename,
                'trash_token' => $this->attachment->trash_token
            ];
        }

        return response()->json($attachments ?? []);
    }

    /**
     * Download attachment by its filename.
     *
     * @param Request $request
     * @param string $filename
     *
     * @return BinaryFileResponse
     */
    public function download(Request $request, string $filename)
    {
        $attachment = $this->findAttachment($filename);

        return response()->download(
            $attachment->fullPath,
            $attachment->name,
            [],
            $request->query('d', false) ? 'attachment' : 'inline'
        );
    }

    /**
     * Move attachment to .trash folder and soft delete eloquent record.
     *
     * @param Request $request
     * @param string $filename
     *
     * @return Response
     *
     * @throws Exception
     */
    public function trash(Request $request, string $filename)
    {
        $attachment = $this->findAttachment($filename);

        if (config('auth')) {
            if (is_null($request->user())) {
                abort(401);
            } else if ($request->user()->getKey() !== $attachment->user_id) {
                abort(401);
            }
        }

        if ($request->input('token') !== $attachment->trash_token) {
            abort(401);
        }

        $originPath = $attachment->fullPath;

        $attachment->delete();

        return response()->json(['success' => rename($originPath, $attachment->fullPath)]);
    }

    /**
     * Find attachment and store to cache.
     *
     * @param string $filename
     *
     * @return Attachment
     */
    protected function findAttachment(string $filename): Attachment
    {
        $key = sprintf('ja-%s', md5($filename));

        $minutes = config('juice-attachments.cache-time', 60);

        $attachment = Cache::remember($key, $minutes, function () use ($filename) {
            return $this->attachment->findByFilename($filename);
        });

        if (is_null($attachment) || !is_file($attachment->fullPath)) {
            abort(404);
        }

        return $attachment;
    }
}
