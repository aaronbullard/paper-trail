<?php

namespace PhpJsonVersioning;

use PhpJsonVersioning\Exceptions\JsonException;

class VersionManager
{
    protected $commits;

    protected $patcher;

    public function __construct(CommitCollection $commits, JsonPatch $patcher)
    {
        $this->commits = $commits;
        $this->patcher = $patcher;
    }

    public function save(Json $json)
    {

    }
}