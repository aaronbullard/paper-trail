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

    /** @test */
    public function it_converts_to_a_array_object_mix()
    {
        $doc = new Document([
            'name' => 'Aaron',
            'framworks' => ['laravel', 'symfony']
        ]);

        $obj = $doc->toObject();

        $this->assertInstanceof(\StdClass::class, $obj);
        $this->assertTrue(property_exists($obj, 'name'));
    }

    /** @test */
    public function it_returns_original_input()
    {
        $doc = Document::create((object)[
            'array' => [
                'one' => 1,
                'two' => 2
            ],
            'obj' => (object)[
                'one' => 1,
                'two' => 2
            ]
        ]);

        $this->assertInstanceOf(\StdClass::class, $doc->getInput());
    }

     /** @test */
     public function it_can_access_props_publicly()
     {
         $doc = Document::create((object)[
             'array' => [
                 'one' => 1,
                 'two' => 2
             ],
             'obj' => (object)[
                 'one' => 1,
                 'two' => 2
             ]
         ]);
 
         $doc->obj->one = 2;

         $this->assertEquals(2, $doc->getInput()->obj->one);
     }

}