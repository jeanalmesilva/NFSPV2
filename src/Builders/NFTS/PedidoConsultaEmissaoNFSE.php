<?php

namespace NFSPV2\Builders\NFTS;

use NFSPV2\Builders\NftsAbstract;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\Xml;

class  PedidoConsultaEmissaoNFSE extends NftsAbstract
{
    public function makeXmlRequest(BaseInformation $information, $params)
    {

        $header = $this->makeHeader($information, [
            HeaderEnum::SENDER => true,
        ]);

        $detail = $this->makeDetailEmission($information, $params);

        $request = array_merge($header, $detail);

        return Xml::makeNFTSRequestXML('ConsultaEmissaoNFSE', $request);
    }

}