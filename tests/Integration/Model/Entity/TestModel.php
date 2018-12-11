<?php
namespace Tests\Integration\Model\Entity;

use Illuminate\Database\Eloquent\Model;
use Triadev\Es\ODM\Searchable;

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
            'email' => $this->getAttribute('email')
        ];
    }
}
