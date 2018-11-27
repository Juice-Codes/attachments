<?php

namespace Juice\Tests;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Juice\Attachments\Attachment;
use Juice\Attachments\Controllers\AttachmentController;
use Mockery as m;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ControllersTest extends TestCase
{
    public function test_upload_method()
    {
        $content = $this->uploadFakeFiles()->content();

        $attachments = Attachment::get();

        $this->assertJson($content);
        $this->assertCount(3, $attachments);

        foreach ($attachments as $attachment) {
            $this->assertFileExists($attachment->fullPath);
        }
    }

    public function test_upload_method_with_invalid_files()
    {
        $m = m::mock(UploadedFile::fake()->image(sprintf('%s.png', str_random(6))));

        $m->shouldReceive('isValid')->andReturnFalse();

        $files['ja_file'][] = $m;

        $content = (new AttachmentController)->upload(Request::create('/', 'post', [], [], $files))->content();

        $result = json_decode($content, true);

        $this->assertEmpty($result);
    }

    public function test_download_method()
    {
        [$img1, $img2] = json_decode($this->uploadFakeFiles(2)->content(), true);

        $request1 = Request::create('/', 'get');

        $response1 = (new AttachmentController)->download($request1, $img1['filename']);

        $this->assertStringStartsWith('inline', $response1->headers->get('content-disposition'));

        $request2 = Request::create('/', 'get', ['d' => '1']);

        $response2 = (new AttachmentController)->download($request2, $img2['filename']);

        $this->assertStringStartsWith('attachment', $response2->headers->get('content-disposition'));
    }

    public function test_trash_method()
    {
        [$img1] = json_decode($this->uploadFakeFiles(1)->content(), true);

        $request1 = Request::create('/', 'delete', ['token' => $img1['trash_token']]);

        $response1 = (new AttachmentController)->trash($request1, $img1['filename']);

        $result = json_decode($response1->content(), true);

        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);
    }

    public function test_trash_method_with_not_exists_filename()
    {
        $this->expectException(NotFoundHttpException::class);

        $request = Request::create('/', 'delete');

        (new AttachmentController)->trash($request, 'apple.png');
    }

    public function test_trash_method_with_invalid_trash_token()
    {
        [$img1] = json_decode($this->uploadFakeFiles(1)->content(), true);

        $this->expectException(HttpException::class);

        $request1 = Request::create('/', 'delete', ['token' => 'apple']);

        (new AttachmentController)->trash($request1, $img1['filename']);
    }

    public function test_trash_method_with_auth_enabled_1()
    {
        $this->app['config']->set('auth', true);

        [$img1] = json_decode($this->uploadFakeFiles(1)->content(), true);

        $this->expectException(HttpException::class);

        $request1 = m::mock(Request::create('/', 'delete', ['token' => $img1['trash_token']]));

        $request1->shouldReceive('user')->andReturnNull();

        (new AttachmentController)->trash($request1, $img1['filename']);
    }

    public function test_trash_method_with_auth_enabled_2()
    {
        $this->app['config']->set('auth', true);

        [$img1] = json_decode($this->uploadFakeFiles(1)->content(), true);

        $this->expectException(HttpException::class);

        $request1 = m::mock(Request::create('/', 'delete', ['token' => $img1['trash_token']]));

        $request1->shouldReceive('user')->andReturn(new class {
            public function getKey() { return 5; }
        });

        (new AttachmentController)->trash($request1, $img1['filename']);
    }

    /**
     * Upload fake files using AttachmentController upload method.
     *
     * @param int $count
     *
     * @return JsonResponse
     */
    protected function uploadFakeFiles($count = 3)
    {
        $files['ja_file'] = [];

        while ($count--) {
            $filename = sprintf('%s.png', str_random(6));

            $files['ja_file'][] = UploadedFile::fake()->image($filename);
        }

        return (new AttachmentController)->upload(Request::create('/', 'post', [], [], $files));
    }
}
