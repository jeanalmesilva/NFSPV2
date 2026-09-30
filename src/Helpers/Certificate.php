<?php

namespace NFSPV2\Helpers;

use Exception;
use Greenter\XMLSecLibs\Certificate\X509Certificate;
use Greenter\XMLSecLibs\Certificate\X509ContentType;
use Greenter\XMLSecLibs\Sunat\SignedXml;
use NFSPV2\Constants\FieldData\BooleanFields;
use NFSPV2\Constants\Requests\RpsEnum;
use NFSPV2\Constants\Requests\SimpleFieldsEnum;
use NFSPV2\Entities\BaseInformation;

/**
 * Class Certificate
 * @package NFSPV2\Helpers
 */
class Certificate
{
    /**
     * @param $path
     * @param $pass
     * @return string
     * @throws Exception
     */
    public static function pfx2pem($path, $pass)
    {
        // Get File
        $pfx = file_get_contents($path);

        // Export PEM
        $certificate = new X509Certificate($pfx, $pass);
        $pem = $certificate->export(X509ContentType::PEM);

        return $pem;
    }

    /**
     * @param $pem
     * @param $xml
     * @return string
     */
    public static function signXmlWithCertificate($pem, $xml)
    {
        // Create Signed XML
        $signer = new SignedXml();

        // Sign XML
        $signer->setCertificate($pem);
        $xmlSigned = $signer->signXml($xml);

        // Return XML File Signed
        return $xmlSigned;
    }


    public static function signItem(BaseInformation $baseInformation, $content)
    {
        //$signatureValue = '';
        //$pkeyId = openssl_get_privatekey($baseInformation->getCertificate());
        //openssl_sign($content, $signatureValue, $pkeyId, OPENSSL_ALGO_SHA1);
        //openssl_free_key($pkeyId);
        //return base64_encode($signatureValue);

        $signatureValue = '';
        /**It should be noted that the default signature algorithm used by openssl_sign() and openssl_verify (OPENSSL_ALGO_SHA1) is no longer supported by default in OpenSSL Version 3 series.
         * With an up to date OpenSSL library, one has to run "update-crypto-policies --set LEGACY"
         * on the server where the library resides in order to allow these functions to work without the optional alternative algorithm argument. */
        $pkeyId = openssl_get_privatekey($baseInformation->getCertificate());
        openssl_sign($content, $signatureValue, $pkeyId, OPENSSL_ALGO_SHA1);
        
        /**https://notadomilhao.sf.prefeitura.sp.gov.br/wp-content/uploads/2025/11/NFe_Web_Service-4.pdf 
        OPENSSL_ALGO_SHA1 OBRIGATÓRIO PARA OPENSSL 3 PREFEITURA MUNICIPAL VERSAO DO MANUAL 3.3.4 PAGINA 45*/


        /**In PHP 8.0.0 and later, OpenSSL key resources are handled as OpenSSLAsymmetricKey objects.
         *  These objects are automatically destroyed by PHP's garbage collection system when they are no longer referenced. 
         * Therefore, explicitly calling openssl_free_key() (or its alias openssl_pkey_free()) is no longer necessary and has no effect in these versions of PHP. */

        if (PHP_VERSION_ID < 80000) {
            openssl_free_key($pkeyId);
        }

        return base64_encode($signatureValue);
    }

    public static function rpsSignatureString($params)
    {
        $valorCobrado = General::getKey($params, RpsEnum::SERVICE_FINAL_CHARGED);
        if ($valorCobrado === null || $valorCobrado === '') {
            $valorCobrado = General::getKey($params, RpsEnum::SERVICE_INITIAL_CHARGED);
        }

        $nifSuffix = '';
        $nif = General::getKey($params, RpsEnum::NIF);
        $naoNif = General::getKey($params, RpsEnum::NAO_NIF);

        if ($nif) {
            $tomadorIndicator = '4';
            $document = '00000000000000';
            $nifSuffix = $nif;
        } elseif ($naoNif !== null && $naoNif !== '') {
            $tomadorIndicator = '4';
            $document = '00000000000000';
            $nifSuffix = (string) $naoNif;
        } elseif (General::getKey($params, SimpleFieldsEnum::CPF)) {
            $tomadorIndicator = '1';
            $document = sprintf('%014s', General::getKey($params, SimpleFieldsEnum::CPF));
        } elseif (General::getKey($params, SimpleFieldsEnum::CNPJ)) {
            $tomadorIndicator = '2';
            $document = sprintf('%014s', General::getKey($params, SimpleFieldsEnum::CNPJ));
        } else {
            $tomadorIndicator = '3';
            $document = '00000000000000';
        }

        $intermediarySuffix = '';
        $cpfIntermediary = General::getKey($params, SimpleFieldsEnum::CPF_INTERMEDIARY);
        $cnpjIntermediary = General::getKey($params, SimpleFieldsEnum::CNPJ_INTERMEDIARY);

        if ($cpfIntermediary) {
            $intermediarySuffix = '1' . sprintf('%014s', $cpfIntermediary);
        } elseif ($cnpjIntermediary) {
            $intermediarySuffix = '2' . sprintf('%014s', $cnpjIntermediary);
        }

        if ($intermediarySuffix !== '') {
            $issRetidoIntermediary = General::getKey($params, RpsEnum::ISS_RETENTION_INTERMEDIARY);
            $intermediarySuffix .= ($issRetidoIntermediary === true || $issRetidoIntermediary === 'true' || $issRetidoIntermediary === BooleanFields::TRUE)
                ? BooleanFields::TRUE
                : BooleanFields::FALSE;
        }

        $string =
            sprintf('%012s', General::getKey($params, SimpleFieldsEnum::IM_PROVIDER)) .
            sprintf('%-5s', General::getKey($params, SimpleFieldsEnum::RPS_SERIES)) .
            sprintf('%012s', General::getKey($params, SimpleFieldsEnum::RPS_NUMBER)) .
            str_replace('-', '', General::getKey($params, RpsEnum::EMISSION_DATE)) .
            General::getKey($params, RpsEnum::RPS_TAX) .
            General::getKey($params, RpsEnum::RPS_STATUS) .
            ($params[RpsEnum::ISS_RETENTION] == 'false' ? BooleanFields::FALSE : BooleanFields::TRUE) .
            sprintf('%015s', str_replace(array('.', ','), '', number_format((float) $valorCobrado, 2, '.', ''))) .
            sprintf('%015s', str_replace(array('.', ','), '', number_format((float) General::getKey($params, RpsEnum::DEDUCTION_VALUE), 2, '.', ''))) .
            sprintf('%05s', General::getKey($params, RpsEnum::SERVICE_CODE)) .
            $tomadorIndicator .
            $document .
            $intermediarySuffix .
            $nifSuffix;

        return $string;
    }

    public static function cancelSignatureString($params)
    {
        return sprintf('%08s', General::getKey($params, SimpleFieldsEnum::IM_PROVIDER)) .
            sprintf('%012s', General::getKey($params, SimpleFieldsEnum::NFE_NUMBER));
    }


    public static function nftsSignatureString($elements)
    {
        return '<tpNFTS>' . Certificate::makeXmlString($elements) . '</tpNFTS>';
    }

    public static function makeXmlString($array)
    {
        $string = '';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = Certificate::makeXmlString($value);
            }
            $string .= '<' . $key . '>' . $value . '</' . $key . '>';
        }
        return $string;
    }

    public static function nftsCancellationSignatureString($elements)
    {
        return '<PedidoCancelamentoNFTSDetalheNFTS>' . Certificate::makeXmlString($elements) . '</PedidoCancelamentoNFTSDetalheNFTS>';
    }
}
