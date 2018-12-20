<?php
namespace Tests\Integration\Model;

use Tests\Integration\Model\Entity\TestRelationshipOneToManyModel;
use Tests\Integration\Model\Entity\TestRelationshipOneToOneModel;
use Tests\TestCase;
use Triadev\Leopard\Business\Mapping\Mapper;
use Triadev\Leopard\Facade\Leopard;
use Tests\Integration\Model\Entity\TestModel;

class ModelTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
    
        $this->deleteElasticsearchMappings();
    
        app()->make(Mapper::class)->run($this->getMappingsPath());
    
        $this->assertNull($this->getTestDocument());
    
        $modelFirst = new TestModel();
        $modelFirst->name = 'FIRST';
        $modelFirst->email = 'first@test.de';
        $modelFirst->saveOrFail();
    
        $this->assertEquals([
            'id' => 1,
            'name' => $modelFirst->name,
            'email' => $modelFirst->email,
            'oneToOneTitle' => null,
            'oneToManyTitle' => []
        ], array_get($this->getTestDocument(1), '_source'));
    
        $modelSecond = new TestModel();
        $modelSecond->name = 'SECOND';
        $modelSecond->email = 'second@test.de';
        $modelSecond->saveOrFail();
    
        $this->assertEquals([
            'id' => 2,
            'name' => $modelSecond->name,
            'email' => $modelSecond->email,
            'oneToOneTitle' => null,
            'oneToManyTitle' => []
        ], array_get($this->getTestDocument(2), '_source'));
    }
    
    private function getTestDocument(int $id = 1) : ?array
    {
        return Leopard::getStatement([
            'index' => 'phpunit',
            'type' => 'test',
            'id' => $id
        ]);
    }
    
    /**
     * @test
     */
    public function it_creates_an_elasticsearch_document_with_an_eloquent_model_and_one_to_one_relationship()
    {
        $modelRelationship = new TestRelationshipOneToOneModel();
        $modelRelationship->test_id = 1;
        $modelRelationship->title = 'TITLE_FIRST';
        $modelRelationship->saveOrFail();
    
        $this->assertEquals([
            'id' => 1,
            'name' => 'FIRST',
            'email' => 'first@test.de',
            'oneToOneTitle' => 'TITLE_FIRST',
            'oneToManyTitle' => []
        ], array_get($this->getTestDocument(1), '_source'));
    
        $modelRelationship = new TestRelationshipOneToOneModel();
        $modelRelationship->test_id = 2;
        $modelRelationship->title = 'TITLE_SECOND';
        $modelRelationship->saveOrFail();
    
        $this->assertEquals([
            'id' => 2,
            'name' => 'SECOND',
            'email' => 'second@test.de',
            'oneToOneTitle' => 'TITLE_SECOND',
            'oneToManyTitle' => []
        ], array_get($this->getTestDocument(2), '_source'));
    }
    
    /**
     * @test
     */
    public function it_creates_an_elasticsearch_document_with_an_eloquent_model_and_one_to_many_relationship()
    {
        $modelRelationshipFirst = new TestRelationshipOneToManyModel();
        $modelRelationshipFirst->test_model_id = 1;
        $modelRelationshipFirst->title = 'TITLE_FIRST';
        $modelRelationshipFirst->saveOrFail();
    
        $modelRelationshipSecond = new TestRelationshipOneToManyModel();
        $modelRelationshipSecond->test_model_id = 1;
        $modelRelationshipSecond->title = 'TITLE_SECOND';
        $modelRelationshipSecond->saveOrFail();
    
        $this->assertEquals([
            'id' => 1,
            'name' => 'FIRST',
            'email' => 'first@test.de',
            'oneToOneTitle' => null,
            'oneToManyTitle' => [
                'TITLE_FIRST',
                'TITLE_SECOND'
            ]
        ], array_get($this->getTestDocument(1), '_source'));
    
        $modelRelationshipFirst = new TestRelationshipOneToManyModel();
        $modelRelationshipFirst->test_model_id = 2;
        $modelRelationshipFirst->title = 'TITLE_FIRST';
        $modelRelationshipFirst->saveOrFail();
    
        $modelRelationshipSecond = new TestRelationshipOneToManyModel();
        $modelRelationshipSecond->test_model_id = 2;
        $modelRelationshipSecond->title = 'TITLE_SECOND';
        $modelRelationshipSecond->saveOrFail();
    
        $this->assertEquals([
            'id' => 2,
            'name' => 'SECOND',
            'email' => 'second@test.de',
            'oneToOneTitle' => null,
            'oneToManyTitle' => [
                'TITLE_FIRST',
                'TITLE_SECOND'
            ]
        ], array_get($this->getTestDocument(2), '_source'));
    }
}
