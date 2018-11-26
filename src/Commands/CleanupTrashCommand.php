<?php

namespace Juice\Attachments\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class CleanupTrashCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attachment:trash:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup trashed attachments.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $confirm = $this->confirm('This command will remove all trashed attachments and can not be undo, do you really wish to run this command?');

        if (!$confirm) {
            return;
        }

        $path = storage_path('ja-attachments/.trash');

        $result = (new Filesystem)->cleanDirectory($path);

        if (!$result) {
            $this->error('There is something wrong when cleanup attachments trash directory.');
        } else {
            $this->info('Attachments trash directory cleanup successfully.');
        }
    }
}
