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

        $this->record = Record::create($this->commits);
    }

    /** @test */
    public function it_encodes_to_json()
    {
        $json = $this->record->toJson();

        $decoded = json_decode($json, 1);

        $this->assertEquals("one", $decoded[0]['patch'][0]['value']);
    }

    /** @test */
    public function it_decodes_from_json()
    {
        $json = $this->record->toJson();

        $record = Record::fromJson($json);

        $this->assertEquals("one", $record[0]->patch()[0]['value']);
    }
}