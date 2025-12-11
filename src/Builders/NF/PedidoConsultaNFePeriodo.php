<?php

namespace NFSPV2\Builders\NF;

use NFSPV2\Builders\NfAbstract;
use NFSPV2\Constants\Methods\NfMethods;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Constants\Requests\SimpleFieldsEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\General;
use NFSPV2\Helpers\Xml;

class  PedidoConsultaNFePeriodo extends NfAbstract
{
    public function makeXmlRequest(BaseInformation $information, $params)
    {
        if(isset($params[HeaderEnum::TRANSACTION])){
            unset($params[HeaderEnum::TRANSACTION]);
        }

        $extra = [
            HeaderEnum::CPFCNPJ_SENDER => true,
            HeaderEnum::CPFCNPJ => true
        ];
        if (
            !General::getKey($params, SimpleFieldsEnum::CNPJ) &&
            !General::getKey($params, SimpleFieldsEnum::CPF) &&
            !General::getKey($params, HeaderEnum::IM)
        ) {
            $extra[SimpleFieldsEnum::CNPJ] = $information->getCnpj();
            $extra[HeaderEnum::IM] = $information->getIm();
        }
        $request = $this->makeHeader($information, array_merge($extra, $params));

        return Xml::makeRequestXML(NfMethods::CONSULTA_NFE_PERIODO, $request);
    }

}