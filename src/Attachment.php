<?php

namespace Juice\Attachments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Mimey\MimeTypes;

class Attachment extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ja_attachments';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Find attachment by filename.
     *
     * @param string $filename
     *
     * @return Attachment|null
     */
    public function findByFilename(string $filename): ?self
    {
        $encoded = strstr($filename, '.', true);

        if ($encoded === false) {
            return null;
        }

        $model = $this->find(Arr::last(app('ja-hashids')->decode($encoded)));

        if (is_null($model)) {
            return null;
        } else if ($model->filename !== $filename) {
            return null;
        }

        return $model;
    }

    /**
     * Get attachment filename.
     *
     * @return string
     */
    public function getFilenameAttribute(): string
    {
        $mimes = new MimeTypes;

        $extension = $mimes->getExtension($this->mime);

        return sprintf('%s.%s', $this->identify, $extension);
    }

    /**
     * Get attachment encode identify.
     *
     * @return string
     */
    public function getIdentifyAttribute(): string
    {
        return app('ja-hashids')->encode($this->getKey());
    }

    /**
     * Get attachment path without filename.
     *
     * @return string
     */
    public function getPathAttribute(): string
    {
        $chunk = implode('/', str_split(md5($this->getKey()), 4));

        $path = storage_path(sprintf(
            'ja-attachments/%s%s',
            $this->trashed() ? '.trash/' : '',
            $chunk
        ));

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    /**
     * Get full path of the attachment.
     *
     * @return string
     */
    public function getFullPathAttribute(): string
    {
        return sprintf('%s/%s', $this->path, $this->filename);
    }
}
