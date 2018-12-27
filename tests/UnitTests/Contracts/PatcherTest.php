<?php

namespace PhpJsonVersioning\Tests\UnitTests\Contracts;

use PhpJsonVersioning\Json;
use PhpJsonVersioning\Patch;
use PhpJsonVersioning\Tests\TestCase;
use PhpJsonVersioning\Contracts\Patcher;
use PhpJsonVersioning\Services\JsonPatch;

class PatcherTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        
        $this->patcher = new JsonPatch();
        $this->origDoc = Json::fromArray(['name' => 'Aaron']);
        $this->newDoc = Json::fromArray(['name' => 'James']);
        $this->jsonPatch = '[{"value":"Aaron","op":"test","path":"\/name"},{"value":"James","op":"replace","path":"\/name"}]';
    }

    /** @test */
    public function it_implements_patcher()
    {
        $this->assertInstanceOf(Patcher::class, $this->patcher);
    }

    /** @test */
    public function it_creates_a_patch_using_diff()
    {
        $patch = $this->patcher->diff($this->origDoc, $this->newDoc);

        $this->assertInstanceOf(Patch::class, $patch);
        $this->assertEquals($this->jsonPatch, $patch->toJson());
    }

    /** @test */
    public function it_creates_a_document_using_a_patch()
    {
        $newDoc = $this->patcher->apply($this->origDoc, Patch::fromJson($this->jsonPatch));

        $this->assertInstanceOf(Json::class, $newDoc);
        $this->assertEquals($this->newDoc->toJson(), $newDoc->toJson());
    }
}