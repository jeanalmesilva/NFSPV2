<?php

namespace NFSPV2\Builders\NF;

use NFSPV2\Builders\NfAbstract;
use NFSPV2\Constants\Methods\NfMethods;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\General;
use NFSPV2\Helpers\Xml;

class  PedidoConsultaCNPJ extends NfAbstract
{
    public function makeXmlRequest(BaseInformation $information, $params = null)
    {

        $header = $this->makeHeader($information, [
            HeaderEnum::CPFCNPJ_SENDER => true
        ]);
        if(isset($params['document'])){
            $taxPayer = $this->makeTaxPayerInformation(General::onlyNumbers($params['document']));
        }else{
            $taxPayer = $this->makeTaxPayerInformation($information->getCnpj()? $information->getCnpj() : $information->getCpf());
        }
        $request = array_merge($header, $taxPayer);

        return Xml::makeRequestXML(NfMethods::CONSULTA_CNPJ, $request);
    }
}