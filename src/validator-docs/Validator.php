<?php

namespace geekcom\ValidatorDocs;

use Illuminate\Validation\Validator as BaseValidator;

/**
 *
 * @author Daniel Rodrigues Lima
 * @email danielrodrigues-ti@hotmail.com
 */
class Validator extends BaseValidator
{
    /**
     * Valida o formato do cpf
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    protected function validateFormatoCpf($attribute, $value)
    {
        return preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $value) > 0;
    }

    /**
     * Valida o formato do cnpj
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    protected function validateFormatoCnpj($attribute, $value)
    {
        return preg_match('/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/', $value) > 0;
    }

    /**
     * Valida o formato do cpf ou cnpj
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    protected function validateFormatoCpfCnpj($attribute, $value)
    {
        return $this->validateFormatoCpf($attribute, $value) || $this->validateFormatoCnpj($attribute, $value);
    }
    
    /**
     * Valida o formato do PIS/PASEP/NIS/NIT
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    protected function validateFormatoNis($attribute, $value)
    {
        return preg_match('/^\d{3}\.\d{5}\.\d{2}-\d{1}$/', $value) > 0;
    }

    /**
     * Valida CPF
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    protected function validateCpf($attribute, $value)
    {
        $c = preg_replace('/\D/', '', $value);

        if (strlen($c) != 11 || preg_match("/^{$c[0]}{11}$/", $c)) {
            return false;
        }

        for ($s = 10, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--) ;

        if ($c[9] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        for ($s = 11, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--) ;

        if ($c[10] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        return true;

    }

    /**
     * Valida CNPJ
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    protected function validateCnpj($attribute, $value)
    {
        $c = preg_replace('/\D/', '', $value);

        if (strlen($c) != 14 || preg_match("/^{$c[0]}{14}$/", $c)) {
            return false;
        }

        $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for ($i = 0, $n = 0; $i < 12; $n += $c[$i] * $b[++$i]) ;

        if ($c[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        for ($i = 0, $n = 0; $i <= 12; $n += $c[$i] * $b[$i++]) ;

        if ($c[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        return true;

    }

    /**
     * Valida CNPJ ou CPF
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    protected function validateCpfCnpj($attribute, $value)
    {
        return ($this->validateCpf($attribute, $value) || $this->validateCnpj($attribute, $value));
    }

    /**
     * Valida CNH
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    protected function validateCnh($attribute, $value)
    {
        // Trecho retirado do respect validation

        $ret = false;

        if ((strlen($input = preg_replace('/[^\d]/', '', $value)) == 11)
            && (str_repeat($input[1], 11) != $input)
        ) {
            $dsc = 0;

            for ($i = 0, $j = 9, $v = 0; $i < 9; ++$i, --$j) {

                $v += (int)$input[$i] * $j;

            }

            if (($vl1 = $v % 11) >= 10) {

                $vl1 = 0;
                $dsc = 2;

            }

            for ($i = 0, $j = 1, $v = 0; $i < 9; ++$i, ++$j) {

                $v += (int)$input[$i] * $j;

            }

            $vl2 = ($x = ($v % 11)) >= 10 ? 0 : $x - $dsc;

            $ret = sprintf('%d%d', $vl1, $vl2) == substr($input, -2);
        }

        return $ret;
    }   

    /**
     * Valida Titulo de Eleitor
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    protected function validateTituloEleitor($attribute, $value)
    {

        $input = preg_replace('/[^\d]/', '', $value);

        $uf = substr($input, -4, 2);

        if (((strlen($input) < 5) || (strlen($input) > 13)) || 
        (str_repeat($input[1], strlen($input)) == $input) || 
        ($uf < 1 || $uf > 28)) {
            return false;
        }

        $dv = substr($input, -2);
        $base = 2;

        $sequencia = substr($input, 0, -4);

        for ($i = 0; $i < 2; $i++) { 
            $fator = 9;
            $soma = 0;

            for ($j = (strlen($sequencia) - 1); $j > -1; $j--) { 
                $soma += $sequencia[$j] * $fator;

                if ($fator == $base) {
                    $fator = 10;
                }

                $fator--;
            }

            $digito = $soma % 11;

            if (($digito == 0) and ($uf < 3)) {
                $digito = 1;
            } elseif ($digito == 10) {
                $digito = 0;
            }
            
            if ($dv[$i] != $digito) {
                return false;
            }

            switch ($i) {
                case '0':
                    $sequencia = $uf . $digito;

                    break;
            }
        }
        
        return true;
    }
    
    /**
     * Valida PIS/PASEP/NIS/NIT
     * @param string $attribute
     * @param string $value
     * @return boolean
     */
    protected function validateNis($attribute, $value)
    {
        $nis = sprintf('%011s', preg_replace('{\D}', '', $value));

        if (strlen($nis) != 11 || preg_match("/^{$nis[0]}{11}$/", $nis)) {
            return false;
        }

        for ($d = 0, $p = 2, $c = 9; $c >= 0; $c--, ($p < 9) ? $p++ : $p = 2) {
            $d += $nis[$c] * $p;
        }

        return ($nis[10] == (((10 * $d) % 11) % 10));
    }

    /**
     * Valida o formato do dinheiro brasileiro
     * 
     * @param string $attribute     O nome do atributo
     * @param string $value         O valor a ser analisado
     * @param string $parameters    Define se valida com R$ seguido de apenas um espaço e número ou sem
     * 
     * @example Sem o prefixo ativado 0,01 ou 1.234.567.890,12
     * @example Com o prefixo ativado R$ 0,01 ou R$ 1.234.567.890,12 (apenas um espaço entre símbolo e número)
     * 
     * @author Carlos Eduardo da Silva <carlosedasilva@gmail.com>
     * @return boolean
     */
    protected function validateBrl($attribute, $value, $parameters = null)
    {
        $pattern = ($parameters == 'prefixed') ? 
                    '/^R\$\s\d{1,3}(?:\.\d{3})*,\d{2}$/' : 
                    '/^\d{1,3}(?:\.\d{3})*,\d{2}$/';
        
        return preg_match($pattern, $value);
    }

}
