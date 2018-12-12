<?php
namespace Tests\Integration\Business\Dsl;

use Tests\Integration\Model\Entity\TestModel;
use Tests\TestCase;
use Triadev\Es\ODM\Facade\EsManager;

class SearchTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->refreshElasticsearchMappings();
        
        EsManager::getEsClient()->indices()->putMapping([
            'index' => 'phpunit',
            'type' => 'test',
            'body' => [
                'properties' => [
                    'test' => [
                        'type' => 'keyword'
                    ]
                ]
            ]
        ]);
    }
    
    /**
     * @test
     */
    public function it_returns_an_elasticsearch_search_result_object()
    {
        $model = new TestModel();
        
        EsManager::indexStatement([
            'index' => $model->getDocumentIndex(),
            'type' => $model->getDocumentType(),
            'id' => 1,
            'body' => [
                'test' => 'phpunit'
            ]
        ]);
    
        EsManager::getEsClient()->indices()->refresh();
        
        $result = EsManager::search()
            ->model($model)
            ->term('test', 'phpunit')
            ->get();
        
        $this->assertIsInt($result->getTook());
        $this->assertIsBool($result->isTimedOut());
        $this->assertIsFloat($result->getMaxScore());
        
        $this->assertEquals(5, $result->getShards()['total']);
        $this->assertEquals(5, $result->getShards()['successful']);
        $this->assertEquals(1, $result->getTotalHits());
        
        $this->assertNotEmpty($result->getHits());
    }
    
    /**
     * @test
     */
    public function it_returns_an_elasticsearch_search_result_object_with_eloquent_models()
    {
        $model = new TestModel();
        
        EsManager::indexStatement([
            'index' => $model->getDocumentIndex(),
            'type' => $model->getDocumentType(),
            'id' => 1,
            'body' => [
                'test' => 'phpunit'
            ]
        ]);
        
        EsManager::getEsClient()->indices()->refresh();
        
        $result = EsManager::search()
            ->model($model)
            ->term('test', 'phpunit')
            ->get();
        
        $this->assertIsInt($result->getTook());
        $this->assertIsBool($result->isTimedOut());
        $this->assertIsFloat($result->getMaxScore());
        
        $this->assertEquals(5, $result->getShards()['total']);
        $this->assertEquals(5, $result->getShards()['successful']);
        $this->assertEquals(1, $result->getTotalHits());
        
        $this->assertNotEmpty($result->getHits());
        
        foreach ($result->getHits() as $hit) {
            $this->assertInstanceOf(
                TestModel::class,
                $hit
            );
            
            $this->assertTrue($hit->isDocument);
            $this->assertNotNull($hit->documentScore);
            $this->assertNull($hit->documentVersion);
        }
    }
}
