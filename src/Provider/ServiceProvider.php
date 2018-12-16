<?php
namespace Triadev\Es\ODM\Provider;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Triadev\Es\ODM\Business\Repository\ElasticsearchRepository;
use Triadev\Es\ODM\Business\Repository\MappingLogRepository;
use Triadev\Es\ODM\Console\Commands\Index\Sync;
use Triadev\Es\ODM\Console\Commands\Mapping\Make;
use Triadev\Es\ODM\Console\Commands\Mapping\Migrate;
use Triadev\Es\ODM\Console\Commands\Mapping\Rollback;
use Triadev\Es\ODM\Contract\ElasticsearchManagerContract;
use Triadev\Es\ODM\Contract\Repository\ElasticsearchRepositoryContract;
use Triadev\Es\ODM\Contract\Repository\MappingLogRepositoryContract;
use Triadev\Es\ODM\ElasticsearchManager;
use Triadev\Es\ODM\Facade\EsManager;
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
            __DIR__ . '/../Config/config.php' => config_path('triadev-elasticsearch-odm.php'),
        ], 'config');
    
        $this->mergeConfigFrom($source, 'triadev-elasticsearch-odm');
    
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
        
        AliasLoader::getInstance()->alias('EsManager', EsManager::class);
    }
}
