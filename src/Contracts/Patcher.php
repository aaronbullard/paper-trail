<?php

namespace PhpJsonVersioning\Contracts;

use PhpJsonVersioning\Patch;
use PhpJsonVersioning\Document;

interface Patcher
{
    public function apply(Document $src, Patch $patch): Document;

    public function diff(Document $src, Document $dst): Patch;
}