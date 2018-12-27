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
        $commit = Commit::create(0, $this->patch);

        $this->assertInstanceOf(Commit::class, $commit);
    }

    /** @test */
    public function it_implements_fromJson()
    {
        $c = Commit::create(0, $this->patch);
        $commitAsJson = $c->toJson();

        $commit = Commit::fromJson($commitAsJson);

        $this->assertInstanceOf(Commit::class, $commit);

        $this->assertEquals($commitAsJson, $commit->toJson());
    }
}