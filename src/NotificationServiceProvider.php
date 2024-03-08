<?php

namespace IICN\Notification;

use IICN\Notification\Http\Middleware\AuthNotification;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'notification');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->runningInConsole();

        $this->publish();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/notification.php', 'notification'
        );

        app('router')->aliasMiddleware('auth.notification', AuthNotification::class);

        $this->app->bind('notificationResponse', function () {
            $responseClass = config('notification.response_class');
            if (app($responseClass) instanceof NotificationResponse) {
                return new $responseClass();
            }
        });
    }

    /**
     * publishes the service provider.
     */
    public function publish(): void
    {
        $this->publishes([
            __DIR__.'/../config/notification.php' => config_path('notification.php'),
        ]);

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/notification'),
        ]);

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'notification-migrations');
    }

    /**
     * runningInConsole the service provider.
     */
    public function runningInConsole(): void
    {
        if ($this->app->runningInConsole()) {
            // $this->commands([

            // ]);
        }
    }
}
