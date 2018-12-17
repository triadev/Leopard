<?php
namespace Triadev\Leopard\Business\Dsl;

use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Search as OngrSearch;
use Triadev\Leopard\Busines\Dsl\Query\Specialized;
use Triadev\Leopard\Business\Dsl\Query\TermLevel;
use Triadev\Leopard\Business\Dsl\Query\Fulltext;
use Triadev\Leopard\Business\Dsl\Query\Geo;
use Triadev\Leopard\Business\Dsl\Query\Joining;
use Triadev\Leopard\Business\Dsl\Query\InnerHit;
use Triadev\Leopard\Business\Dsl\Search as SearchDsl;

abstract class AbstractQuery
{
    /** @var OngrSearch */
    public $search;
    
    /** @var string */
    public $boolState = BoolQuery::MUST;
    
    /**
     * BoolQuery constructor.
     * @param OngrSearch|null $search
     */
    public function __construct(?OngrSearch $search = null)
    {
        $this->search = $search ?: new OngrSearch();
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
}
