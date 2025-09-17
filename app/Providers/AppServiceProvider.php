<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Automatically create storage link if it doesn't exist
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (!file_exists($link)) {
            if (file_exists($target)) {
                symlink($target, $link);
            }
        }

        // Ensure storage directories exist
        $directories = [
            storage_path('app/public'),
            storage_path('app/public/property_images'),
            storage_path('app/public/profile_images'),
            storage_path('app/public/room_images'),
        ];

        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
        }
    }
}
