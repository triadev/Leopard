<?php
namespace Triadev\Leopard\Provider;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Triadev\Leopard\Business\Repository\ElasticsearchRepository;
use Triadev\Leopard\Business\Repository\MappingLogRepository;
use Triadev\Leopard\Console\Commands\Index\Sync;
use Triadev\Leopard\Console\Commands\Mapping\Make;
use Triadev\Leopard\Console\Commands\Mapping\Migrate;
use Triadev\Leopard\Console\Commands\Mapping\Rollback;
use Triadev\Leopard\Contract\ElasticsearchManagerContract;
use Triadev\Leopard\Contract\Repository\ElasticsearchRepositoryContract;
use Triadev\Leopard\Contract\Repository\MappingLogRepositoryContract;
use Triadev\Leopard\ElasticsearchManager;
use Triadev\Leopard\Facade\Leopard;
use Triadev\Es\Provider\ElasticsearchServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public $bindings = [
        ElasticsearchRepositoryContract::class => ElasticsearchRepository::class,
        MappingLogRepositoryContract::class => MappingLogRepository::class
    ];
    
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        ElasticsearchManagerContract::class => ElasticsearchManager::class,
    ];
    
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath(__DIR__ . '/../Config/config.php');
    
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('leopard.php'),
        ], 'config');
    
        $this->mergeConfigFrom($source, 'leopard');
    
        $this->loadMigrationsFrom(__DIR__ . '/../Resources/database/migrations');
    
        $this->publishes([
            __DIR__.'/Resources/database' => database_path(),
        ], 'database');
        
        $this->commands([
            Make::class,
            Migrate::class,
            Rollback::class,
            Sync::class
        ]);
    }
    
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(ElasticsearchServiceProvider::class);
        
        AliasLoader::getInstance()->alias('Leopard', Leopard::class);
    }
}
