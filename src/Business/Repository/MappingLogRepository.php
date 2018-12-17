<?php
namespace Triadev\Leopard\Business\Repository;

use Illuminate\Support\Facades\DB;
use Triadev\Leopard\Contract\Repository\MappingLogRepositoryContract;
use Triadev\Leopard\Model\Entity\MappingLog;

class MappingLogRepository implements MappingLogRepositoryContract
{
    /**
     * @inheritdoc
     */
    public function add(string $mapping)
    {
        $entity = new MappingLog();
        
        $entity->mapping = $mapping;
        $entity->saveOrFail();
    }
    
    /**
     * @inheritdoc
     */
    public function find(int $id) : ?MappingLog
    {
        return MappingLog::find($id);
    }
    
    /**
     * @inheritdoc
     */
    public function delete(int $id)
    {
        if ($mappingLog = $this->find($id)) {
            $mappingLog->delete();
        }
    }
    
    /**
     * @inheritdoc
     */
    public function all(): array
    {
        return MappingLog::all(['id', 'mapping'])->toArray();
    }
    
    /**
     * @inheritdoc
     */
    public function reset()
    {
        DB::table('triadev_mapping_log')->truncate();
    }
}
