<?php
namespace Tests\Unit\Business\Dsl;

use Tests\TestCase;
use Triadev\Es\ODM\Facade\EsManager;

class SuggestionTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
    }
    
    /**
     * @test
     */
    public function it_builds_a_suggestion_term_query()
    {
        $result = EsManager::suggest()->term('NAME', 'TEXT', 'FIELD')->toDsl();
        
        $this->assertEquals([
            'suggest' => [
                'NAME' => [
                    'text' => 'TEXT',
                    'term' => [
                        'field' => 'FIELD'
                    ]
                ]
            ]
        ], $result);
    }
    
    /**
     * @test
     */
    public function it_builds_a_suggestion_phrase_query()
    {
        $result = EsManager::suggest()->phrase('NAME', 'TEXT', 'FIELD')->toDsl();
        
        $this->assertEquals([
            'suggest' => [
                'NAME' => [
                    'text' => 'TEXT',
                    'phrase' => [
                        'field' => 'FIELD'
                    ]
                ]
            ]
        ], $result);
    }
    
    /**
     * @test
     */
    public function it_builds_a_suggestion_completion_query()
    {
        $result = EsManager::suggest()->completion('NAME', 'TEXT', 'FIELD')->toDsl();
        
        $this->assertEquals([
            'suggest' => [
                'NAME' => [
                    'text' => 'TEXT',
                    'completion' => [
                        'field' => 'FIELD'
                    ]
                ]
            ]
        ], $result);
    }
}