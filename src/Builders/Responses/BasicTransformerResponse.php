<?php

namespace NFSPV2\Builders\Responses;

use NFSPV2\Helpers\Xml;
use NFSPV2\Responses\BasicResponse;

class BasicTransformerResponse extends AbstractResponse
{

    public function __construct()
    {
        $this->response = new BasicResponse();
    }

    public function make($input, $output)
    {
        $this->arrayResponse = Xml::toArray($output);

        $this->response->setXmlInput($input);
        $this->response->setXmlOutput($output);
        $this->response->setResponse($this->arrayResponse);
        $this->response->setSuccess($this->checkSuccess());
        return ($this->response);
    }

}
