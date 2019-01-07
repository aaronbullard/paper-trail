<?php

namespace PhpJsonVersioning\Exceptions;

class PatchingException extends \RuntimeException
{
    public static function from(\Throwable $e)
    {
        return new static($e->getMessage(), $e->getCode(), $e);
    }
}