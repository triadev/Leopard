<?php
namespace Tests\Integration\Model\Entity;

use Illuminate\Database\Eloquent\Model;
use Triadev\Leopard\Model\SyncRelationship;
use Triadev\Leopard\Searchable;

/**
 * @property int $id
 * @property int $test_model_id
 * @property string $title
 */
class TestRelationshipOneToManyModel extends Model
{
    use Searchable;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'relationship_one_to_many';
    
    /** @var bool */
    public $syncDocument = false;
    
    /**
     * Build sync relationships
     *
     * @return SyncRelationship[]
     */
    public function buildSyncRelationships() : array
    {
        return [
            SyncRelationship::create(TestModel::class)
        ];
    }
}
