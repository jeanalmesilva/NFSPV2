<?php

namespace NFSPV2\Validators;

use NFSPV2\Constants\FieldData\BooleanFields;
use NFSPV2\Constants\FieldData\RPSType;
use NFSPV2\Constants\FieldData\Status;
use NFSPV2\Constants\Requests\ComplexFieldsEnum;
use NFSPV2\Constants\Requests\DetailEnum;
use NFSPV2\Constants\Requests\RpsEnum;
use NFSPV2\Constants\Requests\SimpleFieldsEnum;
use NFSPV2\Entities\BaseInformation;
use NFSPV2\Entities\Requests\NF\Rps;
use NFSPV2\Exceptions\InvalidParam;
use NFSPV2\Helpers\Certificate;

class RpsValidator
{
    public static function  validateRpsType($value){
        if(!in_array($value, [RPSType::RECIBO_PROVISORIO,RPSType::RECIBO_PROVENIENTE_DE_NOTA_CONJUGADA, RPSType::CUPOM])){
            $expected = RPSType::RECIBO_PROVISORIO. ', '. RPSType::RECIBO_PROVENIENTE_DE_NOTA_CONJUGADA. ' ou '.RPSType::CUPOM;
            throw new InvalidParam('TipoRps', $expected);
        }
    }

    public static function  validateRpsStatus($value){
        if(!in_array($value, [Status::NORMAL, Status::CANCELLED, Status::MISPLACED])){
            $expected = Status::NORMAL. ', '.  Status::CANCELLED. ' ou '.Status::MISPLACED;
            throw new InvalidParam('StatusRps', $expected);
        }
    }

    public static function validateRps(BaseInformation $baseInformation, $rps2)
    {
        $rpsOK = [];

        $rps = !array_key_exists(0, $rps2) ? [$rps2] : $rps2 ;

        foreach ($rps as $item) {
            if ($item instanceof Rps) {
                $item = $item->toArray();
            }
            if (empty($item[SimpleFieldsEnum::IM_PROVIDER]))
                $item[SimpleFieldsEnum::IM_PROVIDER] = $baseInformation->getIm();

            if(isset($item[RpsEnum::ISS_RETENTION])){
                $item[RpsEnum::ISS_RETENTION] = $item[RpsEnum::ISS_RETENTION] ? BooleanFields::LOWER_TRUE : BooleanFields::LOWER_FALSE;
            }else{
                $item[RpsEnum::ISS_RETENTION] = BooleanFields::LOWER_FALSE;
            }

            if (isset($item[RpsEnum::ISS_RETENTION_INTERMEDIARY])) {
                $item[RpsEnum::ISS_RETENTION_INTERMEDIARY] = $item[RpsEnum::ISS_RETENTION_INTERMEDIARY]
                    ? BooleanFields::LOWER_TRUE
                    : BooleanFields::LOWER_FALSE;
            }

            $item[ComplexFieldsEnum::RPS_KEY] = true;
            $item[DetailEnum::SIGN] = Certificate::rpsSignatureString($item);
            $rpsOK[] = $item;
        }
        return $rpsOK;
    }
}
