<?php

namespace PaperTrail;

use PaperTrail\Contracts\Patcher;
use PaperTrail\Services\JsonPatch;

class RecordStore
{
    protected $patcher;

    protected $record;

    protected $versions = [];

    public function __construct(Patcher $patcher, Record $record = null)
    {
        $this->patcher = $patcher;
        $this->record = $record ?? Record::create();

        $this->buildVersions();
    }

    public static function create(Record $record = null): RecordStore
    {
        return new static(new JsonPatch(), $record);
    }

    public static function fromJson(string $jsonRecord = null): RecordStore
    {
        return static::create(Record::fromJson($jsonRecord));
    }

    public function toJson(): string
    {
        return $this->getRecord()->toJson();
    }

    public function load(Record $record): RecordStore
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

    public function save($doc, string $comment = null): Record
    {
        if (false == $doc instanceof Document){
            $doc = Document::create($doc);
        }

        $patch = $this->patcher->diff($this->getLatest(), $doc);

        $this->record->createCommit($patch, $comment);

        $this->buildVersions();

        return $this->record;
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