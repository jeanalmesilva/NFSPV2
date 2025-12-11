<?php

namespace NFSPV2\Entities\Requests\NFTS;

use NFSPV2\Constants\Requests\DetailEnum;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Constants\Requests\SimpleFieldsEnum;
use NFSPV2\Contracts\UserRequest;

class NftsSearch implements UserRequest
{

    public $transacao;
    public $inscricaoMunicipal;
    public $numeroNFTS;

    /**
     * @return mixed
     */
    public function getTransacao()
    {
        return $this->transacao;
    }

    /**
     * @param mixed $transacao
     */
    public function setTransacao($transacao)
    {
        $this->transacao = $transacao;
    }

    /**
     * @return mixed
     */
    public function getInscricaoMunicipal()
    {
        return $this->inscricaoMunicipal;
    }

    /**
     * @param mixed $inscricaoMunicipal
     */
    public function setInscricaoMunicipal($inscricaoMunicipal)
    {
        $this->inscricaoMunicipal = $inscricaoMunicipal;
    }

    /**
     * @return mixed
     */
    public function getNumeroNFTS()
    {
        return $this->numeroNFTS;
    }

    /**
     * @param mixed $numeroNFTS
     */
    public function setNumeroNFTS($numeroNFTS)
    {
        $this->numeroNFTS = $numeroNFTS;
    }

    public function toArray()
    {
        return [
            HeaderEnum::TRANSACTION => $this->transacao,
            DetailEnum::IM => $this->inscricaoMunicipal,
            SimpleFieldsEnum::NFTS_NUMBER => $this->numeroNFTS
        ];
    }
}