<?php

namespace PhpJsonVersioning\Services;

use Swaggest\JsonDiff\JsonDiff as SwaggestJsonDiff;
use Swaggest\JsonDiff\JsonPatch as SwaggestJsonPatch;
use PhpJsonVersioning\Patch;
use PhpJsonVersioning\Document;
use PhpJsonVersioning\Contracts\Patcher;

class JsonPatch implements Patcher
{
    public function apply(Document $src, Patch $patch): Document
    {
        $patch = SwaggestJsonPatch::import($patch->toArray());

        $clone = $src->toArray();

        $patch->apply($clone);

        return new Document($clone);
    }

    public function diff(Document $src, Document $dst): Patch
    {
        $diff = new SwaggestJsonDiff(
            $src->toArray(),
            $dst->toArray()
        );

        return new Patch($diff->getPatch()->jsonSerialize());
    }
}