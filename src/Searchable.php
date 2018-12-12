<?php
namespace Triadev\Es\ODM;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Triadev\Es\ODM\Contract\ElasticsearchManagerContract;
use Triadev\Es\ODM\Contract\Repository\ElasticsearchRepositoryContract;
use Triadev\Es\ODM\Facade\EsManager;

/**
 * Trait Searchable
 * @package Triadev\Es\ODM
 *
 * @property string $documentIndex
 * @property string $documentType
 * @property bool $syncDocument
 * @property array $searchable
 *
 * @method static ElasticsearchManagerContract search()
 * @methdo static ElasticsearchManagerContract suggest()
 * @method array buildDocument()
 * @method array toArray()
 * @method string getTable()
 */
trait Searchable
{
    /** @var bool */
    public $isDocument = false;
    
    /** @var float|null */
    public $documentScore;
    
    /** @var int|null */
    public $documentVersion;
    
    /**
     * Searchable boot model.
     */
    public static function bootSearchable()
    {
        static::saved(function (Model $model) {
            /** @var Model|Searchable $model */
            if ($model->shouldSyncDocument()) {
                $model->document()->save($model);
            }
        });
    
        static::deleted(function (Model $model) {
            /** @var Model|Searchable $model */
            if ($model->shouldSyncDocument()) {
                $model->document()->delete($model);
            }
        });
    }
    
    /**
     * Document
     *
     * @return ElasticsearchRepositoryContract
     */
    public function document() : ElasticsearchRepositoryContract
    {
        return app(ElasticsearchRepositoryContract::class);
    }
    
    /**
     * Get document index
     *
     * @return string|null
     */
    public function getDocumentIndex(): ?string
    {
        if (property_exists($this, 'documentIndex')) {
            return $this->documentIndex;
        }
        
        return null;
    }
    
    /**
     * Get document type
     *
     * @return string
     */
    public function getDocumentType(): string
    {
        if (property_exists($this, 'documentType')) {
            return $this->documentType;
        }
        
        return $this->getTable();
    }
    
    private function shouldSyncDocument() : bool
    {
        if (property_exists($this, 'syncDocument')) {
            return (bool)$this->syncDocument;
        }
        
        return true;
    }
    
    /**
     * Get document data
     *
     * @return array
     */
    public function getDocumentData() : array
    {
        if (method_exists($this, 'buildDocument')) {
            return $this->buildDocument();
        }
        
        if (property_exists($this, 'searchable') && is_array($this->searchable)) {
            return $this->buildDocumentFromArray($this->searchable);
        }
        
        return $this->toArray();
    }
    
    private function buildDocumentFromArray(array $searchable)
    {
        $document = [];
        
        foreach ($searchable as $value) {
            $result = $this->$value;
            
            if ($result instanceof Collection) {
                $result = $result->toArray();
            } elseif ($result instanceof Carbon) {
                $result = $result->toDateTimeString();
            } else {
                $result = $this->$value;
            }
            
            $document[$value] = $result;
        }
        
        return $document;
    }
    
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if ($name == 'search') {
            return EsManager::search()->model($this);
        }
        
        if ($name = 'suggest') {
            return EsManager::suggest();
        }
        
        return parent::__call($name, $arguments);
    }
}
