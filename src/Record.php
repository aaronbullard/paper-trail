<?php

namespace PhpJsonVersioning;

use DomainException;
use PhpSchema\Models\SchemaModel;
use PhpSchema\Traits\MethodAccess;
class Record extends SchemaModel
{
    use MethodAccess;

    protected static $schema = ['$ref' => 'file://' . __DIR__ . '/../schemas/record.json'];

    protected function __construct(Commit ...$commits)
    {
        parent::__construct(compact('commits'));
    }

    public static function create(array $commits): Record
    {
        return new static(...$commits);
    }

    public static function fromJson(string $record): Record
    {
        $arr = json_decode($record, 1);
    
        $commits = array_map(function($commit){
            return new Commit(
                new Patch($commit['patch']),
                $commit['timestamp'],
                $commit['comment']
            );
        }, $arr['commits']);

        return new static(...$commits);
    }

    public function addCommit(Commit $commit): Record
    {
        $this->commits()[] = $commit;

        return $this;
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

    /**
     * Validate the instance
     *
     * @throws ValidationException
     * @return void
     */
    public function validate(): void
    {
        try {
            static::validateIsSorted(...$this->commits());
        } catch (DomainException $e) {
            $this->commits(
                static::sortCommits(...$this->commits())
            );
        }

        parent::validate();
    }
}