<?php
namespace Triadev\Es\ODM\Business\Mapping;

use Illuminate\Database\Eloquent\Model;
use Triadev\Es\ODM\Business\Helper\IsModelSearchable;
use Triadev\Es\ODM\Searchable;

abstract class Mapping
{
    use IsModelSearchable;
    
    /** @var Searchable */
    protected $model;
    
    /** @var string */
    protected $index;
    
    /** @var string */
    protected $type;
    
    /**
     * Mapping constructor.
     *
     * @throws \InvalidArgumentException
     */
    public function __construct()
    {
        $this->model = $this->getMappedEloquentModel();
    
        $this->isModelSearchable($this->model);
    }
    
    /**
     * Get mapped eloquent model
     *
     * @return Model
     */
    abstract public function getMappedEloquentModel() : Model;
    
    /**
     * Map
     */
    abstract public function map();
    
    /**
     * @param string $index
     */
    public function setDocumentIndex(string $index): void
    {
        $this->index = $index;
    }
    
    /**
     * @param string $type
     */
    public function setDocumentType(string $type): void
    {
        $this->type = $type;
    }
    
    /**
     * Get es index
     *
     * @return string
     */
    public function getDocumentIndex() : string
    {
        return $this->index ?: $this->model->getDocumentIndex();
    }
    
    /**
     * Get es type
     *
     * @return string
     */
    public function getDocumentType() : string
    {
        return $this->type ?: $this->model->getDocumentType();
    }
}
