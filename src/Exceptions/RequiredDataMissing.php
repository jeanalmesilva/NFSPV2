<?php

namespace NFSPV2\Exceptions;

class RequiredDataMissing extends \Exception
{
    public function __construct($field = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('Missing Param: ' . $field, $code, $previous);
    }
}