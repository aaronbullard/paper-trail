<?php

namespace PhpJsonVersioning\Tests\UnitTests;

use PhpJsonVersioning\Tests\TestCase;
use PhpJsonVersioning\Document;

class DocumentTest extends TestCase
{
    /** @test */
    public function it_converts_to_string()
    {
        $arr = ['name' => 'Aaron'];
        $person = Document::create($arr);

        $this->assertEquals('{"name":"Aaron"}', (string) $person);
    }

    /** @test */
    public function null_value_returns_an_empty_json_object()
    {
        $doc = new Document();

        $this->assertEquals('{}', $doc->toJson());
    }
}