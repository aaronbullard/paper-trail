<?php

namespace PaperTrail;

class Document extends \ArrayObject
{
    protected $is_array;

    public function __construct($input = [])
    {
        $this->is_array = is_array($input);
        
        parent::__construct($input, self::ARRAY_AS_PROPS);
    }

    public static function create($doc = null): Document
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

    public function toObject()
    {
        return json_decode($this->toJson());
    }

    public function getInput()
    {
       return $this->is_array ? $this->toArray() : (object) $this->toArray();
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