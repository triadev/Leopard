<?php
namespace Tests\Unit\Business\Dsl\Query;

use Tests\TestCase;
use Triadev\Leopard\Busines\Dsl\Query\Specialized;

class SpecializedTest extends TestCase
{
    /**
     * @test
     */
    public function it_builds_a_more_like_this_query()
    {
        $result = (new Specialized())->moreLikeThis('LIKE')->toDsl();
        
        $this->assertEquals([
            'query' => [
                'more_like_this' => [
                    'like' => 'LIKE'
                ]
            ]
        ], $result);
    }
}
