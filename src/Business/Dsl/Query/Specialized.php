<?php
namespace Triadev\Es\ODM\Busines\Dsl\Query;

use Triadev\Es\ODM\Business\Dsl\Query\AbstractQuery;
use ONGR\ElasticsearchDSL\Query\Specialized\MoreLikeThisQuery;

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
