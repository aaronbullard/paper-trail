<?php

namespace PhpJsonVersioning\Tests\UnitTests;

use PhpJsonVersioning\Tests\TestCase;
use PhpSchema\ValidationException;
use PhpJsonVersioning\Patch;
use PhpJsonVersioning\Commit;

class CommitTest extends TestCase
{
    protected $patch;

    protected function setUp()
    {
        parent::setUp();
        $this->patch = Patch::fromJson('[{"value":"Aaron","op":"test","path":"\/name"},{"value":"James","op":"replace","path":"\/name"}]');
    }

    /** @test */
    public function it_implements_create()
    {
        $commit = Commit::create($this->patch);

        $this->assertInstanceOf(Commit::class, $commit);
    }

    /** @test */
    public function it_implements_fromJson()
    {
        $c = Commit::create($this->patch);
        $commitAsJson = $c->toJson();

        $commit = Commit::fromJson($commitAsJson);

        $this->assertInstanceOf(Commit::class, $commit);

        $this->assertEquals($commitAsJson, $commit->toJson());
    }

    /** @test */
    public function it_gets_the_timestamp()
    {
        $commit = Commit::create($this->patch);

        $this->assertTrue( is_int($commit->timestamp()) );
    }

    /** @test */
    public function it_gets_the_patch()
    {
        $commit = Commit::create($this->patch);

        $this->assertEquals($this->patch, $commit->patch());
    }
}