<?php

namespace PhpJsonVersioning;

use PhpSchema\Models\SchemaModel;
use PhpSchema\Traits\MethodAccess;

class Commit extends SchemaModel
{
    use MethodAccess;

    protected static $schema = ['$ref' => 'file://' . __DIR__ . '/../schemas/commit.json'];

    public function __construct(int $version, Patch $patch, int $timestamp, string $comment = null)
    {
        parent::__construct(compact('version', 'patch', 'timestamp', 'comment'));
    }

    public static function create(int $version, Patch $patch, string $comment = null): Commit
    {
        $timestamp = time();

        return new static($version, $patch, $timestamp, $comment);
    }

    public function version(): int
    {
        return $this->containerGet('version');
    }

    public function timestamp(): int
    {
        return $this->containerGet('timestamp');
    }
}