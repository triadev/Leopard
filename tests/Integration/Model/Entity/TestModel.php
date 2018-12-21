<?php
namespace Tests\Integration\Model\Entity;

use Illuminate\Database\Eloquent\Model;
use Triadev\Leopard\Searchable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 */
class TestModel extends Model
{
    use Searchable;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'test';
    
    /** @var string */
    public $documentIndex = 'phpunit';
    
    /** @var string */
    public $documentType = 'test';
    
    /** @var bool */
    public $syncDocument = true;
    
    /**
     * Build document for elasticsearch
     *
     * @return array
     */
    public function buildDocument() : array
    {
        return [
            'id' => $this->getAttribute('id'),
            'name' => $this->getAttribute('name'),
            'email' => $this->getAttribute('email'),
            'oneToOneTitle' => $this->getOneToOneRelationshipTitle(),
            'oneToManyTitle' => $this->getOneToManyRelationshipTitle()
        ];
    }
    
    private function getOneToOneRelationshipTitle() : ?string
    {
        $relationship = $this->hasOne(TestRelationshipOneToOneModel::class, 'test_id')->first();
        return $relationship ? $relationship->title : null;
    }
    
    private function getOneToManyRelationshipTitle() : ?array
    {
        $relationship = $this->hasMany(TestRelationshipOneToManyModel::class)->get();
        
        $titles = [];
        
        foreach ($relationship as $r) {
            $titles[] = $r->title;
        }
        
        return $titles;
    }
}
