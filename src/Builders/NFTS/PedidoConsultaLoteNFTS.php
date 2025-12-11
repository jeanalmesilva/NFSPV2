<?php

namespace NFSPV2\Builders\NFTS;

use NFSPV2\Builders\NftsAbstract;
use NFSPV2\Constants\Methods\NftsMethods;
use NFSPV2\Constants\Requests\DetailEnum;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\Xml;

class  PedidoConsultaLoteNFTS extends NftsAbstract
{
    public function makeXmlRequest(BaseInformation $information, $lot)
    {
        $header = $this->makeHeader($information, [
            HeaderEnum::SENDER => true,
        ]);

        $request = array_merge($header, [
            DetailEnum::DETAIL_LOT => $lot
        ]);
        return Xml::makeNFTSRequestXML(NftsMethods::CONSULTA_LOTE, $request);
    }

}