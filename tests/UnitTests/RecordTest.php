<?php

namespace PhpJsonVersioning\Tests\UnitTests;

use PhpJsonVersioning\Tests\TestCase;
use PhpJsonVersioning\Patch;
use PhpJsonVersioning\Commit;
use PhpJsonVersioning\Record;

class RecordTest extends TestCase
{
    protected $commits;

    protected $record;

    protected function setUp()
    {
        parent::setUp();

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

        $this->record = Record::create($this->commits);
    }

    /** @test */
    public function it_encodes_to_json()
    {
        $json = $this->record->toJson();

        $decoded = json_decode($json, 1);

        $this->assertEquals("one", $decoded['commits'][0]['patch'][0]['value']);
    }

    /** @test */
    public function it_decodes_from_json()
    {
        $json = $this->record->toJson();

        $record = Record::fromJson($json);

        $this->assertEquals("one", $record->commits()[0]->patch()[0]['value']);
    }

    /** @test */
    public function it_can_add_commits()
    {
        $commit = Commit::create(new Patch([
            [
                "op" => "replace",
                "path" => "/version",
                "value" => "four"
            ]
        ]));
        
        $this->record->addCommit($commit);

        $this->assertCount(4, $this->record->commits());
    }

    /** @test */
    public function it_sorts_commits_by_timestamp()
    {
        $commits = [];
        $commits[] = $this->commits[0];
        $commits[] = $this->commits[2];
        $commits[] = $this->commits[1];

        $record = Record::create($commits);

        $this->assertEquals(0, $record->commits()[0]->timestamp());
        $this->assertEquals(1, $record->commits()[1]->timestamp());
        $this->assertEquals(2, $record->commits()[2]->timestamp());
    }
}