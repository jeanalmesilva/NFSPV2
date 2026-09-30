<?php
require_once realpath(__dir__.'/../vendor/autoload.php');
use NFSPV2\Constants\FieldData\RPSType;
use NFSPV2\Entities\Requests\NF\Lot;
use NFSPV2\Entities\Requests\NF\Rps;
use NFSPV2\NFSPV2;
use NFSPV2\Constants\Params;
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();
/* *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *
 *  Para esse Exemplo funcionar é necessário um certificado válido (*.pfx ou *.pem)                *
 *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  *  * */

// Instancie a Classe

$nf = new NFSPV2([
    Params::CNPJ => $_ENV['CNPJ'],
    Params::IM => $_ENV['MUNICIPAL_ID'], // Opcional porém recomendado
    Params::CERTIFICATE_PATH => $_ENV['CERT_PATH'],
    Params::CERTIFICATE_PASS => $_ENV['CERT_PASS']]);
// Monte a RPS
$rps = new Rps();
$rps->setNumeroRps('300000001');
$rps->setTipoRps(RPSType::RECIBO_PROVISORIO);
$rps->setValorCOFINS(0.00);
$rps->setValorINSS(0.00);
$rps->setValorIR(0.00);
$rps->setValorCSLL(0.00);
$rps->setCodigoServico(6009);
$rps->setAliquotaServicos(0.05);
$rps->setCnpj('J0CM5ZAU000106');
$rps->setRazaoSocialTomador('RAZAO SOCIAL TOMADOR LTDA');
$rps->setTipoLogradouro('R');
$rps->setLogradouro('NOME DA RUA');
$rps->setNumeroEndereco(001);
$rps->setBairro('VILA TESTE');
$rps->setCidade('3550308'); // São Paulo
$rps->setUf('SP');
$rps->setCep('00000000');
$rps->setEmailTomador('teste@teste.com.br');
$rps->setDiscriminacao('Teste Emissão de Notas pela API');
$rps->setValorPIS(0.00);
$rps->setValorFinalCobrado(30.80);
$rps->setExigibilidadeSuspensa(0); // 0 - Não | 1 - Sim
$rps->setPagamentoParceladoAntecipado(0); // 0 - Não | 1 - Sim
$rps->setNbs("115029000");
$rps->setlocPrestacao('3550308');
$rps->setClassTrib('200028');//410999
$rps->setFinNfse(0);
$rps->setIndFinal(0);
$rps->setIndOp('100301');
$rps->setTpOper(5);
$rps->setTpEnteGov(1);
$rps->setIndDest(1);
$rps->setRetencaoPisCofins(0); // 0 = PIS/COFINS/CSLL não retidos (manual 3.3.8)
$rps->setIbscbsDest([
    'CNPJ' => 'J0CM5ZAU000106',
    'xNome' => 'NOME TESTE DESTINATARIO',
    // tpEnderecoIBSCBS: endNac (ou endExt) + xLgr/nro/xBairro
    'end' => \NFSPV2\Helpers\IbscbsAddress::makeEndNac(
        '3550308',
        '01415000',
        'Rua Teste Destinatario',
        '100',
        'Centro',
        'Sala 1'
    ),
]);
// Opcional — imóvel/obra usa tpEnderecoSimplesIBSCBS (CEP ou endExt, sem endNac):
// $rps->setIbscbsImovelObra([
//     'end' => \NFSPV2\Helpers\IbscbsAddress::makeEndSimples('01415000', 'Rua da Obra', '50', 'Bela Vista'),
// ]);

// Monte o Objeto do Lote
$lot = new Lot();

// Insira os RPS
$lot->setRpsList(
    [
        $rps,
    ]
);

// Envie a Requisição - testeEnviarLote(Teste) ou enviarLote(Produção)
$request = $nf->testeEnviarLote($lot);

// Utilize algum dos métodos do response para verificar o resultado
//echo $request->getXmlOutput();
exit;