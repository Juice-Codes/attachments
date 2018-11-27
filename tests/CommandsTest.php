<?php

namespace Juice\Tests;

class CommandsTest extends TestCase
{
    public function test_cleanup_trash_command()
    {
        $this->artisan('attachment:trash:cleanup')
            ->expectsQuestion('This command will remove all trashed attachments and can not be undo, do you really wish to run this command?', '')
            ->assertExitCode(0);

        $this->artisan('attachment:trash:cleanup')
            ->expectsQuestion('This command will remove all trashed attachments and can not be undo, do you really wish to run this command?', 'yes')
            ->expectsOutput('There is something wrong when cleanup attachments trash directory.')
            ->assertExitCode(0);

        $path = storage_path('ja-attachments/.trash/apple/banana/car');

        mkdir($path, 0777, true);

        $this->artisan('attachment:trash:cleanup')
            ->expectsQuestion('This command will remove all trashed attachments and can not be undo, do you really wish to run this command?', 'yes')
            ->expectsOutput('Attachments trash directory cleanup successfully.')
            ->assertExitCode(0);

        $this->assertDirectoryNotExists($path);
    }

    public function test_setup_command()
    {
        $directory = storage_path('ja-attachments');

        $this->assertDirectoryNotExists($directory);

        $this->artisan('attachment:setup')
            ->expectsOutput('Setup attachment package successfully.')
            ->assertExitCode(0);

        $this->assertDirectoryExists($directory);
        $this->assertFileExists(storage_path('ja-attachments/.gitignore'));

        $this->artisan('attachment:setup')
            ->expectsOutput('Setup attachment package successfully.')
            ->assertExitCode(0);
    }
}
