<?php

namespace NFSPV2\Client;

use Exception;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Entities\WsdlBase;
use NFSPV2\Responses\BasicResponse;
use SoapClient;

class ApiClient
{
    public static function send(WsdlBase $wsdlBase, $method, BaseInformation $baseInformation)
    {
        $saveXml = false;

        if ($saveXml) {
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($baseInformation->getXml());
            $dom->save('./' . date('YmdHis') . '_request.xml');
        }

        $options = [
            'location' => $wsdlBase->getEndPoint(),
            'keep_alive' => true,
            'trace' => true,
            'local_cert' => $baseInformation->getCertificatePath(),
            'passphrase' => $baseInformation->getCertificatePass(),
            'cache_wsdl' => WSDL_CACHE_NONE,
        ];

        try {
            $client = new SoapClient($wsdlBase->getWsdl(), $options);

            $arguments = [
                $method => [
                    'VersaoSchema' => 2,
                    'MensagemXML' => $baseInformation->getXml()
                ],
            ];

            $options = [];
            $result = $client->__soapCall($method, $arguments, $options);

            if ($saveXml) {
                $dom->loadXML($result->RetornoXML);
                $dom->save('./' . date('YmdHis') . '_response.xml');
            }

            return $result->RetornoXML;
        } catch (Exception $e) {
            $response = new BasicResponse();
            $response->setSuccess(false);
            $response->setXmlInput($baseInformation->getXml());
            $response->setMessage($e);
            return $response;
        }
    }
}
