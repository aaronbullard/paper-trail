<?php

namespace PhpJsonVersioning;

use DomainException;
use PhpSchema\Traits\MethodAccess;
class Record extends SchemaModel
{
    use MethodAccess;

    protected static $schema = ['$ref' => 'file://' . __DIR__ . '/../schemas/record.json'];

    protected function __construct(Commit ...$commits)
    {
        parent::__construct(compact('commits'));
    }

    public static function create(array $commits = []): Record
    {
        return new static(...$commits);
    }

    public static function fromJson(string $record): Record
    {
        $arr = json_decode($record, 1);
    
        $commits = array_map(function($commit){
            return new Commit(
                $commit['version'],
                new Patch($commit['patch']),
                $commit['timestamp'],
                $commit['comment']
            );
        }, $arr['commits']);

        return new static(...$commits);
    }

    public function createCommit(Patch $patch, $comment = null): Commit
    {
        $version = count($this->commits()) + 1;

        $commit = Commit::create($version, $patch, $comment);

        $this->commits()[] = $commit;

        return $commit;
    }

    protected static function validateIsSorted(Commit ...$commits): void
    {
        $lastVersion = 0;

        foreach($commits as $commit)
        {
            $version = $commit->version();

            if($version < $lastVersion){
                throw new DomainException("Commits are out of order");
            }

            $lastVersion = $version;
        }
    }

    protected static function sortCommits(Commit ...$commits): array
    {
        usort($commits, function($a, $b){
            return ($a->version() < $b->version()) ? -1 : 1;
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