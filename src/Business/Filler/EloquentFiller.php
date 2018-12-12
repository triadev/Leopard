<?php
namespace Triadev\Es\ODM\Business\Filler;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Triadev\Es\ODM\Contract\FillerContract;
use Triadev\Es\ODM\Model\SearchResult;
use Triadev\Es\ODM\Searchable;

class EloquentFiller implements FillerContract
{
    /**
     * Fill
     *
     * @param Model $model
     * @param SearchResult $searchResult
     */
    public function fill(Model $model, SearchResult $searchResult)
    {
        $searchResult->setHits(
            $searchResult->getHits()->map(function (array $hit) use ($model) {
                return $this->fillModel($model, $hit);
            })
        );
    }
    
    /**
     * Fill model
     *
     * @param Model $model
     * @param array $hit
     * @return Model
     */
    public function fillModel(Model $model, array $hit = []) : Model
    {
        $source = array_get($hit, '_source');
        
        if ($id = array_get($hit, '_id', null)) {
            $source[$model->getKeyName()] = is_numeric($id) ? intval($id) : $id;
        }
        
        /** @var Model|Searchable $instance */
        $instance = $this->newFromBuilderRecursive($model, $source);
        
        $instance->documentScore = array_get($hit, '_score');
        $instance->isDocument = true;
        
        if ($version = array_get($hit, '_version')) {
            $instance->documentVersion = $version;
        }
        
        return $instance;
    }
    
    /**
     * Fill a model with form an elastic hit.
     *
     * @param Model $model
     * @param array $attributes
     *
     * @return Model
     */
    public function newFromBuilderRecursive(Model $model, array $attributes = []) : Model
    {
        $instance = $model->newInstance([], $exists = true);
        
        $instance->unguard();
        
        $instance->fill($attributes);
        $instance->reguard();
        
        $this->loadRelationsAttributesRecursive($instance);
        
        return $instance;
    }
    
    /**
     * Get the relations attributes from a model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    protected function loadRelationsAttributesRecursive(Model $model)
    {
        foreach ($model->getAttributes() as $key => $value) {
            if (!method_exists($model, $key)) {
                continue;
            }
            
            try {
                if ((new \ReflectionMethod($model, $key))->class != Model::class) {
                    $relation = $model->$key();
                    if ($relation instanceof Relation) {
                        if ($value === null) {
                            $models = null;
                        } else {
                            if (!$multiLevelRelation = $this->isMultiLevelArray($value)) {
                                $value = [$value];
                            }
                
                            $models = $this->hydrateRecursive($relation->getModel(), $value);
                            if (!$multiLevelRelation) {
                                $models = $models->first();
                            }
                        }
            
                        unset($model[$key]);
                        $model->setRelation($key, $models);
                    }
                }
            } catch (\ReflectionException $e) {
                continue;
            }
        }
    }
    
    /**
     * Create a collection of models from plain arrays recursive.
     *
     * @param Model    $model
     * @param array    $items
     *
     * @return Collection
     */
    protected function hydrateRecursive(Model $model, array $items)
    {
        return $model->newCollection(array_map(function ($item) use ($model) {
            return $this->newFromBuilderRecursive($model, $item);
        }, $items));
    }
    
    /**
     * Check if an array is multi-level array like [[id], [id], [id]].
     *
     * For detect if a relation field is single model or collections.
     *
     * @param array $array
     *
     * @return bool
     */
    private function isMultiLevelArray(array $array)
    {
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                return false;
            }
        }
        
        return true;
    }
}
