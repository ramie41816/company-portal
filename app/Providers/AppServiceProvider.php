<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use App\Services\PermissionRegistry;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        Blade::directive('money', function ($expression) {
            return "<?php echo config('company.currency_symbol', '₱') . number_format((float)($expression), 2); ?>";
        });
    }
}
