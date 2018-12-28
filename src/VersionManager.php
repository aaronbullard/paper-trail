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
        $this->record = Record::create();
    }

    public function load(Record $record): VersionManager
    {
        $this->record = $record;

        $this->buildVersions();

        return $this;
    }

    protected function buildVersions(): void
    {
        $src = new Document();

        foreach($this->record->commits() as $index => $commit){
            $doc = $this->patcher->apply($src, $commit->patch());

            $this->versions[$index] = [
                'version' => $commit->version(),
                'timestamp' => $commit->timestamp(),
                'comment' => $commit->comment(),
                'document' => $doc->toArray()
            ];

            $src = $doc;
        }
    }

    public function save(Document $doc, string $comment = null): VersionManager
    {
        $patch = $this->patcher->diff($this->getLatest(), $doc);

        $this->record->createCommit($patch, $comment);

        $this->buildVersions();

        return $this;
    }

    public function getVersion(int $version): Document
    {
        if(count($this->versions)){
            return new Document($this->versions[($version - 1)]['document']);
        }

        return new Document();
    }

    public function getLatest(): Document
    {
        return $this->getVersion(count($this->record->commits()));
    }

    public function getRecord(): Record
    {
        return $this->record;
    }

    public function getHistory(): array
    {
        return $this->versions;
    }
}