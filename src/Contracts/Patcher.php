<?php

namespace PaperTrail\Contracts;

use PaperTrail\Patch;
use PaperTrail\Document;

interface Patcher
{
    public function apply(Document $src, Patch $patch): Document;

    public function diff(Document $src, Document $dst): Patch;
}