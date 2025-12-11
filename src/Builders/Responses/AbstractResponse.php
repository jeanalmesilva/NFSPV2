<?php

namespace NFSPV2\Builders\Responses;

use NFSPV2\Contracts\OutputClass;
use NFSPV2\Helpers\General;

abstract class AbstractResponse implements OutputClass
{
    public $response;
    public $arrayResponse;

    public function checkSuccess()
    {
        return General::getPath($this->arrayResponse, 'Cabecalho.Sucesso');
    }
}