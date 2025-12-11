<?php

namespace NFSPV2\Contracts;

use NFSPV2\Entities\BaseInformation;

interface InputTransformer
{
    public function makeXmlRequest(BaseInformation $information, $params);
}