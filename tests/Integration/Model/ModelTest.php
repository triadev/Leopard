<?php
namespace Tests\Integration\Model;

use Tests\TestCase;
use Triadev\Es\ODM\Business\Mapping\Mapper;
use Triadev\Es\ODM\Facade\EsManager;
use Tests\Integration\Model\Entity\TestModel;

class ModelTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
    
        $this->refreshElasticsearchMappings();
    
        app()->make(Mapper::class)->run($this->getMappingsPath());
    }
    
    private function getTestDocument() : ?array
    {
        return EsManager::getStatement([
            'index' => 'phpunit',
            'type' => 'test',
            'id' => 1
        ]);
    }
    
    /**
     * @test
     */
    public function it_creates_an_elasticsearch_document_with_an_eloquent_model()
    {
        $this->assertNull($this->getTestDocument());
        
        $model = new TestModel();
        $model->name = 'PHPUNIT';
        $model->email = 'test@test.de';
        $model->saveOrFail();
        
        $this->assertEquals([
            'id' => 1,
            'name' => $model->name,
            'email' => $model->email
        ], array_get($this->getTestDocument(), '_source'));
    }
}
