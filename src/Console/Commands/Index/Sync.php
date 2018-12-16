<?php
namespace Triadev\Es\ODM\Console\Commands\Index;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Triadev\Es\ODM\Business\Helper\IsModelSearchable;
use Triadev\Es\ODM\Facade\EsManager;
use Triadev\Es\ODM\Searchable;

class Sync extends Command
{
    use IsModelSearchable;
    
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'triadev:index:sync {--index=}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the elasticsearch index with persist data.';
    
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $index = $this->option('index') ?: EsManager::getEsDefaultIndex();
        
        if (!EsManager::getEsClient()->indices()->exists(['index' => $index])) {
            $this->error(sprintf("The index does not exist: %s", $index));
            return;
        }
        
        $chunkSize = $this->getChunkSize();
        
        foreach ($this->getModelsToSync($index) as $modelClass => $model) {
            $this->line(sprintf("Sync index with model: %s", $modelClass));
            
            $model::chunk($chunkSize, function ($models) {
                /** @var Collection $models */
                EsManager::repository()->bulkSave($models);
                $this->line(sprintf("Indexing a chunk of %s documents ...", count($models)));
            });
        }
    }
    
    /**
     * @return int
     */
    private function getChunkSize() : int
    {
        return config('triadev-elasticsearch-odm.sync.chunkSize');
    }
    
    /**
     * @param string $index
     * @return Model|Searchable[]
     */
    private function getModelsToSync(string $index) : array
    {
        $models = [];
    
        foreach (config(sprintf("triadev-elasticsearch-odm.sync.models.%s", $index), []) as $modelClass) {
            /** @var Model|Searchable $model */
            $model = new $modelClass();
    
            $this->isModelSearchable($model);
            
            $models[$modelClass] = $model;
        }
        
        return $models;
    }
}
