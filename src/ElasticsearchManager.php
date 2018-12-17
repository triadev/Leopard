<?php
namespace Triadev\Leopard;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Triadev\Es\Contract\ElasticsearchContract;
use Triadev\Leopard\Business\Dsl\Search;
use Triadev\Leopard\Business\Dsl\Suggestion;
use Triadev\Leopard\Business\Mapping\Builder;
use Triadev\Leopard\Contract\ElasticsearchManagerContract;
use Triadev\Leopard\Contract\Repository\ElasticsearchRepositoryContract;

class ElasticsearchManager implements ElasticsearchManagerContract
{
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
     * @return Search
     */
    public function search(): Search
    {
        return app()->make(Search::class);
    }
    
    /**
     * Suggestion
     *
     * @return Suggestion
     */
    public function suggest() : Suggestion
    {
        return app()->make(Suggestion::class);
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
     * Map
     *
     * @param \Closure $blueprint
     * @param string $index
     * @param string $type
     */
    public function map(\Closure $blueprint, string $index, string $type)
    {
        (new Builder())->create($blueprint, $index, $type);
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
