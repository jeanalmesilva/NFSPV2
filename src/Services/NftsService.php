<?php

namespace NFSPV2\Services;

use NFSPV2\Builders\NFTS\PedidoCancelamentoNFTS;
use NFSPV2\Builders\NFTS\PedidoConsultaEmissaoNFSE;
use NFSPV2\Builders\NFTS\PedidoConsultaInformacoesLoteNFTS;
use NFSPV2\Builders\NFTS\PedidoConsultaLoteNFTS;
use NFSPV2\Builders\NFTS\PedidoConsultaNFTS;
use NFSPV2\Builders\NFTS\PedidoEnvioLoteNFTS;
use NFSPV2\Builders\NFTS\PedidoEnvioNFTS;
use NFSPV2\Builders\Responses\BasicTransformerResponse;
use NFSPV2\Builders\WsdlBuilder;
use NFSPV2\Client\ApiClient;
use NFSPV2\Constants\Endpoints;
use NFSPV2\Constants\Methods\NftsMethods;
use NFSPV2\Constants\Requests\HeaderEnum;
use NFSPV2\Contracts\InputTransformer;
use NFSPV2\Contracts\OutputClass;
use NFSPV2\Contracts\UserRequest;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Helpers\General;

class NftsService
{
    public $response;
    private $endPoint;

    public function __construct()
    {
        $this->endPoint = WsdlBuilder::make(Endpoints::NFTS);;
        $this->response = new BasicTransformerResponse();
    }

    public function getNfts(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoConsultaNFTS();
        return $this->processRequest($baseInformation, $params, NftsMethods::CONSULTA, $builder);
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

        // Send to API
        $output = ApiClient::send($this->endPoint, $method, $information);

        // Return Response with signed Input and Output
        return isset($output->success) ? $output : $outputClass->make($information->getXml(), $output);
    }

    public function lotInformation(BaseInformation $baseInformation, $lotNumber)
    {
        $lot = [HeaderEnum::LOT_NUMBER => $lotNumber];
        $builder = new PedidoConsultaInformacoesLoteNFTS();
        return $this->processRequest($baseInformation, $lot, NftsMethods::CONSULTA_INFORMACOES_LOTE, $builder);
    }

    public function getLot(BaseInformation $baseInformation, $lotNumber)
    {
        $lot = [HeaderEnum::LOT_NUMBER => $lotNumber];
        $builder = new PedidoConsultaLoteNFTS();
        return $this->processRequest($baseInformation, $lot, NftsMethods::CONSULTA_LOTE, $builder);
    }

    public function checkEmission(BaseInformation $baseInformation, $params)
    {
        if ($params instanceof UserRequest)
            $params = $params->toArray();

        $builder = new PedidoConsultaEmissaoNFSE();
        return $this->processRequest($baseInformation, $params, NftsMethods::CONSULTA_AUT_EMISSAO, $builder);
    }

    public function testLotNfts(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoEnvioLoteNFTS();
        return $this->processRequest($baseInformation, $params, NftsMethods::TESTE_ENVIO_LOTE, $builder);
    }

    public function lotNfts(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoEnvioLoteNFTS();
        return $this->processRequest($baseInformation, $params, NftsMethods::ENVIO_LOTE, $builder);
    }

    public function sendNfts(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoEnvioNFTS();
        $response = $this->processRequest($baseInformation, $params, NftsMethods::ENVIO, $builder);

        if($response->getSuccess() == 'false'){
            $response->setMessage(General::getPath($response->getResponse(),'ListaRetornoNFTS.Erro.Descricao'));
        }
        return $response;
    }

    public function cancelNfts(BaseInformation $baseInformation, $params)
    {
        $builder = new PedidoCancelamentoNFTS();
        return $this->processRequest($baseInformation, $params, NftsMethods::ENVIO, $builder);
    }
}