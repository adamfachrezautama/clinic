<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Xendit\XenditSdkException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        try {
            // Initialize Xendit SDK
            $xenditKey = env('XENDIT_SERVER_KEY', '');
            if ($xenditKey) {
                \Xendit\Configuration::setXenditKey($xenditKey);
            }
        } catch (XenditSdkException $e) {
            // Handle exception if needed
            Log::error('Xendit SDK initialization failed: ' . $e->getMessage());
        }
    }
}
