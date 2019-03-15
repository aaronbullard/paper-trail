<?php

namespace PaperTrail\Tests\FunctionalTests;

use PaperTrail\Tests\TestCase;
use PaperTrail\RecordStore;

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
        $store = RecordStore::create();

        $store->save($this->src);

        $doc = $store->getLatest();

        $doc["books"][] = [ "title" => "Title 3", "author" => "Jack Doe" ];
        $doc["total"] = 3;

        $store->save($doc);

        $this->assertJson($store->toJson());
        $this->assertEquals(
            "Title 3",
            json_decode($store->toJson(), 2)['commits'][1]['patch'][2]['value']['title']
        );
    }

    /** @test */
    public function it_saves_an_returns_each_version()
    {
        $store = RecordStore::create();

        $store->save(['version' => 1]);
        $store->save(['version' => 2]);
        $store->save(['version' => 3]);

        $this->assertEquals(1, $store->getVersion(1)['version']);
        $this->assertEquals(2, $store->getVersion(2)['version']);
        $this->assertEquals(3, $store->getVersion(3)['version']);
        $this->assertEquals(3, $store->getLatest()['version']);
    }

    /** @test */
    public function it_saves_an_returns_each_version_from_json()
    {
        $store = RecordStore::create();

        $store->save(['version' => 1]);
        $store->save(['version' => 2]);
        $store->save(['version' => 3]);

        $jsonRecord = $store->toJson();

        $this->assertJson($jsonRecord);

        $store = RecordStore::fromJson($jsonRecord);

        $this->assertEquals(1, $store->getVersion(1)['version']);
        $this->assertEquals(2, $store->getVersion(2)['version']);
        $this->assertEquals(3, $store->getVersion(3)['version']);
        $this->assertEquals(3, $store->getLatest()['version']);
    }

    /** @test */
    public function demo_test()
    {
        $store = RecordStore::create();

        $version1 = [
            "library" =>  "My Personal Library",
            "total" =>  2,
            "books" =>  [
                [ "title" => "Title 1", "author" => "Jane Doe" ],
                [ "title" => "Title 2", "author" => "John Doe" ]
            ]
        ];

        $version2 = [
            "library" =>  "My Personal Library",
            "total" =>  3,
            "books" =>  [
                [ "title" => "Title 1", "author" => "Jane Doe" ],
                [ "title" => "Title 2", "author" => "John Doe" ],
                [ "title" => "Title 3", "author" => "Jack Doe" ]
            ]
        ];

        $store->save($version1);
        $doc = $store->getLatest();
        $this->assertCount(2, $doc['books']);

        // Update document
        $store->save($version2, "Added a book");

        // Cast record to JSON for persistence
        $jsonRecord = $store->toJson();
        // echo $jsonRecord;
        /*
        {"commits":[{"version":1,"patch":[{"value":"My Personal Library","op":"add","path":"\/library"},{"value":2,"op":"add","path":"\/total"},{"value":[{"title":"Title 1","author":"Jane Doe"},{"title":"Title 2","author":"John Doe"}],"op":"add","path":"\/books"}],"timestamp":1552678141,"comment":null},{"version":2,"patch":[{"value":2,"op":"test","path":"\/total"},{"value":3,"op":"replace","path":"\/total"},{"value":{"title":"Title 3","author":"Jack Doe"},"op":"add","path":"\/books\/2"}],"timestamp":1552678141,"comment":null}]}
        */

        // Hydrate the RecordStore from your JSON record
        $store = RecordStore::fromJson($jsonRecord);

        // Get array of each version eg. {version, timestamp, comment, document}
        $versions = $store->getHistory();
        $this->assertCount(2, $versions);
        $this->assertCount(3, $versions[1]['document']['books']);
        $this->assertEquals("Added a book", $versions[1]['comment']);

        // Retrieve a version
        $this->assertCount(2, $store->getVersion(1)['books']);
        $this->assertCount(3, $store->getVersion(2)['books']);
    }

}