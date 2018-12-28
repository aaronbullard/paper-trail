<?php

namespace PhpJsonVersioning;

use DomainException;
use PhpJsonVersioning\Contracts\Patcher;
use PhpJsonVersioning\Exceptions\JsonException;

class VersionManager
{
    protected $patcher;

    protected $commits = [];

    protected $versions = [];

    public function __construct(Patcher $patcher)
    {
        $this->patcher = $patcher;
    }

    public function load(Commit ...$commits): VersionManager
    {
        try {
            static::validateIsSorted(...$commits);
        } catch (DomainException $e) {
            $commits = static::sortCommits(...$commits);
        }
        
        $this->commits = $commits;

        $this->buildVersions();

        return $this;
    }

    public function save(Json $json, string $comment = null): VersionManager
    {
        $patch = $this->patcher->diff($this->getLatest(), $json);

        $this->commits[] = Commit::create($patch, $comment);

        $this->buildVersions();

        return $this;
    }

    public function getLatest(): Json
    {
        return $this->getVersion(count($this->commits));
    }

    public function getVersion(int $version): Json
    {
        return $this->versions[($version - 1)]['json'];
    }

    public function getHistory(): array
    {
        return $this->versions;
    }

    protected function buildVersions(): void
    {
        $src = new Json();

        foreach($this->commits as $index => $commit){
            $dst = $this->patcher->apply($src, $commit->patch());

            $this->versions[$index] = [
                'version' => $index + 1,
                'timestamp' => $commit->timestamp(),
                'comment' => $commit->comment(),
                'json' => $dst
            ];

            $src = $dst;
        }
    }

    protected static function validateIsSorted(Commit ...$commits): void
    {
        $lastTimestamp = 0;

        foreach($commits as $commit)
        {
            $timestamp = $commit->timestamp();

            if($timestamp < $lastTimestamp){
                throw new DomainException("Commits are out of order");
            }

            $lastTimestamp = $timestamp;
        }
    }

    protected static function sortCommits(Commit ...$commits): array
    {
        usort($commits, function($a, $b){
            return ($a->timestamp() < $b->timestamp()) ? -1 : 1;
        });

        return $commits;
    }
}