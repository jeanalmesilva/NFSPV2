<?php

/* *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
 *  Para esse Exemplo funcionar é necessário um certificado válido (*.pfx ou *.pem)                *
 *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  * */

// Instancie a Classe
use NFSPV2\Constants\FieldData\DocumentType;
use NFSPV2\Entities\Requests\NFTS\Nfts;
use NFSPV2\NFSPV2;
use NFSPV2\Constants\Params;

$nf = new NFSPV2([
    Params::CNPJ => '00027000000000',
    Params::IM => '00000002', // Opcional porém recomendado
    Params::CERTIFICATE_PATH => 'examples/certificate.pfx',
    Params::CERTIFICATE_PASS => '100001'
]);

// Monte a NFTS
$nfts = new Nfts();
$nfts->setNumeroDocumento('000000000163'); // Numero da nota Relacionada a NFTS
$nfts->setSerieNFTS('A'); // Serie da Nota relacionada
$nfts->setCodigoServico('7099');
$nfts->setValorFinalCobrado('165.31');
$nfts->setCnpjPrestador('00000040000100');
$nfts->setDiscriminacao('NFTS X ...');
$nfts->setDataPrestacao('2019-09-10');
$nfts->setTipoDocumento(DocumentType::WITHOUT_REQUIRED_EMISSION_FISCAL_DOCUMENT);
$nfts->setRazaoSocialPrestador('Razao Social Teste');
$nfts->setLogradouroPrestador('Avenida x x x');
$nfts->setCidadePrestador('Cidade X');
$nfts->setNumeroEnderecoPrestador('250');
$nfts->setBairroPrestador('Vila x');
$nfts->setUfPrestador('SP');
$nfts->setCepPrestador('06000000');
$nfts->setValorFinalCobrado(30.80);
$nfts->setValorPIS(0.00);
$nfts->setExigibilidadeSuspensa(0); // 0 - Não | 1 - Sim
$nfts->setPagamentoParceladoAntecipado(0); // 0 - Não | 1 - Sim
$nfts->setNbs("115029000");
$nfts->setlocPrestacao('3550308');
$nfts->setClassTrib('200028');//410999
$nfts->setFinNFSe(0);
$nfts->setIndFinal(0);
$nfts->setIndOp('100301');
$nfts->setTpOper(5);
$nfts->setTpEnteGov(1);
$nfts->setIndDest(1);

// Envie a Requisição - testeEnviarLote(Teste) ou enviarLote(Produção)
$request = $nf->enviarNfts($nfts);

// Utilize algum dos métodos do response para verificar o resultado
echo $request->getXmlOutput();
exit;