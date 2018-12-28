<?php

namespace PhpJsonVersioning;

class Record
{
    protected $commits;
    
    protected function __construct(Commits ...$commits)
    {
        $this->commits = $commits;
    }

    public static function fromJson(string $record)
    {
        $arr = json_decode($record, 1);
        $commits = [];

        foreach($arr as $commit){

        }


    }
}