<?php
namespace Triadev\Leopard\Business\Mapping;

use Illuminate\Database\Eloquent\Model;
use Triadev\Leopard\Business\Helper\IsModelSearchable;
use Triadev\Leopard\Facade\Leopard;
use Triadev\Leopard\Searchable;

abstract class Mapping
{
    use IsModelSearchable;
    
    /** @var Model|Searchable */
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
        $modelClass = $this->model();
        $this->model = new $modelClass();
    
        $this->isModelSearchable($this->model);
    }
    
    /**
     * Get mapped eloquent model class
     *
     * @return string
     */
    abstract public function model() : string;
    
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
        if ($this->index) {
            return $this->index;
        }
        
        return $this->model->getDocumentIndex() ?: Leopard::getEsDefaultIndex();
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
