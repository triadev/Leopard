<?php
namespace Triadev\Leopard\Business\Dsl;

use Illuminate\Database\Eloquent\Model;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Triadev\Leopard\Busines\Dsl\Query\Specialized;
use Triadev\Leopard\Business\Dsl\Query\Compound;
use Triadev\Leopard\Business\Dsl\Query\Fulltext;
use Triadev\Leopard\Business\Dsl\Query\Geo;
use Triadev\Leopard\Business\Dsl\Query\InnerHit;
use Triadev\Leopard\Business\Dsl\Query\Joining;
use Triadev\Leopard\Business\Filler\EloquentFiller;
use Triadev\Leopard\Business\Helper\IsModelSearchable;
use Triadev\Leopard\Contract\ElasticsearchManagerContract;
use Triadev\Leopard\Contract\FillerContract;
use Triadev\Leopard\Searchable;
use Triadev\Leopard\Model\SearchResult;
use Triadev\Leopard\Business\Dsl\Query\TermLevel;

class Search extends AbstractQuery
{
    use IsModelSearchable;
    
    /** @var string */
    private $_index;
    
    /** @var string */
    private $_type;
    
    /** @var Model */
    private $_model;
    
    /** @var ElasticsearchManagerContract */
    private $_manager;
    
    /**
     * Search constructor.
     * @param ElasticsearchManagerContract $manager
     * @param \ONGR\ElasticsearchDSL\Search|null $search
     */
    public function __construct(
        ElasticsearchManagerContract $manager,
        ?\ONGR\ElasticsearchDSL\Search $search = null
    ) {
        parent::__construct($search);
        
        $this->_manager = $manager;
        
        $this->_index = config('leopard.index');
    }
    
    /**
     * Overwrite default index
     *
     * @param string $index
     * @return Search
     */
    public function overwriteIndex(string $index) : Search
    {
        $this->_index = $index;
        return $this;
    }
    
    /**
     * Get index
     *
     * @return string
     */
    public function getIndex() : string
    {
        return $this->_index;
    }
    
    /**
     * Overwrite default type
     *
     * @param string $type
     * @return Search
     */
    public function overwriteType(string $type) : Search
    {
        $this->_type = $type;
        return $this;
    }
    
    /**
     * Get type
     *
     * @return string|null
     */
    public function getType() : ?string
    {
        return $this->_type;
    }
    
    /**
     * Add model
     *
     * @param Model|Searchable $model
     * @return Search
     *
     * @throws \InvalidArgumentException
     */
    public function model(Model $model) : Search
    {
        $this->isModelSearchable($model);
        
        $this->_model = $model;
        
        if (is_string($index = $model->getDocumentIndex())) {
            $this->overwriteIndex($index);
        }
    
        $this->overwriteType($model->getDocumentType());
        
        return $this;
    }
    
    /**
     * Get
     *
     * @param FillerContract|null $filler
     * @return SearchResult
     */
    public function get(?FillerContract $filler = null) : SearchResult
    {
        $searchResult = new SearchResult($this->getRaw());
        
        if ($this->_model) {
            $filler = $filler ?: new EloquentFiller();
            $filler->fill($this->_model, $searchResult);
        }
        
        return $searchResult;
    }
    
    /**
     * Get raw search result
     *
     * @return array
     */
    public function getRaw() : array
    {
        $params = [
            'index' => $this->_index,
            'body' => $this->toDsl()
        ];
    
        if ($this->_type) {
            $params['type'] = $this->_type;
        }
        
        return $this->_manager->searchStatement($params);
    }
    
    /**
     * Aggregation
     *
     * @param \Closure $aggregation
     * @return Search
     */
    public function aggregation(\Closure $aggregation) : Search
    {
        $aggregation(new Aggregation($this->search));
        return $this;
    }
    
    /**
     * Term level
     *
     * @param \Closure $termLevel
     * @return Search
     */
    public function termLevel(\Closure $termLevel) : Search
    {
        $termLevel(new TermLevel($this->search));
        return $this;
    }
    
    /**
     * Fulltext
     *
     * @param \Closure $fulltext
     * @return Search
     */
    public function fulltext(\Closure $fulltext) : Search
    {
        $fulltext(new Fulltext($this->search));
        return $this;
    }
    
    /**
     * Geo
     *
     * @param \Closure $geo
     * @return Search
     */
    public function geo(\Closure $geo) : Search
    {
        $geo(new Geo($this->search));
        return $this;
    }
    
    /**
     * Compound
     *
     * @param \Closure $compound
     * @return Search
     */
    public function compound(\Closure $compound) : Search
    {
        $compound(new Compound($this->search));
        return $this;
    }
    
    /**
     * Joining
     *
     * @param \Closure $joining
     * @return Search
     */
    public function joining(\Closure $joining) : Search
    {
        $joining(new Joining($this->search));
        return $this;
    }
    
    /**
     * Specialized
     *
     * @param \Closure $specialized
     * @return Search
     */
    public function specialized(\Closure $specialized) : Search
    {
        $specialized(new Specialized($this->search));
        return $this;
    }
    
    /**
     * Inner hit
     *
     * @param \Closure $innerHit
     * @return Search
     */
    public function innerHit(\Closure $innerHit) : Search
    {
        $innerHit(new InnerHit($this->search));
        return $this;
    }
    
    /**
     * Paginate
     *
     * @param int $page
     * @param int $limit
     * @return Search
     */
    public function paginate(int $page, int $limit = 25) : Search
    {
        $this->search->setFrom($limit * ($page - 1))->setSize($limit);
        return $this;
    }
    
    /**
     * Min score
     *
     * @param int $minScore
     * @return Search
     */
    public function minScore(int $minScore) : Search
    {
        $this->search->setMinScore($minScore);
        return $this;
    }
    
    /**
     * Sort
     *
     * @param string $field
     * @param string $order
     * @param array $params
     * @return Search
     */
    public function sort(string $field, string $order = FieldSort::DESC, array $params = []) : Search
    {
        $this->search->addSort(new FieldSort(
            $field,
            $order,
            $params
        ));
        
        return $this;
    }
}
