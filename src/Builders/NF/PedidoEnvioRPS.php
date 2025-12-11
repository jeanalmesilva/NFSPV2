<?php

namespace NFSPV2\Builders\NF;

use NFSPV2\Builders\NfAbstract;
use NFSPV2\Constants\Methods\NfMethods;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\Xml;
use NFSPV2\Validators\RpsValidator;

class PedidoEnvioRPS extends NfAbstract
{

    public function makeXmlRequest(BaseInformation $information, $rps)
    {
        $documents = RpsValidator::validateRps($information, $rps);
        $header = $this->makeHeader($information, [
            HeaderEnum::CPFCNPJ_SENDER => true
        ]);
        $allRps = $this->makeRPS($information, $documents);

        $request = array_merge($header, $allRps);

        return Xml::makeRequestXML(NfMethods::ENVIO, $request);
    }

}