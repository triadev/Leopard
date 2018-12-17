<?php
namespace Tests\Integration;

use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;
use Triadev\Leopard\Searchable;

class SearchableTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_the_table_name_as_document_type()
    {
        $searchableModel = new class extends Model {
            use Searchable;
    
            /**
             * The table associated with the model.
             *
             * @var string
             */
            protected $table = 'TABLE';
        };
        
        $this->assertEquals('TABLE', $searchableModel->getDocumentType());
    }
    
    /**
     * @test
     */
    public function it_checks_if_a_document_should_sync()
    {
        $searchableModel = new class extends Model {
            use Searchable;
        };
        
        $this->assertTrue($searchableModel->shouldSyncDocument());
    
        $searchableModel = new class extends Model {
            use Searchable;
            
            public $syncDocument = false;
        };
    
        $this->assertFalse($searchableModel->shouldSyncDocument());
    }
    
    /**
     * @test
     */
    public function it_gets_the_document_data_by_searchable_property()
    {
        $searchableModel = new class extends Model {
            use Searchable;
            
            public $searchable = ['id', 'name'];
        };
        
        $searchableModel->id = 1;
        $searchableModel->name = 'name';
        
        $this->assertEquals([
            'id' => 1,
            'name' => 'name'
        ], $searchableModel->getDocumentData());
    }
    
    /**
     * @test
     */
    public function it_gets_the_document_data_by_document_data_function()
    {
        $searchableModel = new class extends Model {
            use Searchable;
            
            public function getDocumentData(): array
            {
                return [
                    'id' => $this->id,
                    'name' => $this->name
                ];
            }
        };
        
        $searchableModel->id = 1;
        $searchableModel->name = 'name';
        
        $this->assertEquals([
            'id' => 1,
            'name' => 'name'
        ], $searchableModel->getDocumentData());
    }
    
    /**
     * @test
     */
    public function it_gets_the_document_data_by_model_attributes()
    {
        $searchableModel = new class extends Model {
            use Searchable;
        };
        
        $searchableModel->id = 1;
        $searchableModel->name = 'name';
        
        $this->assertEquals([
            'id' => 1,
            'name' => 'name'
        ], $searchableModel->getDocumentData());
    }
}
