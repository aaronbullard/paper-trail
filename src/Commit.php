<?php

namespace PhpJsonVersioning;

use PhpSchema\Models\SchemaModel;

class Commit extends SchemaModel
{
    protected static $schema = [
        "type" => "object",
        "properties" => [
            "version" => ["type" => "integer"],
            "timestamp" => ["type" => "integer"],
            "patch" => ['$ref' => 'file://' . __DIR__ . '/../schemas/json-patch.json'],
            "comment" => [
                "oneOf" => [
                    ["type" => "string"],
                    ["type" => "null"]
                ]
            ]
        ],
        "required" => ["version", "timestamp", "patch"]
    ];

    protected function __construct(int $version, int $timestamp, Patch $patch, string $comment = null)
    {
        parent::__construct(compact('version', 'timestamp', 'patch', 'comment'));
    }

    public static function create(int $version, Patch $patch, string $comment = null): Commit
    {
        $timestamp = time();

        return new static($version, $timestamp, $patch, $comment);
    }

    public static function fromJson(string $commit): Commit
    {
        $json = new Json($commit);

        $obj = $json->toObject();

        return new static($obj->version, $obj->timestamp, new Patch($obj->patch), $obj->comment);
    }

    public function setComment(string $comment): Commit
    {
        $this->containerSet('comment', $comment);

        return $this;
    }
}