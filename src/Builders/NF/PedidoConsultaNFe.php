<?php

namespace NFSPV2\Builders\NF;

use NFSPV2\Builders\NfAbstract;
use NFSPV2\Constants\Methods\NfMethods;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\Xml;
use NFSPV2\Validators\DetailValidator;

class PedidoConsultaNFe extends NfAbstract
{

    public function makeXmlRequest(BaseInformation $information, $params)
    {
        $params = DetailValidator::queryDetail($information, $params);
        $header = $this->makeHeader($information, [
            HeaderEnum::CPFCNPJ_SENDER => true
        ]);
        $detail = $this->makeDetail($information, $params);

        $request = array_merge($header, $detail);

        return Xml::makeRequestXML(NfMethods::CONSULTA, $request);
    }

}