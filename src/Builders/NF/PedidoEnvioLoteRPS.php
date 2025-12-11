<?php

namespace NFSPV2\Builders\NF;

use NFSPV2\Builders\NfAbstract;
use NFSPV2\Constants\Methods\NfMethods;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Constants\Requests\RpsEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Entities\Requests\NF\Lot;
use NFSPV2\Helpers\General;
use NFSPV2\Helpers\Xml;
use NFSPV2\Validators\RpsValidator;

class PedidoEnvioLoteRPS extends NfAbstract
{

    public function makeXmlRequest(BaseInformation $information, $lot)
    {
        if ($lot instanceof Lot)
            $lot = $lot->toArray();

        $documents = RpsValidator::validateRps($information, General::getKey($lot, RpsEnum::RPS));
        $header = $this->makeHeader($information, [
            HeaderEnum::CPFCNPJ_SENDER => true,
            HeaderEnum::TRANSACTION => General::getKey($lot, HeaderEnum::TRANSACTION),
            HeaderEnum::START_DATE => General::getKey($lot, HeaderEnum::START_DATE),
            HeaderEnum::END_DATE => General::getKey($lot, HeaderEnum::END_DATE),
            HeaderEnum::RPS_COUNT => General::getKey($lot, HeaderEnum::RPS_COUNT),
            //HeaderEnum::SERVICES_TOTAL => General::getKey($lot, HeaderEnum::SERVICES_TOTAL),
           // HeaderEnum::DEDUCTION_TOTAL => General::getKey($lot, HeaderEnum::DEDUCTION_TOTAL),
        ]);
        $allRps = $this->makeRPS($information, $documents);

        $request = array_merge($header, $allRps);

        return Xml::makeRequestXML(NfMethods::ENVIO_LOTE, $request);
    }
}