<?php

namespace PhpJsonVersioning\Services;

use Swaggest\JsonDiff\JsonDiff as SwaggestJsonDiff;
use Swaggest\JsonDiff\JsonPatch as SwaggestJsonPatch;
use PhpJsonVersioning\Json;
use PhpJsonVersioning\Patch;
use PhpJsonVersioning\Contracts\Patcher;

class JsonPatch implements Patcher
{
    public function apply(Json $src, Patch $patch): Json
    {
        $patch = SwaggestJsonPatch::import($patch->toObject());

        $clone = $src->toObject();

        $patch->apply($clone);

        return new Json(json_encode($clone));
    }

    public function diff(Json $src, Json $dst): Patch
    {
        $diff = new SwaggestJsonDiff(
            $src->toObject(),
            $dst->toObject()
        );

        return new Patch($diff->getPatch()->jsonSerialize());
    }
}