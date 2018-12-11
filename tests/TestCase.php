<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Triadev\Es\ODM\Facade\EsManager;
use Triadev\Es\ODM\Provider\ServiceProvider;
use Triadev\Es\Provider\ElasticsearchServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;
    
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
    
        $this->loadMigrationsFrom(__DIR__ . '/Resources/Database/migrations');
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
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
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
        if (EsManager::getEsClient()->indices()->exists(['index' => 'phpunit'])) {
            EsManager::getEsClient()->indices()->delete(['index' => 'phpunit']);
        }
    
        EsManager::getEsClient()->indices()->create([
            'index' => 'phpunit'
        ]);
    }
}
