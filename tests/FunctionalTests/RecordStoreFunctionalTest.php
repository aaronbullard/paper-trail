<?php

namespace PhpJsonVersioning\Tests\FunctionalTests;

use PhpJsonVersioning\Tests\TestCase;
use PhpJsonVersioning\Record;
use PhpJsonVersioning\Document;
use PhpJsonVersioning\RecordStore;

class RecordStoreFunctionalTest extends TestCase
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
        $manager = RecordStore::create();

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

    /** @test */
    public function it_saves_an_returns_each_version()
    {
        $manager = RecordStore::create();

        $manager->save(Document::create([
            'version' => 1
        ]));

        $manager->save(Document::create([
            'version' => 2
        ]));

        $manager->save(Document::create([
            'version' => 3
        ]));

        $this->assertEquals(1, $manager->getVersion(1)['version']);
        $this->assertEquals(2, $manager->getVersion(2)['version']);
        $this->assertEquals(3, $manager->getVersion(3)['version']);
        $this->assertEquals(3, $manager->getLatest()['version']);
    }

    /** @test */
    public function it_saves_an_returns_each_version_from_json()
    {
        $manager = RecordStore::create();

        $manager->save(Document::create([
            'version' => 1
        ]));

        $manager->save(Document::create([
            'version' => 2
        ]));

        $manager->save(Document::create([
            'version' => 3
        ]));

        $jsonRecord = $manager->getRecord()->toJson();

        $this->assertJson($jsonRecord);

        $manager = RecordStore::create(Record::fromJson($jsonRecord));

        $this->assertEquals(1, $manager->getVersion(1)['version']);
        $this->assertEquals(2, $manager->getVersion(2)['version']);
        $this->assertEquals(3, $manager->getVersion(3)['version']);
        $this->assertEquals(3, $manager->getLatest()['version']);
    }

}