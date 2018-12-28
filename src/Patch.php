<?php

namespace PhpJsonVersioning;

use ArrayAccess, Iterator;
use PhpSchema\Traits\Loopable;
use PhpSchema\Models\SchemaModel;
use PhpSchema\Traits\ArrayAccessible;

class Patch extends SchemaModel implements Iterator, ArrayAccess
{
    use Loopable, ArrayAccessible;

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