<?php

namespace NFSPV2\Builders\NF;

use NFSPV2\Builders\NfAbstract;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Constants\Requests\SimpleFieldsEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\Xml;

class  PedidoInformacoesLote extends NfAbstract
{
    public function makeXmlRequest(BaseInformation $information, $im = null)
    {
        $im = !empty($im) ? $im : $information->getIm();

        $request = $this->makeHeader($information, [
            HeaderEnum::CPFCNPJ_SENDER => true,
            SimpleFieldsEnum::IM_PROVIDER => $im,

        ]);

        return Xml::makeRequestXML('InformacoesLote', $request);
    }

}