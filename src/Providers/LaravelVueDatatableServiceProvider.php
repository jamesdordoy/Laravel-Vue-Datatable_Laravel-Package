<?php

namespace JamesDordoy\LaravelVueDatatable\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelVueDatatableServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../resources/assets' =>
            resource_path('js/packages/jamesdordoy/laravelvuedatatable'
        )], 'vue-components');
    }
}
