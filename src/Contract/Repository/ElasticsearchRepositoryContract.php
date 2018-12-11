<?php
namespace Triadev\Es\ODM\Contract\Repository;

use Illuminate\Database\Eloquent\Model;
use Triadev\Es\ODM\Searchable;

interface ElasticsearchRepositoryContract
{
    /**
     * Save
     *
     * @param Model|Searchable $model
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function save(Model $model) : array;
    
    /**
     * Update
     *
     * @param Model|Searchable $model
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function update(Model $model) : array;
    
    /**
     * Delete
     *
     * @param Model|Searchable $model
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public function delete(Model $model) : bool;
}
