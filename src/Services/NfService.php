<?php

namespace NFSPV2\Services;

use NFSPV2\Builders\AsyncNF\PedidoConsultaGuia;
use NFSPV2\Builders\AsyncNF\PedidoConsultaSituacaoGuia;
use NFSPV2\Builders\AsyncNF\PedidoConsultaSituacaoLote;
use NFSPV2\Builders\AsyncNF\PedidoEmissaoGuiaAsync;
use NFSPV2\Builders\NF\PedidoCancelamentoNFe;
use NFSPV2\Builders\NF\PedidoConsultaCNPJ;
use NFSPV2\Builders\NF\PedidoConsultaLote;
use NFSPV2\Builders\NF\PedidoConsultaNFe;
use NFSPV2\Builders\NF\PedidoConsultaNFePeriodo;
use NFSPV2\Builders\NF\PedidoEnvioLoteRPS;
use NFSPV2\Builders\NF\PedidoEnvioRPS;
use NFSPV2\Builders\NF\PedidoInformacoesLote;
use NFSPV2\Builders\Responses\BasicTransformerResponse;
use NFSPV2\Builders\Responses\CnpjInformationFactory;
use NFSPV2\Builders\WsdlBuilder;
use NFSPV2\Client\ApiClient;
use NFSPV2\Constants\Endpoints;
use NFSPV2\Constants\Methods\NfAsyncMethods;
use NFSPV2\Constants\Methods\NfMethods;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Constants\Requests\SimpleFieldsEnum;
use NFSPV2\Contracts\InputTransformer;
use NFSPV2\Contracts\OutputClass;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Entities\Requests\NF\NfSearch;
use NFSPV2\Helpers\General;

class NfService
{
    public $response;
    private $nfEndPoint;
    private $nfAsyncEndPoint;

    public function __construct()
    {
        $this->nfEndPoint = WsdlBuilder::make(Endpoints::NF);
        $this->nfAsyncEndPoint = WsdlBuilder::make(Endpoints::NF_ASYNC);
        $this->response = new BasicTransformerResponse();
    }

    /*
     *  NF METHODS
     */
    public function checkCNPJ(BaseInformation $baseInformation, $document = null)
    {
        $builder = new PedidoConsultaCNPJ;
        $outputClass = new CnpjInformationFactory;
        return $this->processRequest($baseInformation, ['document' => $document], NfMethods::CONSULTA_CNPJ, $builder, $outputClass);
    }

    private function processRequest(BaseInformation $information, $params, $method, InputTransformer $builder, OutputClass $outputClass = null)
    {
        // Check Output Type
        $outputClass = !empty($outputClass) ? $outputClass : $this->response;
        $params = General::convertUserRequest($params);

        //  File Without Signature
        $file = $builder->makeXmlRequest($information, $params);

        //Set Input file and sign
        $information->setXml($file);

        // Send to API,
        $output = ApiClient::send($this->nfEndPoint, $method, $information);

        // Return Response with signed Input and Output
        return isset($output->success) ? $output : $outputClass->make($information->getXml(), $output);
    }

    public function getNf(BaseInformation $baseInformation, $params)
    {
        if (!is_array($params) && !$params instanceof NfSearch)
            $params = [SimpleFieldsEnum::NFE_NUMBER => $params];

        $builder = new PedidoConsultaNFe();
        return $this->processRequest($baseInformation, $params, NfMethods::CONSULTA, $builder);
    }

    public function lotInformation(BaseInformation $baseInformation, $params = [])
    {
        $builder = new PedidoInformacoesLote();
        return $this->processRequest($baseInformation, $params, NfMethods::CONSULTA_INFORMACOES_LOTE, $builder);
    }

    public function getLot(BaseInformation $baseInformation, $lotNumber)
    {
        $lot = [HeaderEnum::LOT_NUMBER => $lotNumber];
        $builder = new PedidoConsultaLote();
        return $this->processRequest($baseInformation, $lot, NfMethods::CONSULTA_LOTE, $builder);
    }

    public function cancelNf(BaseInformation $baseInformation, $params)
    {
        if (!is_array($params) && !$params instanceof NfSearch)
            $params = [SimpleFieldsEnum::NFE_NUMBER => $params];

        $builder = new PedidoCancelamentoNFe();
        return $this->processRequest($baseInformation, $params, NfMethods::CANCELAMENTO, $builder);
    }

    public function sendNf(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoEnvioRPS();
        $response = $this->processRequest($baseInformation, $params, NfMethods::ENVIO, $builder);
        if($response->getSuccess() == 'false'){
            $response->setMessage(General::getPath($response->getResponse(),'Erro.0.Descricao'));
        }
        return $response;
    }

    public function sendLot(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoEnvioLoteRPS();
        return $this->processRequest($baseInformation, $params, NfMethods::ENVIO_LOTE, $builder);
    }

    public function testSendLot(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoEnvioLoteRPS();
        return $this->processRequest($baseInformation, $params, NfMethods::TESTE_ENVIO_LOTE, $builder);
    }

    public function getIssued(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoConsultaNFePeriodo();
        return $this->processRequest($baseInformation, $params, NfMethods::CONSULTA_NFE_EMITIDAS, $builder);
    }

    //  NF ASYNC METHODS

    public function getReceived(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoConsultaNFePeriodo();
        return $this->processRequest($baseInformation, $params, NfMethods::CONSULTA_NFE_RECEBIDAS, $builder);
    }

    public function testSendAsyncLot(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoEnvioLoteRPS();
        return $this->processAsyncRequest($baseInformation, $params, NfAsyncMethods::TESTE_ENVIO_LOTE, $builder);
    }

    private function processAsyncRequest(BaseInformation $information, $params, $method, InputTransformer $builder, OutputClass $outputClass = null)
    {
        // Check Output Type
        $outputClass = !empty($outputClass) ? $outputClass : $this->response;
        $params = General::convertUserRequest($params);

        //  File Without Signature
        $file = $builder->makeXmlRequest($information, $params);
        //Set Input file and sign
        $information->setXml($file);

        // Send to API,
        $output = ApiClient::send($this->nfAsyncEndPoint, $method, $information);

        // Return Response with signed Input and Output
        return isset($output->success) ? $output : $outputClass->make($information->getXml(), $output);
    }

    public function sendAsyncLot(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoEnvioLoteRPS();
        return $this->processAsyncRequest($baseInformation, $params, NfAsyncMethods::ENVIO_LOTE, $builder);
    }

    public function checkAsyncLot(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoConsultaSituacaoLote();
        return $this->processAsyncRequest($baseInformation, $params, NfAsyncMethods::CONSULTA_SITUACAO_LOTE, $builder);
    }

    public function checkReceipt(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoConsultaGuia();
        return $this->processAsyncRequest($baseInformation, $params, NfAsyncMethods::CONSULTA_GUIA, $builder);
    }

    // Process Requests

    public function checkReceiptSituation(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoConsultaSituacaoGuia();
        return $this->processAsyncRequest($baseInformation, $params, NfAsyncMethods::CONSULTA_SITUACAO_GUIA, $builder);
    }

    public function makeReceiptAsync(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoEmissaoGuiaAsync();
        return $this->processAsyncRequest($baseInformation, $params, NfAsyncMethods::EMISSAO_GUIA_ASYNC, $builder);
    }

}