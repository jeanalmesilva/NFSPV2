<?php

namespace NFSPV2\Entities\Requests\NFTS;

use NFSPV2\Constants\Requests\SimpleFieldsEnum;
use NFSPV2\Contracts\UserRequest;
use NFSPV2\Helpers\General;

class Document implements UserRequest
{
    public $cpf;
    public $cnpj;

    public function toArray()
    {
        return [
            SimpleFieldsEnum::CPF => $this->getCpf(),
            SimpleFieldsEnum::CNPJ => $this->getCnpj()
        ];
    }

    /**
     * @return mixed
     */
    public function getCpf()
    {
        return $this->cpf;
    }

    /**
     * @param mixed $cpf
     */
    public function setCpf($cpf)
    {
        $this->cpf = sprintf('%011s',General::onlyNumbers($cpf));
    }

    /**
     * @return mixed
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * @param mixed $cnpj
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = sprintf('%014s',General::regexCnpj($cnpj));
    }
}