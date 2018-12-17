<?php
namespace Triadev\Leopard\Model\Entity;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $mapping
 * @property string $created_at
 * @property string $updated_at
 */
class MappingLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'triadev_mapping_log';
}
