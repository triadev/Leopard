<?php
namespace Triadev\Es\ODM\Business\Dsl;

use Illuminate\Database\Eloquent\Model;
use ONGR\ElasticsearchDSL\BuilderInterface;
use ONGR\ElasticsearchDSL\InnerHit\NestedInnerHit;
use ONGR\ElasticsearchDSL\InnerHit\ParentInnerHit;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\Compound\BoostingQuery;
use ONGR\ElasticsearchDSL\Query\Compound\ConstantScoreQuery;
use ONGR\ElasticsearchDSL\Query\Compound\DisMaxQuery;
use ONGR\ElasticsearchDSL\Query\FullText\CommonTermsQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchPhrasePrefixQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchPhraseQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\QueryStringQuery;
use ONGR\ElasticsearchDSL\Query\FullText\SimpleQueryStringQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoBoundingBoxQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoDistanceQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoPolygonQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoShapeQuery;
use ONGR\ElasticsearchDSL\Query\Joining\HasChildQuery;
use ONGR\ElasticsearchDSL\Query\Joining\HasParentQuery;
use ONGR\ElasticsearchDSL\Query\Joining\NestedQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Query\Specialized\MoreLikeThisQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\ExistsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\FuzzyQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\IdsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\PrefixQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\RegexpQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TypeQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\WildcardQuery;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Triadev\Es\ODM\Business\Dsl\Compound\FunctionScore;
use Triadev\Es\ODM\Contract\ElasticsearchManagerContract;
use Triadev\Es\ODM\Model\Location;
use Triadev\Es\ODM\Searchable;
use Triadev\Es\ODM\Model\SearchResult;

class Search
{
    /** @var \ONGR\ElasticsearchDSL\Search */
    private $search;
    
    /** @var string */
    private $boolState = BoolQuery::MUST;
    
    /** @var string */
    private $index;
    
    /** @var string */
    private $type;
    
    /** @var Model */
    private $model;
    
    /** @var ElasticsearchManagerContract */
    private $manager;
    
    /**
     * Search constructor.
     * @param ElasticsearchManagerContract $manager
     * @param \ONGR\ElasticsearchDSL\Search|null $search
     */
    public function __construct(
        ElasticsearchManagerContract $manager,
        ?\ONGR\ElasticsearchDSL\Search $search = null
    ) {
        $this->manager = $manager;
        $this->search = $search ?: new \ONGR\ElasticsearchDSL\Search();
        
        $this->index = config('triadev-elasticsearch-odm.index');
        $this->type = config('triadev-elasticsearch-odm.type');
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
     * @return \ONGR\ElasticsearchDSL\Search
     */
    public function getSearch() : \ONGR\ElasticsearchDSL\Search
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
     * Overwrite default index
     *
     * @param string $index
     * @return Search
     */
    public function overwriteIndex(string $index) : Search
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
        return $this->index;
    }
    
    /**
     * Overwrite default type
     *
     * @param string $type
     * @return Search
     */
    public function overwriteType(string $type) : Search
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
     * @return Search
     */
    public function model(Model $model) : Search
    {
        $traits = class_uses_recursive(get_class($model));
    
        if (!isset($traits[Searchable::class])) {
            throw new \InvalidArgumentException(get_class($model).' does not use the searchable trait.');
        }
        
        $this->model = $model;
        
        if (is_string($index = $model->getDocumentIndex())) {
            $this->overwriteIndex($index);
        }
    
        $this->overwriteType($model->getDocumentType());
        
        return $this;
    }
    
    /**
     * Get
     *
     * @return SearchResult
     */
    public function get() : SearchResult
    {
        return new SearchResult($this->getRaw());
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
     * @return Search
     */
    public function append(BuilderInterface $query) : Search
    {
        $this->search->addQuery($query, $this->boolState);
        return $this;
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
     * Bool state: must
     *
     * @return Search
     */
    public function must(): Search
    {
        $this->boolState = BoolQuery::MUST;
        return $this;
    }
    
    /**
     * Bool state: must not
     *
     * @return Search
     */
    public function mustNot(): Search
    {
        $this->boolState = BoolQuery::MUST_NOT;
        return $this;
    }
    
    /**
     * Bool state: should
     *
     * @return Search
     */
    public function should(): Search
    {
        $this->boolState = BoolQuery::SHOULD;
        return $this;
    }
    
    /**
     * Bool state: filter
     *
     * @return Search
     */
    public function filter(): Search
    {
        $this->boolState = BoolQuery::FILTER;
        return $this;
    }
    
    /**
     * Match all
     *
     * @return Search
     */
    public function matchAll() : Search
    {
        return $this->append(new MatchAllQuery());
    }
    
    /**
     * Exists
     *
     * @param string $field
     * @return Search
     */
    public function exists(string $field): Search
    {
        return $this->append(new ExistsQuery($field));
    }
    
    /**
     * Fuzzy
     *
     * @param string $field
     * @param string $value
     * @param array $params
     * @return Search
     */
    public function fuzzy(string $field, string $value, array $params = []): Search
    {
        return $this->append(new FuzzyQuery($field, $value, $params));
    }
    
    /**
     * Ids
     *
     * @param array $values
     * @param array $params
     * @return Search
     */
    public function ids(array $values, array $params = []): Search
    {
        return $this->append(new IdsQuery($values, $params));
    }
    
    /**
     * Prefix
     *
     * @param string $field
     * @param string $value
     * @param array $params
     * @return Search
     */
    public function prefix(string $field, string $value, array $params = []): Search
    {
        return $this->append(new PrefixQuery($field, $value, $params));
    }
    
    /**
     * Range
     *
     * @param string $field
     * @param array $params
     * @return Search
     */
    public function range(string $field, array $params = []): Search
    {
        return $this->append(new RangeQuery($field, $params));
    }
    
    /**
     * Regexp
     *
     * @param string $field
     * @param string $value
     * @param array $params
     * @return Search
     */
    public function regexp(string $field, string $value, array $params = []): Search
    {
        return $this->append(new RegexpQuery($field, $value, $params));
    }
    
    /**
     * Term
     *
     * @param string $field
     * @param string $value
     * @return Search
     */
    public function term(string $field, string $value): Search
    {
        return $this->append(new TermQuery($field, $value));
    }
    
    /**
     * Terms
     *
     * @param string $field
     * @param array $values
     * @param array $params
     * @return Search
     */
    public function terms(string $field, array $values, array $params = []): Search
    {
        return $this->append(new TermsQuery($field, $values, $params));
    }
    
    /**
     * Type
     *
     * @param string $type
     * @return Search
     */
    public function type(string $type) : Search
    {
        return $this->append(new TypeQuery($type));
    }
    
    /**
     * Wildcard
     *
     * @param string $field
     * @param string $value
     * @param array $params
     * @return Search
     */
    public function wildcard(string $field, string $value, array $params = []): Search
    {
        return $this->append(new WildcardQuery($field, $value, $params));
    }
    
    /**
     * Match
     *
     * @param string $field
     * @param string $query
     * @param array $params
     * @return Search
     */
    public function match(string $field, string $query, array $params = []) : Search
    {
        return $this->append(new MatchQuery($field, $query, $params));
    }
    
    /**
     * Match phrase
     *
     * @param string $field
     * @param string $query
     * @param array $params
     * @return Search
     */
    public function matchPhrase(string $field, string $query, array $params = []): Search
    {
        return $this->append(new MatchPhraseQuery($field, $query, $params));
    }
    
    /**
     * Match phrase prefix
     *
     * @param string $field
     * @param string $query
     * @param array $params
     * @return Search
     */
    public function matchPhrasePrefix(string $field, string $query, array $params = []): Search
    {
        return $this->append(new MatchPhrasePrefixQuery($field, $query, $params));
    }
    
    /**
     * Multi match
     *
     * @param array $fields
     * @param string $query
     * @param array $params
     * @return Search
     */
    public function multiMatch(array $fields, string $query, array $params = []): Search
    {
        return $this->append(new MultiMatchQuery($fields, $query, $params));
    }
    
    /**
     * Query string
     *
     * @param string $query
     * @param array $params
     * @return Search
     */
    public function queryString(string $query, array $params = []): Search
    {
        return $this->append(new QueryStringQuery($query, $params));
    }
    
    /**
     * Simple query string
     *
     * @param string $query
     * @param array $params
     * @return Search
     */
    public function simpleQueryString(string $query, array $params = []): Search
    {
        return $this->append(new SimpleQueryStringQuery($query, $params));
    }
    
    /**
     * Common terms
     *
     * @param string $field
     * @param string $query
     * @param array $params
     * @return Search
     */
    public function commonTerms(string $field, string $query, array $params = []): Search
    {
        return $this->append(new CommonTermsQuery($field, $query, $params));
    }
    
    /**
     * Geo bounding box
     *
     * @param string $field
     * @param Location[] $locations
     * @param array $params
     * @return Search
     */
    public function geoBoundingBox(string $field, array $locations, array $params = []): Search
    {
        $l = [];
        
        foreach ($locations as $location) {
            if ($location instanceof Location) {
                $l[] = [
                    'lat' => $location->getLatitude(),
                    'lon' => $location->getLongitude()
                ];
            }
        }
        
        return $this->append(new GeoBoundingBoxQuery($field, $l, $params));
    }
    
    /**
     * Geo distance
     *
     * @param string $field
     * @param string $distance
     * @param Location $location
     * @return Search
     */
    public function geoDistance(string $field, string $distance, Location $location): Search
    {
        return $this->append(new GeoDistanceQuery(
            $field,
            $distance,
            [
                'lat' => $location->getLatitude(),
                'lon' => $location->getLongitude()
            ]
        ));
    }
    
    /**
     * Geo polygon
     *
     * @param string $field
     * @param Location[] $points
     * @return Search
     */
    public function geoPolygon(string $field, array $points): Search
    {
        $p = [];
        
        foreach ($points as $point) {
            if ($point instanceof Location) {
                $p[] = [
                    'lat' => $point->getLatitude(),
                    'lon' => $point->getLongitude()
                ];
            }
        }
        
        return $this->append(new GeoPolygonQuery($field, $p));
    }
    
    /**
     * Geo shape
     *
     * @param array $params
     * @return Search
     */
    public function geoShape(array $params = []): Search
    {
        return $this->append(new GeoShapeQuery($params));
    }
    
    /**
     * Nested
     *
     * @param string $path
     * @param \Closure $search
     * @param array $params
     * @return Search
     */
    public function nested(string $path, \Closure $search, array $params = []) : Search
    {
        $searchBuilder = new self($this->manager, new \ONGR\ElasticsearchDSL\Search());
        $search($searchBuilder);
        
        return $this->append(
            new NestedQuery(
                $path,
                $searchBuilder->getQuery(),
                $params
            )
        );
    }
    
    /**
     * Has child
     *
     * @param string $type
     * @param \Closure $search
     * @param array $params
     * @return Search
     */
    public function hasChild(string $type, \Closure $search, array $params = []): Search
    {
        $searchBuilder = new self($this->manager, new \ONGR\ElasticsearchDSL\Search());
        $search($searchBuilder);
        
        return $this->append(new HasChildQuery($type, $searchBuilder->getQuery(), $params));
    }
    
    /**
     * Has parent
     *
     * @param string $type
     * @param \Closure $search
     * @param array $params
     * @return Search
     */
    public function hasParent(string $type, \Closure $search, array $params = []): Search
    {
        $searchBuilder = new self($this->manager, new \ONGR\ElasticsearchDSL\Search());
        $search($searchBuilder);
        
        return $this->append(new HasParentQuery($type, $searchBuilder->getQuery(), $params));
    }
    
    /**
     * More like this
     *
     * @param string $like
     * @param array $params
     * @return Search
     */
    public function moreLikeThis(string $like, array $params = []): Search
    {
        return $this->append(new MoreLikeThisQuery($like, $params));
    }
    
    /**
     * Boosting
     *
     * @param BuilderInterface $positive
     * @param BuilderInterface $negative
     * @param float $negativeBoost
     * @return Search
     */
    public function boosting(BuilderInterface $positive, BuilderInterface $negative, float $negativeBoost) : Search
    {
        $this->append(new BoostingQuery($positive, $negative, $negativeBoost));
        return $this;
    }
    
    /**
     * Function score
     *
     * @param \Closure $search
     * @param \Closure $functionScore
     * @param array $params
     * @return Search
     */
    public function functionScore(\Closure $search, \Closure $functionScore, array $params = []) : Search
    {
        $searchBuilder = new self($this->manager, new \ONGR\ElasticsearchDSL\Search());
        $search($searchBuilder);
        
        $functionScoreBuilder = new FunctionScore($searchBuilder->getQuery(), $params);
        $functionScore($functionScoreBuilder);
        
        $this->append($functionScoreBuilder->getQuery());
        return $this;
    }
    
    /**
     * Constant score
     *
     * @param \Closure $search
     * @param array $params
     * @return Search
     */
    public function constantScore(\Closure $search, array $params = []) : Search
    {
        $searchBuilder = new self($this->manager, new \ONGR\ElasticsearchDSL\Search());
        $search($searchBuilder);
        
        $this->append(new ConstantScoreQuery($searchBuilder->getQuery(), $params));
        return $this;
    }
    
    /**
     * Dis max
     *
     * @param BuilderInterface[] $queries
     * @param array $params
     * @return Search
     */
    public function disMax(array $queries, array $params = []) : Search
    {
        $disMaxQuery = new DisMaxQuery($params);
        
        foreach ($queries as $query) {
            if ($query instanceof BuilderInterface) {
                $disMaxQuery->addQuery($query);
            }
        }
        
        $this->append($disMaxQuery);
        return $this;
    }
    
    /**
     * Nested inner hit
     *
     * @param string $name
     * @param string $path
     * @param \Closure|null $search
     * @return Search
     */
    public function nestedInnerHit(string $name, string $path, ?\Closure $search = null) : Search
    {
        $searchForNested = null;
        
        if ($search) {
            $searchBuilder = new self($this->manager, new \ONGR\ElasticsearchDSL\Search());
            $search($searchBuilder);
    
            $searchForNested = $searchBuilder->getSearch();
        }
        
        $this->search->addInnerHit(new NestedInnerHit($name, $path, $searchForNested));
        return $this;
    }
    
    /**
     * Parent inner hits
     *
     * @param string $name
     * @param string $path
     * @param \Closure|null $search
     * @return Search
     */
    public function parentInnerHit(string $name, string $path, ?\Closure $search = null) : Search
    {
        $searchForNested = null;
    
        if ($search) {
            $searchBuilder = new self($this->manager, new \ONGR\ElasticsearchDSL\Search());
            $search($searchBuilder);
        
            $searchForNested = $searchBuilder->getSearch();
        }
        
        $this->search->addInnerHit(new ParentInnerHit($name, $path, $searchForNested));
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
        $this->search
            ->setFrom($limit * ($page - 1))
            ->setSize($limit);
        
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
