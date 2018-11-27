<?php

namespace Juice\Tests;

use Juice\Attachments\Attachment;

class EloquentModelsTest extends TestCase
{
    public function test_find_by_filename_method()
    {
        // filename without extension
        $this->assertNull((new Attachment)->findByFilename('apple'));

        // incorrect filename (can not be decoded)
        $this->assertNull((new Attachment)->findByFilename('apple.pdf'));

        // not exist attachment
        $filename1 = sprintf('%s.pdf', app('ja-hashids')->encode(5));

        $this->assertNull((new Attachment)->findByFilename($filename1));

        // exists but filename not exactly match
        Attachment::create([
            'id' => 3,
            'name' => 'apple.txt',
            'mime' => 'text/plain',
            'size' => 65535,
            'trash_token' => 'abcdefgh',
        ]);

        $filename2 = sprintf('%s.tx', app('ja-hashids')->encode(3));

        $this->assertNull((new Attachment)->findByFilename($filename2));

        Attachment::create([
            'id' => 5,
            'name' => 'apple.txt',
            'mime' => 'text/plain',
            'size' => 1024,
            'trash_token' => 'abcdefgh',
        ]);

        $filename3 = sprintf('%s.txt', app('ja-hashids')->encode(5));

        $attachment = (new Attachment)->findByFilename($filename3);

        $this->assertNotNull($attachment);
        $this->assertEquals(1024, $attachment->size);
    }

    public function test_attachment_custom_attribute()
    {
        $attachment = Attachment::create([
            'id' => 5,
            'name' => 'apple.txt',
            'mime' => 'text/plain',
            'size' => 65535,
            'trash_token' => 'abcdefgh',
        ]);

        $identify = app('ja-hashids')->encode(5);

        $this->assertSame($identify, $attachment->identify);

        $filename = sprintf('%s.txt', $identify);

        $this->assertSame($filename, $attachment->filename);

        // md5(5) with str_split 4 length
        $path = storage_path('ja-attachments/e4da/3b7f/bbce/2345/d777/2b06/74a3/18d5');

        $this->assertSame($path, $attachment->path);

        $this->assertDirectoryExists($path);

        $this->assertSame(sprintf('%s/%s', $path, $filename), $attachment->fullPath);
    }
}
