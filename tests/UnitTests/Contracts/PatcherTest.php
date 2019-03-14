<?php

namespace PaperTrail\Tests\UnitTests\Contracts;

use PaperTrail\Patch;
use PaperTrail\Document;
use PaperTrail\Tests\TestCase;
use PaperTrail\Contracts\Patcher;
use PaperTrail\Services\JsonPatch;
use PaperTrail\Exceptions\PatchingException;

class PatcherTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        
        $this->patcher = new JsonPatch();
        $this->origDoc = Document::create(['name' => 'Aaron']);
        $this->newDoc = Document::create(['name' => 'James']);
        $this->patch = [
            ['value' => 'Aaron', 'op' => 'test', 'path' => '/name'],
            ['value' => 'James', 'op' => 'replace', 'path' => '/name']
        ];
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
        $newDoc = $this->patcher->apply($this->origDoc, new Patch($this->patch));

        $this->assertInstanceOf(Document::class, $newDoc);
        $this->assertEquals($this->newDoc->toJson(), $newDoc->toJson());
        $this->assertNotEquals($this->origDoc->toJson(), $newDoc->toJson());
    }

    /** @test */
    public function it_does_not_mutate_original_doc()
    {
        $newDoc = $this->patcher->apply($this->origDoc, new Patch($this->patch));

        $this->assertNotEquals($this->origDoc->toJson(), $newDoc->toJson());
    }

    /** @test */
    public function it_throws_a_patching_exception()
    {
        $this->expectException(PatchingException::class);

        $newDoc = $this->patcher->apply(new Document(), new Patch([
            [
                'path' => '/no_such_path',
                'op' => 'replace',
                'value' => 'new_name'
            ]
        ]));
    }

}