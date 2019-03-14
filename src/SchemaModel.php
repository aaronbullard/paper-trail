<?php

namespace PaperTrail;

use PhpSchema\Traits\MethodAccess;
use PhpSchema\Models\SchemaModel as PhpSchemaModel;
use PaperTrail\Exceptions\ValidationException;
use PhpSchema\ValidationException as PhpSchemaValidationException;

class SchemaModel extends PhpSchemaModel
{
    public function validate(): void
    {
        try {
            parent::validate();
        } catch (PhpSchemaValidationException $e) {
            throw ValidationException::withErrors($e->getErrors());
        }
    }
}