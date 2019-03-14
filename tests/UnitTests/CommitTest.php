<?php

namespace PaperTrail\Tests\UnitTests;

use PaperTrail\Tests\TestCase;
use PaperTrail\Patch;
use PaperTrail\Commit;

class CommitTest extends TestCase
{
    protected $patch;

    protected function setUp()
    {
        parent::setUp();

        $this->patch = new Patch([
            ['value' => 'Aaron', 'op' => 'test', 'path' => '/name'],
            ['value' => 'Aaron', 'op' => 'replace', 'path' => '/name']
        ]);
    }

    /** @test */
    public function it_implements_create()
    {
        $commit = Commit::create(1, $this->patch);

        $this->assertInstanceOf(Commit::class, $commit);
    }

    /** @test */
    public function it_gets_the_version()
    {
        $commit = Commit::create(1, $this->patch);

        $this->assertEquals(1, $commit->version());
    }

    /** @test */
    public function version_cannot_be_changed()
    {
        $commit = Commit::create(1, $this->patch);

        $commit->version(1234);

        $this->assertFalse(1234 === $commit->version());
    }

    /** @test */
    public function it_gets_the_timestamp()
    {
        $commit = Commit::create(1, $this->patch);

        $this->assertTrue( is_int($commit->timestamp()) );
    }

    /** @test */
    public function timestamp_cannot_be_changed()
    {
        $commit = Commit::create(1, $this->patch);

        $commit->timestamp(1234);

        $this->assertFalse(1234 === $commit->timestamp());
    }

    /** @test */
    public function it_gets_the_patch()
    {
        $commit = Commit::create(1, $this->patch);

        $this->assertEquals($this->patch, $commit->patch());
    }
}