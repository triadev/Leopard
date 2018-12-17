<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Integration\Model\Entity\TestModel;
use Triadev\Es\ODM\Facade\EsManager;
use Triadev\Es\ODM\Provider\ServiceProvider;
use Triadev\Es\Provider\ElasticsearchServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;
    
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->loadMigrationsFrom($this->getMigrationsPath());
    }
    
    /**
     * @inheritDoc
     *
     * (Increase visibility to public)
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }
    
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        app()->setBasePath(__DIR__ . DIRECTORY_SEPARATOR . 'Resources');
        
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        
        $app['config']->set('triadev-elasticsearch-odm.sync', [
            'chunkSize' => 1000,
            'models' => [
                'phpunit' => [
                    TestModel::class
                ]
            ]
        ]);
    }

    /**
     * Get package providers.  At a minimum this is the package being tested, but also
     * would include packages upon which our package depends, e.g. Cartalyst/Sentry
     * In a normal app environment these would be added to the 'providers' array in
     * the config/app.php file.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
            ElasticsearchServiceProvider::class
        ];
    }
    
    /**
     * Refresh elasticsearch mappings
     */
    public function refreshElasticsearchMappings()
    {
        $model = new TestModel();
        
        if (EsManager::getEsClient()->indices()->exists(['index' => $model->getDocumentIndex()])) {
            EsManager::getEsClient()->indices()->delete(['index' => $model->getDocumentIndex()]);
        }
    
        EsManager::getEsClient()->indices()->create([
            'index' => $model->getDocumentIndex()
        ]);
    }
    
    /**
     * Get mappings path
     *
     * @return string
     */
    public function getMappingsPath() : string
    {
        return app()->databasePath() . DIRECTORY_SEPARATOR . 'mappings';
    }
    
    /**
     * Get migrations path
     *
     * @return string
     */
    public function getMigrationsPath() : string
    {
        return app()->databasePath() . DIRECTORY_SEPARATOR . 'migrations';
    }
    
    /**
     * @param string|null $index
     * @return array
     */
    public function getEsMapping(?string $index = null) : array
    {
        return EsManager::getEsClient()->indices()->getMapping(['index' => $index ?: 'phpunit']);
    }
}
