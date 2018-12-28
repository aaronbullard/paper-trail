<?php

namespace PhpJsonVersioning;

use ArrayAccess, Iterator;
use PhpSchema\Traits\Loopable;
use PhpSchema\Models\SchemaModel;
use PhpSchema\Traits\ArrayAccessible;
// use PhpSchema\Traits\MethodAccess;
class Record extends SchemaModel implements ArrayAccess, Iterator
{
    use ArrayAccessible, Loopable;

    protected static $schema = ['$ref' => 'file://' . __DIR__ . '/../schemas/record.json'];

    protected function __construct(Commit ...$commits)
    {
        parent::__construct($commits);
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
        }, $arr);

        return new static(...$commits);
    }
}