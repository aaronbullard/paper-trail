<?php

namespace PhpJsonVersioning\Tests\FunctionalTests;

use PhpJsonVersioning\Tests\TestCase;
use PhpJsonVersioning\Record;
use PhpJsonVersioning\Document;
use PhpJsonVersioning\VersionManager;

class VersionManagerTest extends TestCase
{
    protected $src;

    protected function setUp()
    {
        parent::setUp();

        $this->src = [
            "library" =>  "My Personal Library",
            "total" =>  2,
            "books" =>  [
                [ "title" => "Title 1", "author" => "Jane Doe" ],
                [ "title" => "Title 2", "author" => "John Doe" ]
            ]
        ];
    }

    /** @test */
    public function it_creates_json_record_for_persistance()
    {
        $manager = VersionManager::create();

        $manager->save(Document::create($this->src));

        $doc = $manager->getLatest();

        $doc["books"][] = [ "title" => "Title 3", "author" => "Jake Doe" ];
        $doc["total"] = 3;

        $manager->save($doc);
        $record = $manager->getRecord();

        $this->assertInstanceOf(Record::class, $record);
        $this->assertJson($record->toJson());
        $this->assertEquals(
            "Title 3",
            json_decode($record->toJson(), 2)['commits'][1]['patch'][2]['value']['title']
        );
    }

}