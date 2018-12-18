<?php
namespace Triadev\Leopard\Contract;

use Elasticsearch\Client;
use Triadev\Leopard\Business\Dsl\Search;
use Triadev\Leopard\Business\Dsl\Suggestion;
use Triadev\Leopard\Contract\Repository\ElasticsearchRepositoryContract;

interface ElasticsearchManagerContract
{
    /**
     * Get elasticsearch client
     *
     * @return Client
     */
    public function getEsClient() : Client;
    
    /**
     * Get es default index
     *
     * @return string
     */
    public function getEsDefaultIndex() : string;
    
    /**
     * Search
     *
     * @return Search
     */
    public function search() : Search;
    
    /**
     * Suggestion
     *
     * @return Suggestion
     */
    public function suggest() : Suggestion;
    
    /**
     * Repository
     *
     * @return ElasticsearchRepositoryContract
     */
    public function repository() : ElasticsearchRepositoryContract;
    
    /**
     * Map
     *
     * @param \Closure $blueprint
     * @param string $index
     * @param string $type
     */
    public function map(\Closure $blueprint, string $index, string $type);
    
    /**
     * Search statement
     *
     * @param array $params
     * @return array
     */
    public function searchStatement(array $params) : array;
    
    /**
     * Put mapping statement
     *
     * @param array $params
     * @return array
     */
    public function putMappingStatement(array $params) : array;
    
    /**
     * Index statement
     *
     * @param array $params
     * @return array
     */
    public function indexStatement(array $params) : array;
    
    /**
     * Update statement
     *
     * @param array $params
     * @return array
     */
    public function updateStatement(array $params) : array;
    
    /**
     * Exist statement
     *
     * @param array $params
     * @return bool
     */
    public function existStatement(array $params) : bool;
    
    /**
     * Exist index statement
     *
     * @param array $params
     * @return bool
     */
    public function existIndexStatement(array $params) : bool;
    
    /**
     * Delete statement
     *
     * @param array $params
     * @return array
     */
    public function deleteStatement(array $params) : array;
    
    /**
     * Get statement
     *
     * @param array $params
     * @return array|null
     */
    public function getStatement(array $params) : ?array;
    
    /**
     * Suggest statement
     *
     * @param array $params
     * @return array
     */
    public function suggestStatement(array $params) : array;
    
    /**
     * Bulk statement
     *
     * @param array $params
     * @return array
     */
    public function bulkStatement(array $params) : array;
}
