<?php

namespace PaperTrail;

use ArrayAccess, Iterator;
use PhpSchema\Traits\Loopable;
use PhpSchema\Traits\ArrayAccessible;

class Patch extends SchemaModel implements Iterator, ArrayAccess
{
    use Loopable, ArrayAccessible;

    protected static $schema = ['$ref' => 'file://' . __DIR__ . '/../schemas/patch.json'];

    public function __construct(array $patch)
    {
        parent::__construct($patch);
    }
}