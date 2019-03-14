<?php

namespace PaperTrail\Tests\UnitTests;

use PaperTrail\Patch;
use PaperTrail\Exceptions\ValidationException;
use PaperTrail\Tests\TestCase;

class PatchTest extends TestCase
{
    protected $patch;

    protected $jsonPatch;

    protected function setUp()
    {
        parent::setUp();
        
        $this->patch = [
            ['op' => 'replace', 'path' => '', 'value' => ['name' => 'Aaron', 'age' => 41]]
        ];

        $this->jsonPatch = '[{"op":"replace","path":"","value":{"name":"Aaron","age":41}}]';
    }

    /** @test */
    public function it_validates_a_patch()
    {
        new Patch($this->patch);

        $this->expectException(ValidationException::class);

        new Patch([
            ['not' => 'a patch']
        ]);
    }

    /** @test */
    public function it_is_array_accessible()
    {
        $patch = new Patch($this->patch);

        $this->assertEquals('replace', $patch[0]['op']);
    }
}