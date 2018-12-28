<?php

namespace PhpJsonVersioning;

use DomainException;
use PhpJsonVersioning\Contracts\Patcher;

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

    public function save(Document $doc, string $comment = null): VersionManager
    {
        $patch = $this->patcher->diff($this->getLatest(), $doc);

        $this->commits[] = Commit::create($patch, $comment);

        $this->buildVersions();

        return $this;
    }

    public function getLatest(): Document
    {
        return $this->getVersion(count($this->commits));
    }

    public function getVersion(int $version): Document
    {
        return $this->versions[($version - 1)]['document'];
    }

    public function getHistory(): array
    {
        return $this->versions;
    }

    protected function buildVersions(): void
    {
        $src = new Document();

        foreach($this->commits as $index => $commit){
            $dst = $this->patcher->apply($src, $commit->patch());

            $this->versions[$index] = [
                'version' => $index + 1,
                'timestamp' => $commit->timestamp(),
                'comment' => $commit->comment(),
                'document' => $dst
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