<?php

namespace NFSPV2\Builders\AsyncNF;

use NFSPV2\Builders\NfAbstract;
use NFSPV2\Constants\Methods\NfAsyncMethods;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Constants\Requests\SimpleFieldsEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\General;
use NFSPV2\Helpers\Xml;

class  PedidoConsultaGuia extends NfAbstract
{
    public function makeXmlRequest(BaseInformation $information, $params = null)
    {
        $request = [];
        $request[HeaderEnum::CPFCNPJ_SENDER] = $this->getDocument($information);
        $request[SimpleFieldsEnum::IM_PROVIDER] = $information->getIm();
        $request[SimpleFieldsEnum::INCIDENCE] = General::getKey($params, SimpleFieldsEnum::INCIDENCE);
        $request[SimpleFieldsEnum::SITUATION] = General::getKey($params, SimpleFieldsEnum::SITUATION);

        return Xml::makeRequestXML(NfAsyncMethods::CONSULTA_GUIA, $request);
    }
}