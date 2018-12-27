<?php

namespace PhpJsonVersioning\Contracts;

use PhpJsonVersioning\Json;
use PhpJsonVersioning\Patch;

interface Patcher
{
    public function apply(Json $src, Patch $patch): Json;

    public function diff(Json $src, Json $dst): Patch;
}