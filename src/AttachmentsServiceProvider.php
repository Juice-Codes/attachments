<?php

namespace Juice\Attachments;

use Hashids\Hashids;
use Illuminate\Support\ServiceProvider;
use Juice\Attachments\Commands\CleanupTrashCommand;
use Juice\Attachments\Commands\SetupCommand;

class AttachmentsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app instanceof \Laravel\Lumen\Application) {
            $this->app->configure('juice-attachments');
        } else {
            if ($this->app->runningInConsole()) {
                $this->publishes([
                    __DIR__.'/../config/juice-attachments.php' => config_path('juice-attachments.php'),
                ], 'config');
            }
        }

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../migrations');

            $this->commands([
                CleanupTrashCommand::class,
                SetupCommand::class,
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/juice-attachments.php', 'juice-attachments'
        );

        $this->app->singleton('ja-hashids', function () {
            return new Hashids(config('juice-attachments.hashids-salt', ''), 5);
        });
    }
}
