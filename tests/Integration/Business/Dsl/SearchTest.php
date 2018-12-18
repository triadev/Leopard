<?php
namespace Tests\Integration\Business\Dsl;

use Tests\Integration\Model\Entity\TestModel;
use Tests\TestCase;
use Triadev\Leopard\Facade\Leopard;

class SearchTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->refreshElasticsearchMappings();
    
        Leopard::getEsClient()->indices()->putMapping([
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
    
    private function buildPayload(array $payload = []) : array
    {
        return array_merge([
            'index' => $this->testModel->getDocumentIndex(),
            'type' => $this->testModel->getDocumentType()
        ], $payload);
    }
    
    private function createTestDocument()
    {
        Leopard::indexStatement($this->buildPayload([
            'id' => 1,
            'body' => [
                'test' => 'phpunit'
            ]
        ]));
    
        Leopard::getEsClient()->indices()->refresh();
    }
    
    /**
     * @test
     */
    public function it_returns_an_elasticsearch_search_result_object_with_array_documents()
    {
        $this->createTestDocument();
        
        $result = Leopard::search()
            ->overwriteIndex($this->testModel->getDocumentIndex())
            ->overwriteType($this->testModel->getDocumentType())
            ->termLevel()
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
            $this->assertTrue(is_array($hit));
        }
    }
    
    /**
     * @test
     */
    public function it_returns_an_elasticsearch_search_result_object_with_eloquent_models()
    {
        $this->createTestDocument();
        
        $result = Leopard::search()
            ->model($this->testModel)
            ->termLevel()
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
