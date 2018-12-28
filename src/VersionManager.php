<?php

namespace PhpJsonVersioning;

use DomainException;
use PhpJsonVersioning\Contracts\Patcher;

class VersionManager
{
    protected $patcher;

    protected $record;

    protected $versions = [];

    public function __construct(Patcher $patcher)
    {
        $this->patcher = $patcher;
    }

    public function load(Record $record): VersionManager
    {
        $this->record = $record;

        $this->buildVersions();

        return $this;
    }

    public function save(Document $doc, string $comment = null): VersionManager
    {
        $patch = $this->patcher->diff($this->getLatest(), $doc);

        $commit = Commit::create($patch, $comment);

        $this->record->addCommit($commit);

        $this->buildVersions();

        return $this;
    }

    protected function buildVersions(): void
    {
        $src = new Document();

        foreach($this->record->commits() as $index => $commit){
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

    public function getVersion(int $version): Document
    {
        return $this->versions[($version - 1)]['document'];
    }

    public function getLatest(): Document
    {
        return $this->getVersion(count($this->record->commits()));
    }

    public function getHistory(): array
    {
        return $this->versions;
    }

    public function getRecord(): Record
    {
        return $this->record;
    }
}