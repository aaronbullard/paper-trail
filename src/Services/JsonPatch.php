<?php

namespace PhpJsonVersioning\Services;

use Swaggest\JsonDiff\JsonDiff as SwaggestJsonDiff;
use Swaggest\JsonDiff\JsonPatch as SwaggestJsonPatch;
use PhpJsonVersioning\Patch;
use PhpJsonVersioning\Document;
use PhpJsonVersioning\Contracts\Patcher;
use PhpJsonVersioning\Exceptions\PatchingException;

class JsonPatch implements Patcher
{
    public function apply(Document $src, Patch $patch): Document
    {
        try {
            $jsonpatch = SwaggestJsonPatch::import($patch->toArray());

            $clone = $src->getInput();

            $jsonpatch->apply($clone);
            
            return new Document($clone);
        } catch (\Throwable $e) {
            throw PatchingException::from($e);
        }
    }

    public function diff(Document $src, Document $dst): Patch
    {
        try {
            $diff = new SwaggestJsonDiff(
                $src->getInput(),
                $dst->getInput()
            );
        } catch (\Throwable $e) {
            throw PatchingException::from($e);
        }
        
        return new Patch($diff->getPatch()->jsonSerialize());
    }
}