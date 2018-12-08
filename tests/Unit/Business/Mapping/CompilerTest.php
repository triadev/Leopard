<?php
namespace Tests\Unit\Business\Mapping;

use Illuminate\Support\Fluent;
use Tests\TestCase;
use Triadev\Es\ODM\Business\Mapping\Compiler;

class CompilerTest extends TestCase
{
    /** @var Compiler */
    private $compiler;
    
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        
        $this->compiler = new Compiler();
    }
    
    /**
     * @test
     */
    public function it_compiles_a_text()
    {
        $this->assertEquals([
            'type' => 'text',
            'analyzer' => 'ANALYZER'
        ], $this->compiler->compileText(new Fluent([
            'type' => 'text',
            'analyzer' => 'ANALYZER'
        ])));
    }
}
