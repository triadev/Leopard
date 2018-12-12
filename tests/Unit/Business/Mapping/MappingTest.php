<?php
namespace Tests\Unit\Business\Mapping;

use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;
use Triadev\Es\ODM\Business\Mapping\Mapping;
use Triadev\Es\ODM\Searchable;

class MappingTest extends TestCase
{
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_exception_if_model_not_searchable()
    {
        new class extends Mapping {
            public function getMappedEloquentModel(): Model
            {
                return new class extends Model {};
            }
            
            public function map() {}
        };
    }
    
    /**
     * @test
     */
    public function it_builds_mapping_with_individual_index_and_type()
    {
        $mapping = new class extends Mapping {
            public function getMappedEloquentModel(): Model
            {
                return new class extends Model {
                    use Searchable;
                    
                    public $documentIndex = 'index';
                    public $documentType = 'type';
                };
            }
            
            public function map() {}
        };
        
        $this->assertEquals('index', $mapping->getDocumentIndex());
        $this->assertEquals('type', $mapping->getDocumentType());
        
        $mapping->setDocumentIndex('INDEX');
        $mapping->setDocumentType('TYPE');
    
        $this->assertEquals('INDEX', $mapping->getDocumentIndex());
        $this->assertEquals('TYPE', $mapping->getDocumentType());
    }
    
    /**
     * @test
     */
    public function it_returns_default_index()
    {
        $mapping = new class extends Mapping {
            public function getMappedEloquentModel(): Model
            {
                return new class extends Model {
                    use Searchable;
                };
            }
        
            public function map() {}
        };
        
        $this->assertEquals('default_index', $mapping->getDocumentIndex());
    }
}
