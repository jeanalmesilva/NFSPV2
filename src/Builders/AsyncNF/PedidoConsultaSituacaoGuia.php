<?php

namespace NFSPV2\Builders\AsyncNF;

use NFSPV2\Builders\NfAbstract;
use NFSPV2\Constants\Methods\NfAsyncMethods;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Constants\Requests\SimpleFieldsEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\General;
use NFSPV2\Helpers\Xml;

class  PedidoConsultaSituacaoGuia extends NfAbstract
{
    public function makeXmlRequest(BaseInformation $information, $params = null)
    {
        $request = [];
        $request[HeaderEnum::CPFCNPJ_SENDER] = $this->getDocument($information);
        $request[SimpleFieldsEnum::PROTOCOL_NUMBER] = General::getKey($params, SimpleFieldsEnum::PROTOCOL_NUMBER);

        return Xml::makeRequestXML(NfAsyncMethods::CONSULTA_SITUACAO_GUIA, $request);
    }
}