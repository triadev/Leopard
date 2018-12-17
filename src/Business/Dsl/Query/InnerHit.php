<?php
namespace Triadev\Leopard\Business\Dsl\Query;

use ONGR\ElasticsearchDSL\InnerHit\NestedInnerHit;
use ONGR\ElasticsearchDSL\InnerHit\ParentInnerHit;
use Triadev\Leopard\Business\Dsl\Search;
use Triadev\Leopard\Business\Dsl\AbstractQuery;

class InnerHit extends AbstractQuery
{
    /**
     * Nested inner hit
     *
     * @param string $name
     * @param string $path
     * @param \Closure|null $search
     * @return InnerHit
     */
    public function nestedInnerHit(string $name, string $path, ?\Closure $search = null) : InnerHit
    {
        $searchForNested = null;
        
        if ($search) {
            $searchBuilder = app()->make(Search::class);
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
     * @return InnerHit
     */
    public function parentInnerHit(string $name, string $path, ?\Closure $search = null) : InnerHit
    {
        $searchForNested = null;
        
        if ($search) {
            $searchBuilder = app()->make(Search::class);
            $search($searchBuilder);
            
            $searchForNested = $searchBuilder->getSearch();
        }
        
        $this->search->addInnerHit(new ParentInnerHit($name, $path, $searchForNested));
        return $this;
    }
}
