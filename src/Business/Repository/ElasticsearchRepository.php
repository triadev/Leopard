<?php
namespace Triadev\Leopard\Business\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Triadev\Leopard\Business\Helper\IsModelSearchable;
use Triadev\Leopard\Contract\Repository\ElasticsearchRepositoryContract;
use Triadev\Leopard\Facade\Leopard;
use Triadev\Leopard\Searchable;

class ElasticsearchRepository implements ElasticsearchRepositoryContract
{
    use IsModelSearchable;
    
    /**
     * Save
     *
     * @param Model|Searchable $model
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function save(Model $model): array
    {
        $this->isModelSearchable($model);
        
        return Leopard::indexStatement([
            'index' => $model->getDocumentIndex(),
            'type' => $model->getDocumentType(),
            'id' => $model->getKey(),
            'body' => $model->getDocumentData()
        ]);
    }
    
    /**
     * Update
     *
     * @param Model|Searchable $model
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function update(Model $model): array
    {
        $this->isModelSearchable($model);
        
        return Leopard::updateStatement([
            'index' => $model->getDocumentIndex(),
            'type' => $model->getDocumentType(),
            'id' => $model->getKey(),
            'body' => [
                'doc' => $model->getDocumentData()
            ]
        ]);
    }
    
    /**
     * Delete
     *
     * @param Model|Searchable $model
     * @return bool
     *
     * @throws \InvalidArgumentException
     */
    public function delete(Model $model): bool
    {
        $this->isModelSearchable($model);
        
        $params = [
            'index' => $model->getDocumentIndex(),
            'type' => $model->getDocumentType(),
            'id' => $model->getKey(),
        ];
        
        if (Leopard::existStatement($params)) {
            Leopard::deleteStatement($params);
        }
        
        return true;
    }
    
    /**
     * Bulk save
     *
     * @param array|Collection $models
     * @return array
     */
    public function bulkSave($models) : array
    {
        $params = [];
    
        $defaultIndex = Leopard::getEsDefaultIndex();
        
        foreach ($models as $model) {
            /** @var Model|Searchable $model */
            $this->isModelSearchable($model);
            
            $params['body'][] = [
                'index' => [
                    '_index' => $model->getDocumentIndex() ?: $defaultIndex,
                    '_type' => $model->getDocumentType(),
                    '_id' => $model->getKey()
                ]
            ];
            
            $params['body'][] = $model->getDocumentData();
        }
        
        return Leopard::bulkStatement($params);
    }
    
    /**
     * Bulk delete
     *
     * @param array $models
     * @return array
     */
    public function bulkDelete(array $models): array
    {
        $params = [];
    
        $defaultIndex = Leopard::getEsDefaultIndex();
        
        foreach ($models as $model) {
            /** @var Model|Searchable $model */
            $this->isModelSearchable($model);
        
            $params['body'][] = [
                'delete' => [
                    '_index' => $model->getDocumentIndex() ?: $defaultIndex,
                    '_type' => $model->getDocumentType(),
                    '_id' => $model->getKey()
                ]
            ];
        }
    
        return Leopard::bulkStatement($params);
    }
}
