<?php
namespace Triadev\Es\ODM\Provider;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath(__DIR__ . '/../Config/config.php');
    
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('triadev-elasticsearch-odm.php'),
        ], 'config');
    
        $this->mergeConfigFrom($source, 'triadev-elasticsearch-odm');
    }
    
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
    
    }
}
