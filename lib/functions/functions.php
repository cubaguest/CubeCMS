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
//   $string = strtr($string,
//           Array(
//           "á"=>'a',"â"=>'a',"ă"=>'a',"ä"=>'a',"č"=>'c',"ć"=>'c',"ç"=>'c',
//           "ď"=>'d',"đ"=>'d',"é"=>'e',"ě"=>'e',"ę"=>'e',"ë"=>'e',
//           "í"=>'i',"î"=>'i',"ľ"=>'l',"ĺ"=>'l',"ň"=>'n',"ń"=>'n',
//           "ó"=>'o',"ô"=>'o',"ő"=>'o',"ö"=>'o',"ř"=>'r',"š"=>'s',"ś"=>'s',"ß" => 's',
//           "ť"=>'t',"ú"=>'u',"ů"=>'u',"ű"=>'u',"ü"=>'u',"ý"=>'y',"ž"=>'z',"ź"=>'z',
//
//           "Á"=>'A',"Â"=>'A',"Ă"=>'A',"Ä"=>'A',"Č"=>'C',"Ć"=>'C',"Ç"=>'C',
//           "Ď"=>'D',"Đ"=>'D',"É"=>'E',"Ě"=>'E',"Ę"=>'E',"Ë"=>'E',
//           "Í"=>'I',"Î"=>'I',"Ľ"=>'L',"Ĺ"=>'L',"Ň"=>'N',"Ń"=>'N',
//           "Ó"=>'O',"Ô"=>'O',"Ő"=>'O',"Ö"=>'O',"Ř"=>'R',"Š"=>'S',"Ś"=>'S',
//           "Ť"=>'T',"Ú"=>'U',"Ů"=>'U',"Ű"=>'U',"Ü"=>'U',"Ý"=>'Y',"Ž"=>'Z',"Ź"=>'Z'));
//   $string = StrToLower($string); //velká písmena nahradí malými.
   $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
   return $string;
}

/**
 * Funkce odstrani nepovolené znaky a diakritiku pro vložení jako klíč do db
 * @param string/array $string -- řetězec nebo pole pro převedení
 * @return string/array -- řetězec nebo pole s převedenými znaky
 */
function vve_cr_url_key($string, $removeSlashes = true) {
   if(is_null($string)) {
      return $string;
   } else if(is_array($string)) {
      foreach ($string as $key => $variable) {
         $string[$key] = vve_cr_url_key($variable);
      }
   } else {
      $string = vve_to_ascii(strip_tags($string));
      $slashes = null;
      if($removeSlashes){
         $slashes = '\/';
      }
      $regexp = array('/[^a-z0-9\/ _-]+/i', '/[ '.$slashes.'-]+/', '/[\/]+/');
      $replacements = array('', '-', URL_SEPARATOR);
      $string = preg_replace($regexp, $replacements, $string);
   }
   return $string;
}

/**
 * Funkce odstrani nepovolené znaky a diakritiku pro vytvoření bezpečného názvu souboru
 * @param string/array $string -- řetězec nebo pole pro převedení
 * @return string/array -- řetězec nebo pole s převedenými znaky
 */
function vve_cr_safe_file_name($string) {
   if(is_array($string)) {
      foreach ($string as $key => $variable) {
         $string[$key] = vve_cr_safe_file_name($variable);
      }
   } else {
      $string = vve_to_ascii($string);
      $string = preg_replace("/[ ]{1,}/", "-", $string);
      $string = preg_replace("/[()\"\'!?,]?/", "", $string);
   }
   return $string;
}

/**
 * Funkce rozparsuje řetězec z konfoigurace podle odělovače (možné variany: "left;right" nebo ("left=0;right=2"))
 * @param string $value -- řetězec pro parsování
 * @param string $delimiter -- oddělovač
 * @return array -- pole s hodnotami
 */
function vve_parse_cfg_value($value, $delimiter = ';') {
   $arr = $retArr = array();
   $arr = explode($delimiter, $value);

   foreach ($arr as $ret) {
      $retArr[$ret] = $ret;
   }

   return $retArr;
}

/**
 * Funkce přepíše všechny relativní adresy v řetězce a absolutní
 * @param string &$string -- samotný řetězec
 * @param string $atrName -- (option) název atribut, který se má opravovat (default: src a href)
 * @return string -- řetězec s opravenou adresou
 */
function vve_create_full_url_path(&$string, $atrNames = 'src|href') {
   $string = preg_replace('/(src|href)="(?!http)([^"]+)"/i', '\\1="'.Url_Link::getMainWebDir().'\\2"', $string);
   return $string;
}

/**
 * Funkce odstraní html tagy z řetězce a pole (rekurzivní)
 * @param mixed $value -- samotný řetězec nebo pole
 * @return mixed -- řětezce nebo pole bez html tagů
 */
function vve_strip_tags($value) {
   if(is_array($value)) {
      foreach ($value as $key => $val) {
         $value[$key] = vve_strip_tags($val);
      }
   } else {
      $value = strip_tags($value);
   }
   return $value;
}

/**
 * Metoda podobná date a strftime, umožňuje jednodušší vypsání data a to i podle locales
 * @param string $format -- formát viz. dále
 * @param int $timestamp -- (option) timestamp
 * @return string -- formátované datum
 * <p>%d - Day of the month without leading zeros - 1 to 31</p>
 * <p>%D - A textual representation of a day, three letters - Mon through Sun</p>
 * <p>%l - Hour in 12-hour format, with a space preceeding single digits - 1 through 12</p>
 * <p>%L - A full textual representation of the day - Sunday through Saturday</p>
 * <p>%m - Numeric representation of a month, without leading zeros - 1 through 12</p>
 * <p>%M - Numeric representation of a month, with leading zeros - 01 through 12</p>
 * <p>%f(%b) - Abbreviated month name, based on the locale - Jan through Dec</p>
 * <p>%F(%B) - Full month name, based on the locale - January through December</p>
 * <p>%Y - A full numeric representation of a year, 4 digits - Examples: 1999 or 2003</p>
 * <p>%y - A two digit representation of a year - Examples: 99 or 03</p>
 * <p>%G - 24-hour format of an hour without leading zeros - 0 through 23</p>
 * <p>%g - 12-hour format of an hour without leading zeros - 1 through 12</p>
 * <p>%H - 24-hour format of an hour with leading zeros - 00 through 23</p>
 * <p>%h - 12-hour format of an hour with leading zeros - 01 through 12</p>
 * <p>%i - Minutes with leading zeros - 00 to 59</p>
 * <p>%s - Seconds, with leading zeros - 00 to 59</p>
 * <p>%x - Preferred date representation based on locale, without the time - Example: 02/05/09 for February 5, 2009</p>
 * <p>%X - Preferred time representation based on locale, without the date - Example: 03:59:16 or 15:59:16</p>
 */
function vve_date($format, $timestamp = null) {
   if($timestamp instanceof DateTime) {
      $timestamp = $timestamp->format("U");
   } else if($timestamp === null) {
      $timestamp = time();
   }

   $replacementArray = array(
           '%d' => array('func' => 'date', 'param' =>'j'),
           '%D' => array('func' => 'date', 'param' =>'d'),
           '%l' => array('func' => 'strftime', 'param' =>'%a'),
           '%L' => array('func' => 'strftime', 'param' =>'%A'),
           '%m' => array('func' => 'date', 'param' =>'n'),
           '%M' => array('func' => 'date', 'param' =>'m'),
           '%f' => array('func' => 'strftime', 'param' =>'%b'),
           '%b' => array('func' => 'strftime', 'param' =>'%b'),
           '%F' => array('func' => 'strftime', 'param' =>'%B'),
           '%B' => array('func' => 'strftime', 'param' =>'%B'),
           '%x' => array('func' => 'strftime', 'param' =>'%x'),
           '%X' => array('func' => 'strftime', 'param' =>'%X'),
           '%Y' => array('func' => 'date', 'param' =>'Y'),
           '%y' => array('func' => 'date', 'param' =>'y'),
           '%G' => array('func' => 'date', 'param' =>'G'),
           '%H' => array('func' => 'date', 'param' =>'H'),
           '%g' => array('func' => 'date', 'param' =>'g'),
           '%h' => array('func' => 'date', 'param' =>'h'),
           '%i' => array('func' => 'date', 'param' =>'i'),
           '%s' => array('func' => 'date', 'param' =>'s')
   );

   foreach ($replacementArray as $str => $func) {
      $format = str_replace($str, call_user_func_array($func['func'], array($func['param'], (int)$timestamp)), $format);
   }
   return $format;
}

/**
 * Metoda kontroluje jestli zadaná URL adresa existuje
 * @param string $url -- url adresa
 * @return bool -- true pokud existuje
 */
function vve_url_exists($url){
    $headers = @get_headers($url);
    return (bool)preg_match('/^HTTP\/\d\.\d\s+(200|301|302)/', $headers[0]);
}

/**
 * Funkce vloží požadovanou hodnotu do pole na danou pozici
 * @param array $array -- pole kde se má prvek vložit
 * @param int $pos -- pozice na kterou se má prvek vložit (začíná se od 0)
 * @param mixed $val -- hodnota pro vložení
 * @param string $valkey -- (option) pokud není zadáno použije se první volný index
 *
 * @return array -- upravené pole, v případě chyby false
 */
function vve_array_insert($array, $pos, $val, $valkey = null){
    $array2 = array_splice($array,$pos);
    if($valkey == null){
      $array[] = $val;
    } else {
      $array[$valkey] = $val;
    }
    $array = array_merge($array,$array2);

    return $array;
}

/**
 * Funkce vloží požadovanou hodnotu do pole za dany klíč
 * @param array $array -- pole kde se má prvek vložit
 * @param string $pos -- pozice na kterou se má prvek vložit (klíč)
 * @param mixed $val -- hodnota pro vložení
 * @param string $valkey -- (option) pokud není zadáno použije se první volný index
 * @param string $pos -- (option) (after|before) jestli se má prvek vložit před daný klíč nebo zaněj
 *
 * @return array -- upravené pole, v případě chyby false
 */
function vve_array_insert_by_key($array, $key, $val, $valkey = null, $sort = 'after'){
    $pos = 0;
    foreach ($array as $lkey => $lval){
       if($lkey == $key) break;
       $pos++;
    }
    if($sort == 'after'){
      $array = vve_array_insert($array, $pos+1, $val, $valkey);
    } else {
       $array = vve_array_insert($array, $pos, $val, $valkey);
    }
    return $array;
}

/**
 * Funcke odstraní html komentáře
 * @param mixed $str -- řetězec nebo pole
 */
function vve_strip_html_comment($str){
   if(is_array($str)){
      foreach ($str as $key => $s) {
         $str[$key] = vve_strip_html_comment($s);
      }
   } else {
      $str = preg_replace('/<!--(.|\s)*?-->/', '', $str);
   }
   return $str;
}

/**
 * vytvoření funkce pokud neexistuje
 */
if (!function_exists('get_called_class')){
   function get_called_class() {
      $bt = debug_backtrace();
      $lines = file($bt[1]['file']);
      preg_match('/([a-zA-Z0-9\_]+)::'.$bt[1]['function'].'/', $lines[$bt[1]['line']-1], $matches);
      return $matches[1];
   }
}
?>
