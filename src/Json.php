<?php

namespace PhpJsonVersioning;

use PhpJsonVersioning\Exceptions\JsonException;

class Json
{
    protected $json;

    public function __construct(string $json = '{}')
    {
        static::validate($json);
        $this->json = $json;
    }

    public static function create(string $json = null): Json
    {
        return new static($json);
    }

    public static function fromArray(array $assoc): Json
    {
        return new static(json_encode($assoc));
    }

    protected static function validate(string $json)
    {
        @json_decode($json);
        if(json_last_error()){
            throw new JsonException(json_last_error_msg());
        }
    }

    public function toJson(): string
    {
        return $this->json;
    }

    public function toObject()
    {
        return json_decode($this->toJson());
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