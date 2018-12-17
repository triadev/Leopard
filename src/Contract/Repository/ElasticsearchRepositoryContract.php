<?php
namespace Triadev\Leopard\Contract\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Triadev\Leopard\Searchable;

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
    
    /**
     * Bulk save
     *
     * @param array|Collection $models
     * @return array
     */
    public function bulkSave($models) : array;
    
    /**
     * Bulk delete
     *
     * @param array $models
     * @return array
     */
    public function bulkDelete(array $models) : array;
}
