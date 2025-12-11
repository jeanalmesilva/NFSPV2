<?php

namespace NFSPV2\Builders\NFTS;

use NFSPV2\Builders\NftsAbstract;
use NFSPV2\Constants\Methods\NfMethods;
use NFSPV2\Constants\Requests\DetailEnum;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\Certificate;
use NFSPV2\Helpers\Xml;
use NFSPV2\Validators\DetailValidator;

class PedidoCancelamentoNFTS extends NftsAbstract
{
    public function makeXmlRequest(BaseInformation $information, $params)
    {
        $params = DetailValidator::queryDetail($information, $params);
        $header = $this->makeHeader($information, [
            HeaderEnum::SENDER => true,
            HeaderEnum::TRANSACTION => 'false'
        ]);

        foreach ($params as $key => $document) {
            $params[$key][DetailEnum::CANCELLATION_SIGN] = Certificate::cancelSignatureString($document);
        }

        $detail = $this->makeDetail($information, $params);

        $request = array_merge($header, $detail);

        return Xml::makeNFTSRequestXML(NfMethods::CANCELAMENTO, $request);
    }
}