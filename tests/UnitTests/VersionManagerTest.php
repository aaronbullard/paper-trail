<?php

namespace PhpJsonVersioning\Tests\UnitTests;

use PhpJsonVersioning\Tests\TestCase;
use PhpSchema\ValidationException;
use PhpJsonVersioning\Patch;
use PhpJsonVersioning\Commit;
use PhpJsonVersioning\Record;
use PhpJsonVersioning\Document;
use PhpJsonVersioning\VersionManager;
use PhpJsonVersioning\Services\JsonPatch;

class VersionManagerTest extends TestCase
{
    protected $patcher;

    protected $commits;

    protected function setUp()
    {
        parent::setUp();

        // $this->patch = Patch::fromJson('[{"value":"Aaron","op":"test","path":"\/name"},{"value":"James","op":"replace","path":"\/name"}]');

        $this->commits = [];
        $this->commits[] = Commit::create(new Patch([
            [
                "op" => "add",
                "path" => "/version",
                "value" => "one"
            ]
        ]));

        $this->commits[] = Commit::create(new Patch([
            [
                "op" => "replace",
                "path" => "/version",
                "value" => "two"
            ]
        ]));

        $this->commits[] = Commit::create(new Patch([
            [
                "op" => "replace",
                "path" => "/version",
                "value" => "three"
            ]
        ]));

        // Create more detailed timestamps
        foreach($this->commits as $index => $commit){
            $commit->timestamp($index);
        }

        $this->patcher = new JsonPatch();
        $this->manager = new VersionManager($this->patcher);

        $this->manager->load(Record::create($this->commits));
    }

    /** @test */
    public function it_gives_history()
    {
        $history = $this->manager->getHistory();

        $this->assertEquals(1, $history[0]['version']);
        $this->assertEquals("one", $history[0]['document']['version']);
    }

    /** @test */
    public function it_saves_a_new_commit()
    {
        $this->assertEquals(3, count($this->manager->getHistory()));
        
        // save a new version
        $this->manager->save(new Document(['version' => 'four']), "version four");

        $this->assertEquals("four", $this->manager->getLatest()['version']);
        $this->assertEquals("version four", $this->manager->getHistory()[3]['comment']);
    }

    /** @test */
    public function it_returns_the_mutated_record()
    {
        // save a new version
        $this->manager->save(new Document(['version' => 'four']), "version four");

        $record = $this->manager->getRecord();

        $this->assertInstanceOf(Record::class, $record);
        $this->assertEquals("four", $record->commits()[3]->patch()[1]->value);
    }
}