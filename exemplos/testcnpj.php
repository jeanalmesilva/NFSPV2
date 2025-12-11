<?php
  /**
   * Valida um CNPJ com 14 caracteres, onde os 12 primeiros podem ser alfanuméricos e os 2 últimos são dígitos verificadores.
   * A validação é feita utilizando o método do módulo 11, e para caracteres alfabéticos o valor ASCII é subtraído por 48.
   *
   * @link http://normas.receita.fazenda.gov.br/sijut2consulta/link.action?idAto=141102
   * @param string $cnpj O CNPJ a ser validado, com 14 caracteres alfanuméricos (12 primeiros) e numéricos (2 últimos).
   * @return bool Retorna true se o CNPJ for válido, false caso contrário.
   * @author Bruno Constantino
   * @date 2024-10-29
   */
  function isCNPJ($cnpj) {
      $c = preg_replace('/[^A-Z0-9]/', '', strtoupper($cnpj));
      if (strlen($c) !== 14 || preg_match('/^0{14}$/', $c)) return false;
  
      $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
      $getCharValue = function($char) {
          return ($char >= 'A') ? ord($char) - 48 : (int)$char;
      };
  
      $checkDigit = function($pos) use ($c, $b, $getCharValue) {
          $sum = 0;
          for ($i = 0; $i < $pos; $i++) {
              $sum += $getCharValue($c[$i]) * $b[$i + ($pos === 12)];
          }
          $n = $sum % 11;
          return $c[$pos] == ($n < 2 ? 0 : 11 - $n);
      };
  
      return $checkDigit(12) && $checkDigit(13);
  }
 
 // Exemplos de uso
var_dump( isCNPJ("00.000.000/0001-91") );
var_dump( isCNPJ("59.952.259/0001-85") );
var_dump( isCNPJ("12ABC34501DE38") );
var_dump( isCNPJ("12aBc34501DE35") );
?>