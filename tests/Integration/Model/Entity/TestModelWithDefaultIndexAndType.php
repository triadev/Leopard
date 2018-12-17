<?php
namespace Tests\Integration\Model\Entity;

use Illuminate\Database\Eloquent\Model;
use Triadev\Leopard\Searchable;

class TestModelWithDefaultIndexAndType extends Model
{
    use Searchable;
}
