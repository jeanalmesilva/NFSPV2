# NFe-NFTS-SP-V2 (PHP)
[![Latest Stable Version](https://poser.pugx.org/kaleu62/notafiscalsp/v/stable)](https://packagist.org/packages/kaleu62/notafiscalsp) [![Total Downloads](https://poser.pugx.org/kaleu62/notafiscalsp/downloads)](https://packagist.org/packages/kaleu62/notafiscalsp) [![License](https://poser.pugx.org/kaleu62/notafiscalsp/license)](https://packagist.org/packages/kaleu62/notafiscalsp)


O Projeto se trata de um módulo de integração com o sistema de notas da Prefeitura de São Paulo (Nota do Milhão) Versão 2.0 (Reforma tributária obrigatoria a partir de 01-01-2026) , possibilitando a automatização de serviços como emissão e consulta de Notas e outros serviços relacionados.


### OBSERBAÇÂO -- NFST 
- Ainda não contemplado nesta primeira atualização para a versão 2.0 já que as necessidades da minha empresa não as contemplam.
- Porém para quem necessitar da NFS-e já está funcional.
- NFST será implementado em breve
  

### Extensões PHP Necessárias 
- Soap
- openssl

## OPENSSSL - Versoes 3
- SSH1 não é o padrão para as novas versões de OPENSSL
- para contornar os erros edite o arquivo /etc/ssl/openssl.cnf
- # List of providers to load
 [provider_sect]
 default = default_sect
 legacy = legacy_sect

 [default_sect]
 activate = 1
 [legacy_sect]
 activate = 1

  It should be noted that the default signature algorithm used by openssl_sign() and openssl_verify (OPENSSL_ALGO_SHA1) is no longer supported by default in OpenSSL Version 3 series.
  With an up to date OpenSSL library, one has to run as root "update-crypto-policies --set LEGACY"
  on the server where the library resides in order to allow these functions to work without the optional alternative algorithm argument. 


### Referências úteis
- Na hora de emitir uma nota o campo de Cidade do Tomador é preenchido com o código do IBGE para a mesma, e ele pode ser consultado no site https://cidades.ibge.gov.br/brasil/sp/sao-paulo


## Novos campos obrigatórios.
- observe no diretório de exemplos o arquivo NF-EnviarLoteTeste.php

  
## Instanciando a Classe
Para instanciar a classe é necessário informar o CNPJ (Aceita CNPJ alfanumérico) , o Certificado do Emissor e a senha do mesmo. No caso do caminho do Certificado pode ser utilizado o arquivo '.pfx' ou '.pem'

```php
  // Instanciando a Classe
  $nfSP = new NFSPV2([
      'cnpj' => '00000000000000',
      'certificate' => 'path/to/certificate.pfx',
      'certificatePass' => '000000'
  ]);
```

Ao instanciar a Lib ela faz uma requisiçao para obter a Inscrição Municipal(IM), porém a mesma pode ser passada como parametro.

```php
  // Instanciando a Classe
  $nfSP = new NFSPV2([
      'cnpj' => '00000000000000',
      'certificate' => 'path/to/certificate.pfx',
      'certificatePass' => '000000',
      'im' => '1225'
  ]);
```

# Nota Fiscal (NFs NFe)
## Obtendo Informações Base do CNPJ
Esse método retorna a Inscrição Municipal relacionada ao CNPJ e um booleano indicando se o mesmo pode emitir NFe

```php
// Consulta seu próprio CNPJ para verificar a Inscrição Municipal
$response = $nfSP->cnpjInfo(); 
```

```php
// Consulta um CNPJ para verificar a inscrição municipal e a situação referente a emissão
$response = $nfSP->cnpjInfo('111.222.333-44'); 
```

## Obtendo Informações Basicas do Lote
Retorna apeas informações básicas como horário de envio do lote

```php
$response = $nfSP->informacaoLote();
```

## Consultando Nota Fiscal
Retorna Informaçes detalhadas de uma ou mais Notas ***(Limite 50 Notas por Requisição)***

```php
// Utilize o numero da nota
$response = $nfSP->consultarNf('00056');
```
*Para maiores detalhes sobre a consulta de várias notas simultaneamente veja o Wiki

## Consultando Notas Fiscais Recebidas por Periodo
Retorna Notas recebidas em um periodo especifico ***(50 Notas por Pagina)***

```php
$period = new Period();
$period->setDtInicio('2019-08-05');
$period->setDtFim('2019-08-10');
$period->setPagina(2);

$response = $nfSP->notasRecebidas($period);
```
***- Caso não insira a data Final, serão retornados somente registros da data inicial***

***- Caso não seja informado o numero da página o valor padrão é 1***

## Consultando Notas Fiscais Emitidas por Periodo
Retorna Notas emitidas em um periodo especifico ***(50 Notas por Pagina)***

```php
$period = new Period();
$period->setDtInicio('2019-08-05');
$period->setDtFim('2019-08-10');
$period->setPagina(2);

$response = $nfSP->notasEmitidas($period);
```
***- Caso não insira a data Final, serão retornados somente registros da data inicial***

***- Caso não seja informado o numero da página o valor padrão é 1***

## Consultando Lote
Retorna Informações detalhadas de um lote especifico

```php
// Utilize o numero do Lote
$response = $nfSP->consultarLote(356);
```
*Para mais detalhes da utilizaço acesse o Wiki 

## Cancelando Nota Fiscal
Cancela uma ou mais Notas ***(Limite 50 Notas por Requisição)***

```php
$response = $nfSP->cancelarNota('00568');
```

## Emitindo uma Nota
```php
$rps = new Rps();
$rps->setNumeroRps('300000001');
$rps->setTipoRps(RPSType::RECIBO_PROVISORIO);
$rps->setValorPIS(0.00);
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
$rps->setValorFinalCobrado(30.80);
$rps->setExigibilidadeSuspensa(0); // 0 - Não | 1 - Sim
$rps->setPagamentoParceladoAntecipado(0); // 0 - Não | 1 - Sim
$rps->setNbs("115029000");
$rps->setlocPrestacao('3550308');
$rps->setClassTrib('200028');//410999
$rps->setFinNFSe(0);
$rps->setIndFinal(0);
$rps->setIndOp('100301');
$rps->setTpOper(5);
$rps->setTpEnteGov(1);
$rps->setIndDest(1);

$response =  $nfSP->enviarNota($rps);
```

## Enviando Lote
O Lote envia diversos objetos do tipo RPS em uma unica requisição

```php
$lote = new Lot();
$lote->setRpsList([$rps1, $rps2, $rps3]);
$response =  $nfSP->enviarLote($lote);
```

## Enviando um Lote Async
O Lote ASYNC utiliza um outro Endpoint e pode ser útil caso o sistema de Notas esteja com alguma instabilidade ou em manutenção, é utilizada a mesma request porém é retornado um número de protocolo que pode ser consultado posteriormente
```php
// Enviar Lote Async
$makeProtocol = $nfSP->enviarLoteAsync($lot);

// Consultar se o lote foi emitido
$lotResult = $nfSP->consultarLoteAsync('1223589');
```

# NFTS
## Consultando uma NFTS
```php
    $nfSP->consultarNfts('454565')
```
## Emitindo uma NFTS
 #ainda não contemplado 
```php
// Montando o objeto da NFTS
$nfts = new Nfts();
$nfts->setNumeroDocumento('000000000000');
$nfts->setSerieNFTS('A');
$nfts->setCodigoServico('7099');
$nfts->setValorServicos('150.30');
$nfts->setCnpjPrestador('00000000000100');
$nfts->setDiscriminacao('xxx');
$nfts->setDataPrestacao('2019-09-10');
$nfts->setTipoDocumento('01');
$nfts->setRazaoSocialPrestador('XXXX');
$nfts->setLogradouroPrestador('Avenida x x x');
$nfts->setCidadePrestador('x');
$nfts->setNumeroEnderecoPrestador('250');
$nfts->setBairroPrestador('Vila x');
$nfts->setUfPrestador('SP');
$nfts->setCepPrestador('06000000');

// Emitindo a NFTS
$nfSP->enviarNfts($nfts);
```

## Cancelando uma NFTS
```php
$response = $nfSP->cancelarNfts('00568');
```


# Métodos Básicos do Response
## getResponse
Retorna uma array com as informaçes da resposta da API
```php
  $response->getResponse();
```


## getXmlInput
Retorna o XML enviado para API (REQUEST)
```php
  $response->getXmlInput();
```

## getXmlOutput
Retorna o XML Recebido da API (RESPONSE)
```php
  $response->getXmlOutput();
```

## getSuccess
Verifica o sucesso da operação realizada
```php
  $response->getSuccess();
```

## Classe NfSearch (NFSPV2\Entities\Requests\NF\NfSearch)
É a classe utilizada para referenciar uma Nota Fiscal já Existente, não é necessário preencher todas propriedades, apenas o NumeroNfe é o suficiente.
   
|          **Propriedade**           |          **Método**         |   **Tipo**   |
|:----------------------------------:|:---------------------------:|:------------:|
|         InscricaoPrestador         |   setInscricaoPrestador()   |      int     |
|              NumeroNfe             |        setNumeroNfe()       |      int     |
|          CodigoVerificacao         |    setCodigoVerificacao()   |    string    |
|              NumeroRPS             |        setNumeroRPS()       |      int     |
|              SerieRPS              |        setSerieRPS()        |    string    |


## Classe Period (NFSPV2\Entities\Requests\NF\Period)
Utilizada na realização de consultas por periodo nas notas Emitidas e Recebidas, caso não altere nenhuma das propriedades retorna uma busca com os valores Padrões para data Atual

|         **Propriedade**           |          **Método**           |   **Tipo**   |    ** Observações**    |
|:---------------------------------:|:-----------------------------:|:------------:|:----------------------:|
|                CPF                |            setCpf()           |    string    |                        |
|                CNPJ               |           setCnpj()           |    string    |                        |
|         InscricaoMunicipal        |    setInscricaoMunicipal()    |      int     |                        |
|              DtInicio             |         setDtInicio()         |    string    |   format(YYYY-MM-DD)   |
|               DtFim               |           setDtFim()          |    string    |   format(YYYY-MM-DD)   |
|               Pagina              |          setPagina()          |      int     |                        |
|             Transacao             |         setTransacao()        |    boolean   |                        |

## Classe Rps (NFSPV2\Entities\Requests\NF\Rps)
Objeto utilizado para emissão de novas notas

|         **Propriedade**           |              **Método**              |   **Tipo**   |       ** Observações**       |
|:---------------------------------:|:------------------------------------:|:------------:|:----------------------------:|
|         InscricaoPrestador        |        setInscricaoPrestador()       |      int     |                              |
|              SerieRps             |             setSerieRps()            |    string    |                              |
|             NumeroRps             |            setNumeroRps()            |      int     |                              |
|              TipoRps              |             setTipoRps()             |    string    |                              |
|            DataEmissao            |           setDataEmissao()           |    string    |      format(YYYY-MM-DD)      |
|             StatusRps             |            setStatusRps()            |    string    |                              |
|           TributacaoRps           |          setTributacaoRps()          |    string    |                              |
|           ValorServicos           |          setValorServicos()          |     float    |                              |
|           ValorDeducoes           |          setValorDeducoes()          |      int     |          default: 0          |
|              ValorPIS             |             setValorPIS()            |     float    |                              |
|            ValorCOFINS            |           setValorCOFINS()           |     float    |                              |
|             ValorINSS             |            setValorINSS()            |     float    |                              |
|              ValorIR              |             setValorIR()             |     float    |                              |
|             ValorCSLL             |            setValorCSLL()            |     float    |                              |
|           CodigoServico           |          setCodigoServico()          |      int     |                              |
|          AliquotaServicos         |         setAliquotaServicos()        |     float    |                              |
|             IssRetido             |            setIssRetido()            |    boolean   |        default: false        |
|     InscricaoMunicipalTomador     |    setInscricaoMunicipalTomador()    |      int     |                              |
|      InscricaoEstadualTomador     |     setInscricaoEstadualTomador()    |      int     |                              |
|         RazaoSocialTomador        |        setRazaoSocialTomador()       |    string    |                              |
|            EmailTomador           |           setEmailTomador()          |    string    |                              |
|           CpfCnpjTomador          |          setCpfCnpjTomador()         |    string    |                              |
|           TipoLogradouro          |          setTipoLogradouro()         |    string    |                              |
|             Logradouro            |            setLogradouro()           |    string    |                              |
|           NumeroEndereco          |          setNumeroEndereco()         |      int     |                              |
|        ComplementoEndereco        |       setComplementoEndereco()       |    string    |                              |
|               Bairro              |              setBairro()             |    string    |                              |
|               Cidade              |              setCidade()             |    string    | default: 3550308 (São Paulo) |
|                 UF                |                setUF()               |    string    |                              |
|                Cep                |               setCep()               |    string    |                              |
|                Cpf                |               setCpf()               |    string    |                              |
|                Cnpj               |               setCnpj()              |    string    |                              |
|           Discriminacao           |          setDiscriminacao()          |    string    |                              |
|          cpfIntermediario         |         setcpfIntermediario()        |    string    |                              |
|         cnpjIntermediario         |        setcnpjIntermediario()        |    string    |                              |
|  InscricaoMunicipalIntermediario  | setInscricaoMunicipalIntermediario() |      int     |                              |
|       IssRetidoIntermediario      |      setIssRetidoIntermediario()     |    boolean   |                              |
|         EmailIntermediario        |        setEmailIntermediario()       |    string    |                              |
|        ValorCargaTributaria       |       setValorCargaTributaria()      |     float    |                              |
|     PercentualCargaTributaria     |    setPercentualCargaTributaria()    |     float    |                              |
|        FonteCargaTributaria       |       setFonteCargaTributaria()      |    string    |                              |
|             CodigoCEI             |            setCodigoCEI()            |    string    |                              |
|           MatriculaObra           |          setMatriculaObra()          |    string    |                              |
|         MunicipioPrestacao        |        setMunicipioPrestacao()       |    string    |                              |
|         ValortotalRecebido        |        setValortotalRecebido()       |     float    |                              |
|        NumeroEncapsulamento       |       setNumeroEncapsulamento()      |      int     |                              |
