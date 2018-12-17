<?php
namespace Tests\Integration\Model\Entity;

use Illuminate\Database\Eloquent\Model;
use Triadev\Leopard\Searchable;

class TestModelWithIndividualIndexAndType extends Model
{
    use Searchable;
    
    /** @var string */
    public $documentIndex = 'index';
    
    /** @var string */
    public $documentType = 'type';
}
