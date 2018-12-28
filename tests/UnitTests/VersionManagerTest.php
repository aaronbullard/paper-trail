<?php

namespace PhpJsonVersioning\Tests\UnitTests;

use Mockery;
use PhpJsonVersioning\Tests\TestCase;
use PhpSchema\ValidationException;
use PhpJsonVersioning\Json;
use PhpJsonVersioning\Patch;
use PhpJsonVersioning\Commit;
use PhpJsonVersioning\VersionManager;
use PhpJsonVersioning\Services\JsonPatch;

class VersionManagerTest extends TestCase
{
    protected $patch;

    protected $commits;

    protected function setUp()
    {
        parent::setUp();
        $this->patch = Patch::fromJson('[{"value":"Aaron","op":"test","path":"\/name"},{"value":"James","op":"replace","path":"\/name"}]');

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
    }

    protected function mockCommit(int $timestamp = 0)
    {
        $commit = Mockery::mock(Commit::class);
        $commit->shouldReceive('timestamp')->andReturn($timestamp);

        return $commit;
    }

    /** @test */
    public function it_loads_commits_in_order_and_gets_versions()
    {
        $commits = [];
        $commits[] = $this->commits[0];
        $commits[] = $this->commits[2];
        $commits[] = $this->commits[1];

        $this->manager->load(...$commits);

        $this->assertEquals("one", $this->manager->getVersion(1)->toObject()->version);
        $this->assertEquals("two", $this->manager->getVersion(2)->toObject()->version);
        $this->assertEquals("three", $this->manager->getVersion(3)->toObject()->version);
        $this->assertEquals("three", $this->manager->getLatest()->toObject()->version);
    }

    /** @test */
    public function it_gives_history()
    {
        $this->manager->load(...$this->commits);

        $history = $this->manager->getHistory();

        $this->assertEquals(1, $history[0]['version']);
        $this->assertEquals("one", $history[0]['json']->toObject()->version);
    }

    /** @test */
    public function it_saves_a_new_commit()
    {
        $this->manager->load(...$this->commits);
        $this->assertEquals(3, count($this->manager->getHistory()));
        
        // save a new version
        $this->manager->save(new Json('{"version": "four"}'), "version four");

        $this->assertEquals("four", $this->manager->getLatest()->toObject()->version);
        $this->assertEquals("version four", $this->manager->getHistory()[3]['comment']);
    }
}