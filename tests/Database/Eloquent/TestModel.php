<?php
namespace Tests\Database\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Triadev\Es\ODM\Searchable;

class TestModel extends Model
{
    use Searchable;
    
    /** @var string */
    public $documentIndex = 'test_index';
    
    /** @var bool */
    public $syncDocument = true;
}
