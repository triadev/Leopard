<?php
namespace Tests\Integration\Model\Entity;

use Illuminate\Database\Eloquent\Model;
use Triadev\Es\ODM\Searchable;

class TestModelWithDefaultIndexAndType extends Model
{
    use Searchable;
}
