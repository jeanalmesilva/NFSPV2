<?php

namespace NFSPV2\Helpers;

/**
 * Monta endereços IBS/CBS conforme manual NFS-e Web Service v3.3.8 / schemas-reformatributaria-v02-5:
 * - tpEnderecoIBSCBS (dest/pessoa): choice endNac|endExt + xLgr, nro, xBairro [, xCpl]
 * - tpEnderecoSimplesIBSCBS (imovelobra/atvEvento): choice CEP|endExt + xLgr, nro, xBairro [, xCpl]
 */
class IbscbsAddress
{
    /**
     * Endereço nacional completo (tpEnderecoIBSCBS com endNac).
     */
    public static function makeEndNac($cMun, $cep, $xLgr, $nro, $xBairro, $xCpl = null)
    {
        $end = [
            'endNac' => self::makeEndNacCore($cMun, $cep),
        ];

        return array_merge($end, self::makeBase($xLgr, $nro, $xBairro, $xCpl));
    }

    /**
     * Endereço no exterior completo (tpEnderecoIBSCBS com endExt).
     */
    public static function makeEndExt($cPais, $cEndPost, $xCidade, $xEstProvReg, $xLgr, $nro, $xBairro, $xCpl = null)
    {
        $end = [
            'endExt' => self::makeEndExtCore($cPais, $cEndPost, $xCidade, $xEstProvReg),
        ];

        return array_merge($end, self::makeBase($xLgr, $nro, $xBairro, $xCpl));
    }

    /**
     * Endereço simplificado nacional (tpEnderecoSimplesIBSCBS com CEP).
     */
    public static function makeEndSimples($cep, $xLgr, $nro, $xBairro, $xCpl = null)
    {
        $end = [
            'CEP' => self::normalizeCep($cep),
        ];

        return array_merge($end, self::makeBase($xLgr, $nro, $xBairro, $xCpl));
    }

    /**
     * Normaliza o bloco end de tpInformacoesPessoa (dest etc.).
     */
    public static function normalizeEndIbscbs(array $end)
    {
        $normalized = [];

        if (isset($end['endNac']) && is_array($end['endNac'])) {
            $normalized['endNac'] = self::normalizeEndNacCore($end['endNac']);
        } elseif (isset($end['endExt']) && is_array($end['endExt'])) {
            $normalized['endExt'] = self::normalizeEndExtCore($end['endExt']);
        } elseif (self::hasEndExtFlat($end)) {
            $normalized['endExt'] = self::makeEndExtCore(
                $end['cPais'],
                $end['cEndPost'],
                $end['xCidade'],
                $end['xEstProvReg']
            );
        } elseif (isset($end['cMun']) || isset($end['CEP'])) {
            $normalized['endNac'] = self::makeEndNacCore(
                isset($end['cMun']) ? $end['cMun'] : null,
                isset($end['CEP']) ? $end['CEP'] : null
            );
        }

        return array_merge($normalized, self::normalizeBase($end));
    }

    /**
     * Normaliza o bloco end de tpEnderecoSimplesIBSCBS.
     */
    public static function normalizeEndSimples(array $end)
    {
        $normalized = [];

        if (isset($end['endExt']) && is_array($end['endExt'])) {
            $normalized['endExt'] = self::normalizeEndExtCore($end['endExt']);
        } elseif (self::hasEndExtFlat($end)) {
            $normalized['endExt'] = self::makeEndExtCore(
                $end['cPais'],
                $end['cEndPost'],
                $end['xCidade'],
                $end['xEstProvReg']
            );
        } elseif (isset($end['CEP']) || isset($end['endNac']['CEP'])) {
            $cep = isset($end['CEP']) ? $end['CEP'] : $end['endNac']['CEP'];
            $normalized['CEP'] = self::normalizeCep($cep);
        }

        return array_merge($normalized, self::normalizeBase($end));
    }

    /**
     * Normaliza dest / Adquirente / fornec (tpInformacoesPessoa).
     * Ordem XSD: CPF|CNPJ|NIF|NaoNIF → xNome → end → email
     */
    public static function normalizePessoa(array $pessoa)
    {
        $normalized = [];

        if (isset($pessoa['CPF'])) {
            $normalized['CPF'] = sprintf('%011s', General::onlyNumbers($pessoa['CPF']));
        } elseif (isset($pessoa['CNPJ'])) {
            $normalized['CNPJ'] = General::regexCnpj($pessoa['CNPJ']);
        } elseif (isset($pessoa['NIF'])) {
            $normalized['NIF'] = substr((string)$pessoa['NIF'], 0, 40);
        } elseif (isset($pessoa['NaoNIF'])) {
            $normalized['NaoNIF'] = $pessoa['NaoNIF'];
        }

        if (isset($pessoa['xNome'])) {
            $normalized['xNome'] = General::filterString(substr($pessoa['xNome'], 0, 75));
        }

        if (isset($pessoa['end']) && is_array($pessoa['end'])) {
            $normalized['end'] = self::normalizeEndIbscbs($pessoa['end']);
        } elseif (self::hasAddressHints($pessoa)) {
            $normalized['end'] = self::normalizeEndIbscbs(self::extractAddressHints($pessoa));
        }

        if (isset($pessoa['email']) && $pessoa['email'] !== '') {
            $normalized['email'] = substr((string)$pessoa['email'], 0, 75);
        }

        return $normalized;
    }

    /**
     * Normaliza imovelobra (tpImovelObra).
     */
    public static function normalizeImovelObra(array $imovelObra)
    {
        if (isset($imovelObra['end']) && is_array($imovelObra['end'])) {
            $imovelObra['end'] = self::normalizeEndSimples($imovelObra['end']);
        }

        return $imovelObra;
    }

    /**
     * Normaliza atvEvento (tpAtividadeEvento).
     */
    public static function normalizeAtvEvento(array $atvEvento)
    {
        if (isset($atvEvento['end']) && is_array($atvEvento['end'])) {
            $atvEvento['end'] = self::normalizeEndSimples($atvEvento['end']);
        }

        return $atvEvento;
    }

    private static function makeEndNacCore($cMun, $cep)
    {
        return [
            'cMun' => self::normalizeCMun($cMun),
            'CEP' => self::normalizeCep($cep),
        ];
    }

    private static function normalizeEndNacCore(array $endNac)
    {
        return self::makeEndNacCore(
            isset($endNac['cMun']) ? $endNac['cMun'] : null,
            isset($endNac['CEP']) ? $endNac['CEP'] : null
        );
    }

    private static function makeEndExtCore($cPais, $cEndPost, $xCidade, $xEstProvReg)
    {
        return [
            'cPais' => strtoupper(substr((string)$cPais, 0, 2)),
            'cEndPost' => General::filterString(substr((string)$cEndPost, 0, 11)),
            'xCidade' => General::filterString(substr((string)$xCidade, 0, 60)),
            'xEstProvReg' => General::filterString(substr((string)$xEstProvReg, 0, 60)),
        ];
    }

    private static function normalizeEndExtCore(array $endExt)
    {
        return self::makeEndExtCore(
            isset($endExt['cPais']) ? $endExt['cPais'] : null,
            isset($endExt['cEndPost']) ? $endExt['cEndPost'] : null,
            isset($endExt['xCidade']) ? $endExt['xCidade'] : null,
            isset($endExt['xEstProvReg']) ? $endExt['xEstProvReg'] : null
        );
    }

    private static function makeBase($xLgr, $nro, $xBairro, $xCpl = null)
    {
        // Ordem do gpEnderecoBaseIBSCBS no XSD: xLgr, nro, xCpl?, xBairro
        $base = [
            'xLgr' => General::filterString(substr((string)$xLgr, 0, 50)),
            'nro' => substr((string)$nro, 0, 10),
        ];

        if ($xCpl !== null && $xCpl !== '') {
            $base['xCpl'] = General::filterString(substr((string)$xCpl, 0, 30));
        }

        $base['xBairro'] = General::filterString(substr((string)$xBairro, 0, 30));

        return $base;
    }

    private static function normalizeBase(array $end)
    {
        $base = [];

        if (isset($end['xLgr'])) {
            $base['xLgr'] = General::filterString(substr((string)$end['xLgr'], 0, 50));
        }
        if (isset($end['nro'])) {
            $base['nro'] = substr((string)$end['nro'], 0, 10);
        }
        if (isset($end['xCpl']) && $end['xCpl'] !== '') {
            $base['xCpl'] = General::filterString(substr((string)$end['xCpl'], 0, 30));
        }
        if (isset($end['xBairro'])) {
            $base['xBairro'] = General::filterString(substr((string)$end['xBairro'], 0, 30));
        }

        return $base;
    }

    private static function normalizeCMun($cMun)
    {
        return sprintf('%07s', General::onlyNumbers($cMun));
    }

    private static function normalizeCep($cep)
    {
        return General::onlyNumbers($cep);
    }

    private static function hasEndExtFlat(array $data)
    {
        return isset($data['cPais'], $data['cEndPost'], $data['xCidade'], $data['xEstProvReg']);
    }

    private static function hasAddressHints(array $data)
    {
        return isset($data['endNac'])
            || isset($data['endExt'])
            || isset($data['xLgr'])
            || isset($data['cMun'])
            || (isset($data['CEP']) && isset($data['xBairro']));
    }

    private static function extractAddressHints(array $data)
    {
        $keys = ['endNac', 'endExt', 'cMun', 'CEP', 'cPais', 'cEndPost', 'xCidade', 'xEstProvReg', 'xLgr', 'nro', 'xCpl', 'xBairro'];
        $end = [];
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $end[$key] = $data[$key];
            }
        }
        return $end;
    }
}
