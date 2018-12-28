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

    protected $record;

    protected $manager;

    protected function setUp()
    {
        parent::setUp();

        $patches = [
            ["op" => "add", "path" => "/version", "value" => "one"],
            ["op" => "replace", "path" => "/version", "value" => "two"],
            ["op" => "replace", "path" => "/version", "value" => "three"]
        ];

        $this->commits = array_map(function($patch_data, $version){
            return Commit::create($version + 1, new Patch([$patch_data]));
        }, $patches, array_keys($patches));

        $this->record = Record::create($this->commits);
 
        $this->patcher = new JsonPatch();

        $this->manager = new VersionManager($this->patcher);
        $this->manager->load($this->record);
    }

    /** @test */
    public function it_has_a_helper_constructor()
    {
        // with no record
        $manager = VersionManager::create();
        $this->assertCount(0, $manager->getRecord()->commits());

        // with record
        $manager = VersionManager::create($this->record);
        $this->assertCount(3, $manager->getRecord()->commits());
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

    /** @test */
    public function it_creates_a_record_for_first_save()
    {
        $manager = new VersionManager($this->patcher);

        $manager->save(new Document(['version' => 'one']), "version one");

        $this->assertInstanceOf(Record::class, $manager->getRecord());
        $this->assertCount(1, $manager->getRecord()->commits());
        $this->assertEquals("version one", $manager->getRecord()->commits()[0]->comment());
    }

    /** @test */
    public function mutating_a_version_for_saving()
    {
        $doc = $this->manager->getLatest();

        $doc['version'] = "four";

        $this->manager->save($doc);
        $doc = $this->manager->getLatest();

        $this->assertEquals("four", $doc['version']);
    }
}