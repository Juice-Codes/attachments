<?php

namespace Juice\Attachments\Commands;

use Illuminate\Console\Command;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attachment:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup attachment package, run this command after install or upgrade.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $path = storage_path('ja-attachments');

        if (!is_dir($path)) {
            mkdir($path);
        }

        $path = storage_path('ja-attachments/.gitignore');

        if (!is_file($path)) {
            file_put_contents(
                $path,
                sprintf('*%s!.gitignore%s', PHP_EOL, PHP_EOL)
            );
        }

        $this->info('Setup attachment package successfully.');
    }
}
