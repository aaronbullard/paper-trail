<?php

namespace PhpJsonVersioning;

use PhpSchema\Models\SchemaModel;
use PhpSchema\Traits\MethodAccess;

class Commit extends SchemaModel
{
    use MethodAccess;

    protected static $schema = ['$ref' => 'file://' . __DIR__ . '/../schemas/commit.json'];

    public function __construct(Patch $patch, int $timestamp, string $comment = null)
    {
        parent::__construct(compact('patch', 'timestamp', 'comment'));
    }

    public static function create(Patch $patch, string $comment = null): Commit
    {
        $timestamp = time();

        return new static($patch, $timestamp, $comment);
    }

    public static function fromJson(string $commit): Commit
    {
        $json = new Json($commit);

        $obj = $json->toObject();

        return new static(new Patch($obj->patch), $obj->timestamp, $obj->comment);
    }
}