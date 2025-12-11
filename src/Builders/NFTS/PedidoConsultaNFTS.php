<?php

namespace NFSPV2\Builders\NFTS;

use NFSPV2\Builders\NftsAbstract;
use NFSPV2\Constants\Methods\NftsMethods;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\Xml;
use NFSPV2\Validators\DetailValidator;

class  PedidoConsultaNFTS extends NftsAbstract
{
    public function makeXmlRequest(BaseInformation $information, $params)
    {
        $params = DetailValidator::queryDetail($information, $params);
        $header = $this->makeHeader($information, [
            HeaderEnum::SENDER => true,
        ]);

        $detail = $this->makeDetail($information, $params);
        $request = array_merge($header, $detail);
        return Xml::makeNFTSRequestXML(NftsMethods::CONSULTA, $request);
    }

}