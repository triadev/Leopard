<?php
namespace Triadev\Leopard;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Database\Eloquent\Model;
use Triadev\Es\Contract\ElasticsearchContract;
use Triadev\Es\Dsl\Dsl\Search;
use Triadev\Es\Dsl\Dsl\Suggestion;
use Triadev\Es\Dsl\Facade\ElasticDsl;
use Triadev\Leopard\Business\Dsl\SearchDsl;
use Triadev\Leopard\Business\Helper\IsModelSearchable;
use Triadev\Leopard\Contract\ElasticsearchManagerContract;
use Triadev\Leopard\Contract\Repository\ElasticsearchRepositoryContract;

class ElasticsearchManager implements ElasticsearchManagerContract
{
    use IsModelSearchable;
    
    /** @var Client */
    private $esClient;
    
    /**
     * ElasticsearchManager constructor.
     * @param ElasticsearchContract $elasticsearch
     */
    public function __construct(ElasticsearchContract $elasticsearch)
    {
        $this->esClient = $elasticsearch->getClient();
    }
    
    /**
     * Get elasticsearch client
     *
     * @return Client
     */
    public function getEsClient(): Client
    {
        return $this->esClient;
    }
    
    /**
     * Get es default index
     *
     * @return string
     */
    public function getEsDefaultIndex() : string
    {
        return config('leopard.index');
    }
    
    /**
     * Search
     *
     * @param \ONGR\ElasticsearchDSL\Search|null $search
     * @param Model|Searchable|null $model
     * @return SearchDsl|Search
     */
    public function search(
        ?\ONGR\ElasticsearchDSL\Search $search = null,
        ?Model $model = null
    ) : SearchDsl {
        return app()->make(SearchDsl::class, [
            'search' => $search,
            'model' => $model
        ]);
    }
    
    /**
     * Suggestion
     *
     * @return Suggestion
     */
    public function suggest() : Suggestion
    {
        return ElasticDsl::suggest();
    }
    
    /**
     * Repository
     *
     * @return ElasticsearchRepositoryContract
     */
    public function repository() : ElasticsearchRepositoryContract
    {
        return app(ElasticsearchRepositoryContract::class);
    }
    
    /**
     * Search statement
     *
     * @param array $params
     * @return array
     */
    public function searchStatement(array $params) : array
    {
        return $this->esClient->search($params);
    }
    
    /**
     * Put mapping statement
     *
     * @param array $params
     * @return array
     */
    public function putMappingStatement(array $params): array
    {
        return $this->esClient->indices()->putMapping($params);
    }
    
    /**
     * Index statement
     *
     * @param array $params
     * @return array
     */
    public function indexStatement(array $params) : array
    {
        return $this->esClient->index($params);
    }
    
    /**
     * Update statement
     *
     * @param array $params
     * @return array
     */
    public function updateStatement(array $params) : array
    {
        return $this->esClient->update($params);
    }
    
    /**
     * Exist statement
     *
     * @param array $params
     * @return bool
     */
    public function existStatement(array $params) : bool
    {
        return $this->esClient->exists($params);
    }
    
    /**
     * Exist index statement
     *
     * @param array $params
     * @return bool
     */
    public function existIndexStatement(array $params) : bool
    {
        return $this->esClient->indices()->exists($params);
    }
    
    /**
     * Delete statement
     *
     * @param array $params
     * @return array
     */
    public function deleteStatement(array $params) : array
    {
        return $this->esClient->delete($params);
    }
    
    /**
     * Get statement
     *
     * @param array $params
     * @return array|null
     */
    public function getStatement(array $params) : ?array
    {
        try {
            return $this->esClient->get($params);
        } catch (Missing404Exception $e) {
            return null;
        }
    }
    
    /**
     * Suggest statement
     *
     * @param array $params
     * @return array
     */
    public function suggestStatement(array $params): array
    {
        return $this->esClient->suggest($params);
    }
    
    /**
     * Bulk statement
     *
     * @param array $params
     * @return array
     */
    public function bulkStatement(array $params): array
    {
        return $this->esClient->bulk($params);
    }
}
