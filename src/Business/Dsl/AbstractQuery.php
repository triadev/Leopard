<?php
namespace Triadev\Leopard\Business\Dsl;

use Illuminate\Database\Eloquent\Model;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Search as OngrSearch;
use Triadev\Leopard\Busines\Dsl\Query\Specialized;
use Triadev\Leopard\Business\Dsl\Query\Compound;
use Triadev\Leopard\Business\Dsl\Query\TermLevel;
use Triadev\Leopard\Business\Dsl\Query\Fulltext;
use Triadev\Leopard\Business\Dsl\Query\Geo;
use Triadev\Leopard\Business\Dsl\Query\Joining;
use Triadev\Leopard\Business\Dsl\Query\InnerHit;
use Triadev\Leopard\Business\Dsl\Search as SearchDsl;
use Triadev\Leopard\Business\Filler\EloquentFiller;
use Triadev\Leopard\Business\Helper\IsModelSearchable;
use Triadev\Leopard\Contract\ElasticsearchManagerContract;
use Triadev\Leopard\Contract\FillerContract;
use Triadev\Leopard\Facade\Leopard;
use Triadev\Leopard\Model\SearchResult;
use Triadev\Leopard\Searchable;

/**
 * Class AbstractQuery
 * @package Triadev\Leopard\Business\Dsl
 *
 * @method TermLevel termLevel()
 * @method Fulltext fulltext()
 * @method Geo geo()
 * @method Compound compound()
 * @method Joining joining()
 * @method Specialized specialized()
 * @method InnerHit innerHit()
 */
abstract class AbstractQuery
{
    use IsModelSearchable;
    
    /** @var OngrSearch */
    public $search;
    
    /** @var string */
    public $boolState = BoolQuery::MUST;
    
    /** @var string|null */
    private $index;
    
    /** @var string|null */
    private $type;
    
    /** @var Model|null */
    public $model;
    
    /** @var ElasticsearchManagerContract */
    private $manager;
    
    /**
     * BoolQuery constructor.
     * @param OngrSearch|null $search
     * @param Model|null $model
     */
    public function __construct(?OngrSearch $search = null, ?Model $model = null)
    {
        $this->search = $search ?: new OngrSearch();
        $this->model = $model;
    
        $this->manager = app()->make(ElasticsearchManagerContract::class);
    }
    
    /**
     * Overwrite default index
     *
     * @param string $index
     * @return AbstractQuery|Search
     */
    public function overwriteIndex(string $index) : AbstractQuery
    {
        $this->index = $index;
        return $this;
    }
    
    /**
     * Get index
     *
     * @return string
     */
    public function getIndex() : string
    {
        return $this->index ?: config('leopard.index');
    }
    
    /**
     * Overwrite default type
     *
     * @param string $type
     * @return AbstractQuery|Search
     */
    public function overwriteType(string $type) : AbstractQuery
    {
        $this->type = $type;
        return $this;
    }
    
    /**
     * Get type
     *
     * @return string|null
     */
    public function getType() : ?string
    {
        return $this->type;
    }
    
    /**
     * Add model
     *
     * @param Model|Searchable $model
     * @return AbstractQuery|Search
     *
     * @throws \InvalidArgumentException
     */
    public function model(Model $model) : AbstractQuery
    {
        $this->isModelSearchable($model);
        
        $this->model = $model;
        
        if (is_string($index = $model->getDocumentIndex())) {
            $this->overwriteIndex($index);
        }
        
        $this->overwriteType($model->getDocumentType());
        
        return $this;
    }
    
    /**
     * To dsl
     *
     * @return array
     */
    public function toDsl() : array
    {
        return $this->search->toArray();
    }
    
    /**
     * Get search
     *
     * @return OngrSearch
     */
    public function getSearch() : OngrSearch
    {
        return $this->search;
    }
    
    /**
     * Get query
     *
     * @return BuilderInterface
     */
    public function getQuery() : BuilderInterface
    {
        return $this->search->getQueries();
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
        
        if ($this->model) {
            $filler = $filler ?: new EloquentFiller();
            $filler->fill($this->model, $searchResult);
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
            'index' => $this->index,
            'body' => $this->toDsl()
        ];
        
        if ($this->type) {
            $params['type'] = $this->type;
        }
        
        return $this->manager->searchStatement($params);
    }
    
    /**
     * Append
     *
     * @param BuilderInterface $query
     * @return AbstractQuery|TermLevel|Fulltext|Geo|SearchDsl|Joining|Specialized|InnerHit
     */
    public function append(BuilderInterface $query) : AbstractQuery
    {
        $this->search->addQuery($query, $this->boolState);
        return $this;
    }
    
    /**
     * Bool state: must
     *
     * @return AbstractQuery|TermLevel|Fulltext|Geo|SearchDsl|Joining|Specialized|InnerHit
     */
    public function must(): AbstractQuery
    {
        $this->boolState = BoolQuery::MUST;
        return $this;
    }
    
    /**
     * Bool state: must not
     *
     * @return AbstractQuery|TermLevel|Fulltext|Geo|SearchDsl|Joining|Specialized|InnerHit
     */
    public function mustNot(): AbstractQuery
    {
        $this->boolState = BoolQuery::MUST_NOT;
        return $this;
    }
    
    /**
     * Bool state: should
     *
     * @return AbstractQuery|TermLevel|Fulltext|Geo|SearchDsl|Joining|Specialized|InnerHit
     */
    public function should(): AbstractQuery
    {
        $this->boolState = BoolQuery::SHOULD;
        return $this;
    }
    
    /**
     * Bool state: filter
     *
     * @return AbstractQuery|TermLevel|Fulltext|Geo|SearchDsl|Joining|Specialized|InnerHit
     */
    public function filter(): AbstractQuery
    {
        $this->boolState = BoolQuery::FILTER;
        return $this;
    }
    
    /**
     * Search
     *
     * @param \Closure $search
     * @return AbstractQuery|TermLevel|Fulltext|Geo|Compound|Joining|Specialized|InnerHit
     */
    public function bool(\Closure $search) : AbstractQuery
    {
        $searchBuilder = new Search();
        $search($searchBuilder);
        
        $this->append($searchBuilder->getQuery());
        
        return $this;
    }
    
    /**
     * Call
     *
     * @param string $name
     * @param array $arguments
     *
     * @return AbstractQuery|null
     */
    public function __call(string $name, array $arguments)
    {
        $validFunctions = [
            'termLevel',
            'fulltext',
            'geo',
            'compound',
            'joining',
            'specialized',
            'innerHit'
        ];
        
        if (in_array($name, $validFunctions)) {
            return Leopard::search($this->search, $this->model)->$name();
        }
        
        return null;
    }
}
