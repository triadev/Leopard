<?php
namespace Tests\Integration\Business\Repository;

use Illuminate\Database\Eloquent\Model;
use Tests\Integration\Model\Entity\TestModel;
use Tests\TestCase;
use Triadev\Es\ODM\Business\Mapping\Mapper;
use Triadev\Es\ODM\Contract\Repository\ElasticsearchRepositoryContract;
use Triadev\Es\ODM\Facade\EsManager;
use Triadev\Es\ODM\Searchable;

class ElasticsearchRepositoryTest extends TestCase
{
    /** @var ElasticsearchRepositoryContract */
    private $repository;
    
    /** @var Model|Searchable */
    private $model;
    
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->repository = app(ElasticsearchRepositoryContract::class);
        
        $this->model = new TestModel();
        $this->model->id = 1;
        $this->model->name = 'PHPUNIT';
        $this->model->email = 'test@test.de';
        
        $this->refreshElasticsearchMappings();
    
        app()->make(Mapper::class)->run(__DIR__. '/../../../Resources/Database/mappings');
    }
    
    private function getSearchParamsFromModel() : array
    {
        return [
            'index' => $this->model->getDocumentIndex(),
            'type' => $this->model->getDocumentType(),
            'id' => 1
        ];
    }
    
    /**
     * @test
     */
    public function it_creates_a_document()
    {
        $this->assertNull(EsManager::getStatement($this->getSearchParamsFromModel()));
        
        $result = $this->repository->save($this->model);
        
        $this->assertEquals('created', array_get($result, 'result'));
        $this->assertEquals('phpunit', array_get($result, '_index'));
        $this->assertEquals('test', array_get($result, '_type'));
        $this->assertEquals('1', array_get($result, '_id'));
    
        $this->assertNotNull(EsManager::getStatement($this->getSearchParamsFromModel()));
    }
    
    /**
     * @test
     */
    public function it_updates_a_document()
    {
        $this->assertNull(EsManager::getStatement($this->getSearchParamsFromModel()));
        
        $result = $this->repository->save($this->model);
        
        $this->assertEquals('created', array_get($result, 'result'));
        $this->assertEquals('phpunit', array_get($result, '_index'));
        $this->assertEquals('test', array_get($result, '_type'));
        $this->assertEquals('1', array_get($result, '_id'));
        
        $this->assertEquals(
            'PHPUNIT',
            array_get(
                EsManager::getStatement($this->getSearchParamsFromModel()),
                '_source.name'
            )
        );
        
        $updateModel = clone $this->model;
        $updateModel->name = 'UPDATE';
    
        $result = $this->repository->update($updateModel);
    
        $this->assertEquals('updated', array_get($result, 'result'));
        $this->assertEquals('phpunit', array_get($result, '_index'));
        $this->assertEquals('test', array_get($result, '_type'));
        $this->assertEquals('1', array_get($result, '_id'));
    
        $this->assertEquals(
            'UPDATE',
            array_get(
                EsManager::getStatement($this->getSearchParamsFromModel()),
                '_source.name'
            )
        );
    }
    
    /**
     * @test
     */
    public function it_deletes_a_document()
    {
        $this->repository->save($this->model);
    
        $this->assertNotNull(EsManager::getStatement($this->getSearchParamsFromModel()));
        
        $this->repository->delete($this->model);
    
        $this->assertNull(EsManager::getStatement($this->getSearchParamsFromModel()));
    }
    
    /**
     * @test
     */
    public function it_creates_a_document_via_bulk()
    {
        $this->assertNull(EsManager::getStatement($this->getSearchParamsFromModel()));
        
        $this->repository->bulkSave([$this->model]);
        
        $this->assertNotNull(EsManager::getStatement($this->getSearchParamsFromModel()));
    }
    
    /**
     * @test
     */
    public function it_deletes_a_document_via_bulk()
    {
        $this->repository->save($this->model);
        
        $this->assertNotNull(EsManager::getStatement($this->getSearchParamsFromModel()));
        
        $this->repository->bulkDelete([$this->model]);
        
        $this->assertNull(EsManager::getStatement($this->getSearchParamsFromModel()));
    }
}
