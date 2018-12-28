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

        $patches = [
            ["op" => "add", "path" => "/version", "value" => "one"],
            ["op" => "replace", "path" => "/version", "value" => "two"],
            ["op" => "replace", "path" => "/version", "value" => "three"]
        ];

        $this->commits = array_map(function($patch_data, $version){
            return Commit::create($version + 1, new Patch([$patch_data]));
        }, $patches, array_keys($patches));

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
        $this->record->createCommit(
            new Patch([["op" => "replace", "path" => "/version","value" => "four"]])
        );

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

        $this->assertEquals(1, $record->commits()[0]->version());
        $this->assertEquals(2, $record->commits()[1]->version());
        $this->assertEquals(3, $record->commits()[2]->version());
    }
}