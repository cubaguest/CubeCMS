<?php
/**
 * Soubor s globálními funkcemi, použitelnými v celém frameworku. každá funkce
 * začíná prefixem vve_ např. vve_názevfunkce
 */

/**
 * Funkce převede znaky s diakritikou na znaky bez diakritiky
 * @param string $string -- řětezec pro převedení
 * @return string -- převedený řetězec
 */
function vve_to_ascii(&$string) {
   $string = strtr($string,
       Array(
       "á"=>'a',"â"=>'a',"ă"=>'a',"ä"=>'a',"č"=>'c',"ć"=>'c',"ç"=>'c',
       "ď"=>'d',"đ"=>'d',"é"=>'e',"ě"=>'e',"ę"=>'e',"ë"=>'e',
       "í"=>'i',"î"=>'i',"ľ"=>'l',"ĺ"=>'l',"ň"=>'n',"ń"=>'n',
       "ó"=>'o',"ô"=>'o',"ő"=>'o',"ö"=>'o',"ř"=>'r',"š"=>'s',"ś"=>'s',"ß" => 's',
       "ť"=>'t',"ú"=>'u',"ů"=>'u',"ű"=>'u',"ü"=>'u',"ý"=>'y',"ž"=>'z',"ź"=>'z',

       "Á"=>'A',"Â"=>'A',"Ă"=>'A',"Ä"=>'A',"Č"=>'C',"Ć"=>'C',"Ç"=>'C',
       "Ď"=>'D',"Đ"=>'D',"É"=>'E',"Ě"=>'E',"Ę"=>'E',"Ë"=>'E',
       "Í"=>'I',"Î"=>'I',"Ľ"=>'L',"Ĺ"=>'L',"Ň"=>'N',"Ń"=>'N',
       "Ó"=>'O',"Ô"=>'O',"Ő"=>'O',"Ö"=>'O',"Ř"=>'R',"Š"=>'S',"Ś"=>'S',
       "Ť"=>'T',"Ú"=>'U',"Ů"=>'U',"Ű"=>'U',"Ü"=>'U',"Ý"=>'Y',"Ž"=>'Z',"Ź"=>'Z'));
   $string = StrToLower($string); //velká písmena nahradí malými.
   return $string;
}

/**
 * Funkce odstrani nepovolené znaky a diakritiku pro vložení jako klíč do db
 * @param string/array $string -- řetězec nebo pole pro převedení
 * @return string/array -- řetězec nebo pole s převedenými znaky
 */
function vve_cr_url_key($string) {
   if(is_array($string)) {
      foreach ($string as $key => $variable) {
         $string[$key] = vve_cr_url_key($variable);
      }
   } else {
      $string = vve_to_ascii($string);
      $string = preg_replace("/[ \_-]{1,}/", "-", $string);
      $string = preg_replace("/[().\"\'!?<>,]?/", "", $string);
   }
   return $string;
}
?>
