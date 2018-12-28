<?php

namespace PhpJsonVersioning;

class Document extends \ArrayObject
{
    public static function create(array $doc = null): Document
    {
        return new static($doc);
    }

    public function toArray(): array
    {
        return $this->getArrayCopy();
    }

    public function toJson(): string
    {
        return json_encode($this);
    }

    /**
     * Returns JSON
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }
}