<?php

namespace NFSPV2\Builders\NFTS;

use NFSPV2\Builders\NftsAbstract;
use NFSPV2\Constants\Methods\NftsMethods;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Constants\Requests\NftsEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\General;
use NFSPV2\Helpers\Xml;
use NFSPV2\Validators\NftsValidator;

class PedidoEnvioLoteNFTS extends NftsAbstract
{

    public function makeXmlRequest(BaseInformation $information, $lot)
    {
        $documents = NftsValidator::validateRequest($information, General::getKey($lot, NftsEnum::NFTS));
        $header = $this->makeHeader($information, [
            HeaderEnum::SENDER => true,
            HeaderEnum::TRANSACTION => General::getKey($lot, HeaderEnum::TRANSACTION),
            HeaderEnum::START_DATE => General::getKey($lot, HeaderEnum::START_DATE),
            HeaderEnum::END_DATE => General::getKey($lot, HeaderEnum::END_DATE),
            HeaderEnum::NFTS_COUNT => General::getKey($lot, HeaderEnum::NFTS_COUNT),
            HeaderEnum::SERVICES_TOTAL => General::getKey($lot, HeaderEnum::SERVICES_TOTAL),
            HeaderEnum::DEDUCTION_TOTAL => General::getKey($lot, HeaderEnum::DEDUCTION_TOTAL),
        ]);
        $allNfts = $this->makeNFTS($information, $documents);

        $request = array_merge($header, $allNfts);

        return Xml::makeNFTSRequestXML(NftsMethods::ENVIO_LOTE, $request);
    }
}