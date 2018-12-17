<?php
namespace Triadev\Leopard\Busines\Dsl\Query;

use ONGR\ElasticsearchDSL\Query\Specialized\MoreLikeThisQuery;
use Triadev\Leopard\Business\Dsl\AbstractQuery;

class Specialized extends AbstractQuery
{
    /**
     * More like this
     *
     * @param string $like
     * @param array $params
     * @return Specialized
     */
    public function moreLikeThis(string $like, array $params = []): Specialized
    {
        return $this->append(new MoreLikeThisQuery($like, $params));
    }
}
