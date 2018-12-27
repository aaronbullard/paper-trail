<?php

namespace PhpJsonVersioning;

use PhpSchema\Models\SchemaModel;

class Patch extends SchemaModel
{
    protected static $schema = ['$ref' => 'file://' . __DIR__ . '/../schemas/json-patch.json'];

    public function __construct(array $patch)
    {
        parent::__construct($patch);
    }

    public static function fromJson(string $patch): Patch
    {
        return new static(json_decode($patch, 1));
    }
}