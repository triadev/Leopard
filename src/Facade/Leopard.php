<?php
namespace Triadev\Leopard\Facade;

use Elasticsearch\Client;
use Illuminate\Support\Facades\Facade;
use Triadev\Leopard\Business\Repository\ElasticsearchRepository;
use Triadev\Leopard\Contract\ElasticsearchManagerContract;
use Triadev\Leopard\Business\Dsl\Search;
use Triadev\Leopard\Business\Dsl\Suggestion;

/**
 * Class Leopard
 * @package Triadev\Leopard\Facade
 *
 * @method static Client getEsClient()
 * @method static string getEsDefaultIndex()
 * @method static Search search()
 * @method static Suggestion suggest()
 * @method static ElasticsearchRepository repository()
 * @method static map(\Closure $blueprint, string $index, string $type)
 * @method static array searchStatement(array $params)
 * @method static array putMappingStatement(array $params)
 * @method static array indexStatement(array $params)
 * @method static array updateStatement(array $params)
 * @method static bool existStatement(array $params)
 * @method static array deleteStatement(array $params)
 * @method static array getStatement(array $params)
 * @method static array suggestStatement(array $params)
 * @method static array bulkStatement(array $params)
 */
class Leopard extends Facade
{
    /**
     * Get a plastic manager instance for the default connection.
     *
     * @return ElasticsearchManagerContract
     */
    protected static function getFacadeAccessor()
    {
        return app(ElasticsearchManagerContract::class);
    }
}
