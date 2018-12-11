<?php
namespace Triadev\Es\ODM\Business\Repository;

use Illuminate\Database\Eloquent\Model;
use Triadev\Es\ODM\Business\Helper\IsModelSearchable;
use Triadev\Es\ODM\Contract\Repository\ElasticsearchRepositoryContract;
use Triadev\Es\ODM\Facade\EsManager;
use Triadev\Es\ODM\Searchable;

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
        
        return EsManager::indexStatement([
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
        
        return EsManager::updateStatement([
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
        
        if (EsManager::existStatement($params)) {
            EsManager::deleteStatement($params);
        }
        
        return true;
    }
    
    /**
     * Bulk save
     *
     * @param array $models
     * @return array
     */
    public function bulkSave(array $models) : array
    {
        $params = [];
    
        $defaultIndex = EsManager::getEsDefaultIndex();
        
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
        
        return EsManager::bulkStatement($params);
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
    
        $defaultIndex = EsManager::getEsDefaultIndex();
        
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
    
        return EsManager::bulkStatement($params);
    }
}
