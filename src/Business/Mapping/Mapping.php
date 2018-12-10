<?php
namespace Triadev\Es\ODM\Business\Mapping;

use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;
use Triadev\Es\ODM\Searchable;

abstract class Mapping
{
    /** @var Searchable */
    protected $model;
    
    /** @var string */
    protected $index;
    
    /** @var string */
    protected $type;
    
    /**
     * Mapping constructor.
     */
    public function __construct()
    {
        $this->model = $this->getMappedEloquentModel();
    
        $traits = class_uses_recursive(get_class($this->model));
    
        if (!isset($traits[Searchable::class])) {
            throw new InvalidArgumentException(get_class($this->model).' does not use the searchable trait.');
        }
    }
    
    /**
     * Get mapped eloquent model
     *
     * @return Model
     */
    abstract public function getMappedEloquentModel() : Model;
    
    /**
     * Build a mapping
     */
    abstract public function buildMapping();
    
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