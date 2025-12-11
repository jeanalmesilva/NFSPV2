<?php

namespace NFSPV2\Entities\Requests\NF;

use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Constants\Requests\RpsEnum;
use NFSPV2\Contracts\UserRequest;
use NFSPV2\Helpers\General;

class Lot implements UserRequest
{
    private $transacao;
    private $dtInicio;
    private $dtFim;
    private $qtdRPS;
    private $rpsList;

    public function __construct()
    {
        $this->setTransacao(true);
        $this->setDtInicio(date('Y-m-d'));
        $this->setDtFim(date('Y-m-d'));
    }

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
    public function getDtInicio()
    {
        return $this->dtInicio;
    }

    /**
     * @param mixed $dtInicio
     */
    public function setDtInicio($dtInicio)
    {
        $this->dtInicio = General::filterDate($dtInicio);
    }

    /**
     * @return mixed
     */
    public function getDtFim()
    {
        return $this->dtFim;
    }

    /**
     * @param mixed $dtFim
     */
    public function setDtFim($dtFim)
    {
        $this->dtFim = General::filterDate($dtFim);
    }

    /**
     * @return mixed
     */
    public function getQtdRPS()
    {
        return $this->qtdRPS;
    }

    /**
     * @param mixed $qtdRPS
     */
    public function setQtdRPS($qtdRPS)
    {
        $this->qtdRPS = $qtdRPS;
    }



    /**
     * @return mixed
     */
    public function getRpsList()
    {
        return $this->rpsList;
    }

    /**
     * @param mixed $rpsList
     */
    public function setRpsList(array $rpsList)
    {
        // $valorTotalServicos = 0;
        // $valorTotalDeducoes = 0;
        $startDate = $this->getDtInicio();
        foreach ($rpsList as $rps) {
            if ($rps instanceof Rps) {
                $emission = $rps->getDataEmissao();
                $startDate = strtotime($emission) < strtotime($startDate) ? $emission : $startDate;
            }
        }

        $this->qtdRPS = count($rpsList);
        $this->setDtInicio($startDate);
        $this->rpsList = $rpsList;
    }

    public function toArray()
    {
        return [
            HeaderEnum::TRANSACTION => $this->transacao,
            HeaderEnum::START_DATE => $this->dtInicio,
            HeaderEnum::END_DATE => $this->dtFim,
            HeaderEnum::RPS_COUNT => $this->qtdRPS,
            RpsEnum::RPS => $this->rpsList,
        ];
    }
}
