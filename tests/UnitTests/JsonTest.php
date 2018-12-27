<?php

namespace PhpJsonVersioning\Tests\UnitTests;

use PhpJsonVersioning\Tests\TestCase;
use PhpJsonVersioning\Json;
use PhpJsonVersioning\Exceptions\JsonException;

class JsonTest extends TestCase
{
    /** @test */
    public function it_validates_json()
    {
        $person = new Json('{"name":"Aaron"}');

        $this->expectException(JsonException::class);
        new Json('foobar');
    }

    /** @test */
    public function it_converts_to_string()
    {
        $json = '{"name":"Aaron"}';
        $person = new Json($json);

        $this->assertEquals($json,(string) $person);
    }

    /** @test */
    public function it_has_a_static_constructor()
    {
        $json = '{"name":"Aaron"}';
        $person = Json::create($json);

        $this->assertEquals($json, (string) $person);
    }

    /** @test */
    public function null_value_returns_an_empty_json_object()
    {
        $json = new Json();

        $this->assertEquals('{}', (string) $json);
    }
}