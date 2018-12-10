<?php
namespace Triadev\Es\ODM\Business\Repository;

use Illuminate\Database\Eloquent\Model;
use Triadev\Es\ODM\Contract\Repository\ElasticsearchRepositoryContract;
use Triadev\Es\ODM\Facade\EsManager;
use Triadev\Es\ODM\Searchable;

class ElasticsearchRepository implements ElasticsearchRepositoryContract
{
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
    
    private function isModelSearchable(Model $model)
    {
        $traits = class_uses_recursive(get_class($model));
        
        if (!isset($traits[Searchable::class])) {
            throw new \InvalidArgumentException(get_class($model).' does not use the searchable trait.');
        }
    }
}
