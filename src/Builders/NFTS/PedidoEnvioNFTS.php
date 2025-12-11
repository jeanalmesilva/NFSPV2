<?php

namespace NFSPV2\Builders\NFTS;

use NFSPV2\Builders\NftsAbstract;
use NFSPV2\Constants\Methods\NftsMethods;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\Xml;
use NFSPV2\Validators\NftsValidator;

class PedidoEnvioNFTS extends NftsAbstract
{
    public function makeXmlRequest(BaseInformation $information, $rps)
    {
        $documents = NftsValidator::validateRequest($information, $rps);
        $header = $this->makeHeader($information, [
            HeaderEnum::SENDER => true
        ]);
        $allNfts = $this->makeNFTS($information, $documents);

        $request = array_merge($header, $allNfts);

        return Xml::makeNFTSRequestXML(NftsMethods::ENVIO, $request);
    }
}