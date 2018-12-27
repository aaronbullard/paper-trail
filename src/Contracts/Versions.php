<?php

namespace PhpJsonVersioning\Contracts;

use PhpJsonVersioning\Json;

interface Versions
{
    public function save(Json $json, string $comment = null): int;

    public function getCurrentVersion(): int;

    public function getVersion(int $version): Json;

    public function getVersions(): array;

    public function getHistory(): array;

    public function getLatest(): Json;

    public function getCommitRecord(): Record;
}