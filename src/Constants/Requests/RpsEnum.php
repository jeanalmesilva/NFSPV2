<?php

namespace NFSPV2\Constants\Requests;

use NFSPV2\Entities\Requests\NF\Rps;

class RpsEnum
{
    const RPS = 'RPS';
    const RPS_TYPE = 'TipoRPS';
    const EMISSION_DATE = 'DataEmissao';
    const RPS_STATUS = 'StatusRPS';
    const RPS_TAX = 'TributacaoRPS';
    const DEDUCTION_VALUE = 'ValorDeducoes';
    const PIS_VALUE = 'ValorPIS';
    const COFINS_VALUE = 'ValorCOFINS';
    const INSS_VALUE = 'ValorINSS';
    const IR_VALUE = 'ValorIR';
    const CSLL_VALUE = 'ValorCSLL';
    const SERVICE_CODE = 'CodigoServico';
    const SERVICE_TAX = 'AliquotaServicos';
    const ISS_RETENTION = 'ISSRetido';
    const IM_TAKER = 'InscricaoMunicipalTomador';
    const IE_TAKER = 'InscricaoEstadualTomador';
    const CORPORATE_NAME_TAKER = 'RazaoSocialTomador';
    const EMAIL_TAKER = 'EmailTomador';
    const SERVICE_TOTAL_RECEIVED = 'ValorTotalRecebido';
    const SERVICE_INITIAL_CHARGED = 'ValorInicialCobrado';
    const SERVICE_FINAL_CHARGED = 'ValorFinalCobrado';
    const DISCRIMINATION = 'Discriminacao';
    const CPFCNPJ_TAKER = 'CPFCNPJTomador';
    const CPFCNPJ_INTERMEDIARY = 'CPFCNPJIntermediario';
    const IM_INTERMEDIARY = 'InscricaoMunicipalIntermediario';
    const ISS_RETENTION_INTERMEDIARY = 'ISSRetidoIntermediario';
    const EMAIL_INTERMEDIARY = 'EmailIntermediario';
    const TAX_VALUE_INTERMEDIARY = 'ValorCargaTributaria';
    const TAX_PERCENT_INTERMEDIARY = 'PercentualCargaTributaria';
    const TAX_ORIGIN = 'FonteCargaTributaria';
    const CEI_CODE = 'CodigoCEI';
    const WORK_REGISTRATION = 'MatriculaObra';
    const CITY_INSTALLMENT = 'MunicipioPrestacao';
    const TOTAL_VALUE = 'ValorTotalRecebido';
    const ENCAPSULATION_NUMBER = 'NumeroEncapsulamento';
    const MULTA_VALUE = 'ValorMulta';
    const JUROS_VALUE = 'ValorJuros';
    const IPI_VALUE = 'ValorIPI';
    const EXIGIBILIDADE_SUSPENSA = 'ExigibilidadeSuspensa';
    const PAGAMENTO_PARCELADO_ANTECIPADO = 'PagamentoParceladoAntecipado';
    const NBS_FIELD = 'NBS'; //115029000
    const NCM_FIELD = 'NCM'; //01012100
    const LOC_PRESTACAO = 'cLocPrestacao'; //>3550308
    const CLASS_TRIB = 'cClassTrib';
    const FIN_NFSE = 'finNFSe';
    const IND_FINAL = 'indFinal';
    const IND_OP = 'cIndOp';
    const TP_OPER = 'tpOper';
    const TP_ENTE_GOV  = 'tpEnteGov';
    const IND_DEST = 'indDest';
    const RETENCAO_PIS_COFINS = 'RetencaoPisCofins';
    const C_PAIS_PRESTACAO = 'cPaisPrestacao';
    const ATV_EVENTO = 'atvEvento';
    const NIF = 'NIF';
    const NAO_NIF = 'NaoNIF';
    const DEST = 'dest';
    const IMOVEL_OBRA = 'imovelobra';
    const G_REF_NFSE = 'gRefNFSe';
    const VALORES = 'valores';

    public static function simpleTypes()
    {
        return [
            RpsEnum::RPS_TYPE,
            RpsEnum::EMISSION_DATE,
            RpsEnum::RPS_STATUS,
            RpsEnum::RPS_TAX,
            RpsEnum::DEDUCTION_VALUE,
            RpsEnum::PIS_VALUE,
            RpsEnum::COFINS_VALUE,
            RpsEnum::INSS_VALUE,
            RpsEnum::IR_VALUE,
            RpsEnum::CSLL_VALUE,
            RpsEnum::SERVICE_CODE,
            RpsEnum::SERVICE_TAX,
            RpsEnum::ISS_RETENTION,

        ];
    }

    public static function takerInformations()
    {
        return [
            RpsEnum::IM_TAKER,
            RpsEnum::IE_TAKER,
            RpsEnum::CORPORATE_NAME_TAKER,
        ];
    }
    public static function ibscbsFields()
    {
        return [
            RpsEnum::FIN_NFSE,
            RpsEnum::IND_FINAL,
            RpsEnum::IND_OP,
            RpsEnum::TP_OPER,
            RpsEnum::TP_ENTE_GOV,
            RpsEnum::IND_DEST,
        ];
    }



    /*		<IBSCBS>
			<finNFSe>0</finNFSe>
			<indFinal>0</indFinal>
			<cIndOp>100301</cIndOp>
			<tpOper>5</tpOper>
			<tpEnteGov>1</tpEnteGov>
			<indDest>1</indDest>
			<valores>
				<trib>
					<gIBSCBS>
						<cClassTrib>200028</cClassTrib>
					</gIBSCBS>
				</trib>
			</valores>
		</IBSCBS> */
}
