<?php

namespace NFSPV2\Builders\AsyncNF;

use NFSPV2\Builders\NfAbstract;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Constants\Requests\SimpleFieldsEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\General;
use NFSPV2\Helpers\Xml;

class  PedidoEmissaoGuiaAsync extends NfAbstract
{
    public function makeXmlRequest(BaseInformation $information, $params = null)
    {
        $request = [];
        $request[HeaderEnum::CPFCNPJ_SENDER] = $this->getDocument($information);
        $request[SimpleFieldsEnum::IM_PROVIDER] = $information->getIm();
        $request[SimpleFieldsEnum::EMISSION_TYPE] = General::getKey($params, SimpleFieldsEnum::EMISSION_TYPE);
        $request[SimpleFieldsEnum::INCIDENCE] = General::getKey($params, SimpleFieldsEnum::INCIDENCE);
        $request[SimpleFieldsEnum::PAYMENT_DATE] = General::getKey($params, SimpleFieldsEnum::PAYMENT_DATE);

        return Xml::makeRequestXML('EmissaoGuia', $request);
    }
}