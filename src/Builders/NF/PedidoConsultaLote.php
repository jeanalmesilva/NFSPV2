<?php

namespace NFSPV2\Builders\NF;

use NFSPV2\Builders\NfAbstract;
use NFSPV2\Constants\Methods\NfMethods;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\Xml;

class  PedidoConsultaLote extends NfAbstract
{
    public function makeXmlRequest(BaseInformation $information, $lot)
    {

        $request = $this->makeHeader($information, [
            HeaderEnum::CPFCNPJ_SENDER => true,
            HeaderEnum::LOT_NUMBER => $lot[HeaderEnum::LOT_NUMBER]
        ]);
        return Xml::makeRequestXML(NfMethods::CONSULTA_LOTE, $request);
    }

}