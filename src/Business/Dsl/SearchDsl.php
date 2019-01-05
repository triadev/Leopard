<?php
namespace Triadev\Leopard\Business\Dsl;

use ONGR\ElasticsearchDSL\Search as OngrSearch;
use Illuminate\Database\Eloquent\Model;
use Triadev\Es\Dsl\Facade\ElasticDsl;
use Triadev\Es\Dsl\Model\SearchResult;
use Triadev\Leopard\Business\Filler\EloquentFiller;
use Triadev\Leopard\Business\Helper\IsModelSearchable;
use Triadev\Leopard\Contract\FillerContract;
use Triadev\Leopard\Searchable;

class SearchDsl
{
    use IsModelSearchable;
    
    /** @var \Triadev\Es\Dsl\Dsl\Search */
    private $dsl;
    
    /** @var Model|null */
    public $model;
    
    /**
     * SearchDsl constructor.
     * @param OngrSearch|null $search
     * @param Model|null $model
     */
    public function __construct(?OngrSearch $search = null, ?Model $model = null)
    {
        $this->dsl = ElasticDsl::search($search);
        
        if ($model) {
            $this->setModel($model);
        }
    }
    
    /**
     * Add model
     *
     * @param Model|Searchable $model
     * @return SearchDsl|\Triadev\Es\Dsl\Dsl\Search
     *
     * @throws \InvalidArgumentException
     */
    public function model(Model $model) : SearchDsl
    {
        $this->setModel($model);
        return $this;
    }
    
    /**
     * @param Model|Searchable $model
     */
    private function setModel(Model $model)
    {
        $this->isModelSearchable($model);
    
        $this->model = $model;
    
        if (is_string($index = $model->getDocumentIndex())) {
            $this->dsl->esIndex($index);
        }
    
        $this->dsl->esType($model->getDocumentType());
    }
    
    /**
     * Get
     *
     * @param FillerContract|null $filler
     * @return SearchResult
     */
    public function get(?FillerContract $filler = null) : SearchResult
    {
        $searchResult = $this->dsl->get();
        
        if ($this->model) {
            $filler = $filler ?: new EloquentFiller();
            $filler->fill($this->model, $searchResult);
        }
        
        return $searchResult;
    }
    
    /**
     * Call methods in elasticsearch dsl
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $resultMethods = ['toDsl', 'getSearch', 'getQuery'];
        
        if (in_array($name, $resultMethods)) {
            return $this->dsl->$name();
        }
    
        $this->dsl = call_user_func_array([$this->dsl, $name], $arguments);
    
        return $this;
    }
}
