<?php
namespace Triadev\Es\ODM\Contract;

use Illuminate\Database\Eloquent\Model;
use Triadev\Es\ODM\Model\SearchResult;

interface FillerContract
{
    /**
     * Fill
     *
     * @param Model $model
     * @param SearchResult $searchResult
     */
    public function fill(Model $model, SearchResult $searchResult);
}
