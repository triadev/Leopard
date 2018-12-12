<?php
namespace Tests\Unit\Business\Helper;

use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;
use Triadev\Es\ODM\Business\Helper\IsModelSearchable;
use Triadev\Es\ODM\Searchable;

class IsModelSearchableTest extends TestCase
{
    use IsModelSearchable;
    
    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function it_throws_an_exception_if_a_model_is_not_searchable()
    {
        $this->isModelSearchable(new class extends Model {});
    }
    
    /**
     * @test
     */
    public function it_checks_if_a_model_is_searchable()
    {
        $this->assertTrue($this->isModelSearchable(new class extends Model {
            use Searchable;
        }));
    }
}
