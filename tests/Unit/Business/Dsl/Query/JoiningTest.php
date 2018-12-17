<?php
namespace Tests\Unit\Business\Dsl\Query;

use Tests\TestCase;
use Triadev\Leopard\Business\Dsl\Query\Joining;
use Triadev\Leopard\Business\Dsl\Query\TermLevel;
use Triadev\Leopard\Business\Dsl\Search;

class JoiningTest extends TestCase
{
    /** @var Joining */
    private $joining;
    
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
    
        $this->joining = new Joining();
    }
    
    /**
     * @test
     */
    public function it_builds_a_nested_query()
    {
        $result = $this->joining->nested('PATH', function (Search $search) {
            $search->termLevel(function (TermLevel $boolQuery) {
                $boolQuery
                    ->filter()
                    ->term('FIELD1', 'VALUE1')
                    ->term('FIELD2', 'VALUE2');
            });
        })->toDsl();
        
        $this->assertEquals([
            'query' => [
                'nested' => [
                    'path' => 'PATH',
                    'query' => [
                        'bool' => [
                            'filter' => [
                                [
                                    'term' => [
                                        'FIELD1' => 'VALUE1'
                                    ]
                                ],
                                [
                                    'term' => [
                                        'FIELD2' => 'VALUE2'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ], $result);
    }
    
    /**
     * @test
     */
    public function it_builds_a_has_child_query()
    {
        $result = $this->joining->hasChild('TYPE', function (Search $search) {
            $search->termLevel(function (TermLevel $boolQuery) {
                $boolQuery
                    ->filter()
                    ->term('FIELD1', 'VALUE1')
                    ->term('FIELD2', 'VALUE2');
            });
        })->toDsl();
        
        $this->assertEquals([
            'query' => [
                'has_child' => [
                    'type' => 'TYPE',
                    'query' => [
                        'bool' => [
                            'filter' => [
                                [
                                    'term' => [
                                        'FIELD1' => 'VALUE1'
                                    ]
                                ],
                                [
                                    'term' => [
                                        'FIELD2' => 'VALUE2'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ], $result);
    }
    
    /**
     * @test
     */
    public function it_builds_a_has_parent_query()
    {
        $result = $this->joining->hasParent('TYPE', function (Search $search) {
            $search->termLevel(function (TermLevel $boolQuery) {
                $boolQuery
                    ->filter()
                    ->term('FIELD1', 'VALUE1')
                    ->term('FIELD2', 'VALUE2');
            });
        })->toDsl();
        
        $this->assertEquals([
            'query' => [
                'has_parent' => [
                    'parent_type' => 'TYPE',
                    'query' => [
                        'bool' => [
                            'filter' => [
                                [
                                    'term' => [
                                        'FIELD1' => 'VALUE1'
                                    ]
                                ],
                                [
                                    'term' => [
                                        'FIELD2' => 'VALUE2'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ], $result);
    }
}
