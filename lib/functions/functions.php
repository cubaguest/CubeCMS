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
function vve_to_ascii(&$string)
{
   if(defined('VVE_USE_ICONV') && VVE_USE_ICONV == false){
      $string =  strtr($string, array("\xc3\xa1"=>"a","\xc3\xa4"=>"a","\xc4\x8d"=>"c","\xc4\x8f"=>"d","\xc3\xa9"=>"e","\xc4\x9b"=>"e","\xc3\xad"=>"i","\xc4\xbe"=>"l","\xc4\xba"=>"l","\xc5\x88"=>"n","\xc3\xb3"=>"o","\xc3\xb6"=>"o","\xc5\x91"=>"o","\xc3\xb4"=>"o","\xc5\x99"=>"r","\xc5\x95"=>"r","\xc5\xa1"=>"s","\xc5\xa5"=>"t","\xc3\xba"=>"u","\xc5\xaf"=>"u","\xc3\xbc"=>"u","\xc5\xb1"=>"u","\xc3\xbd"=>"y","\xc5\xbe"=>"z","\xc3\x81"=>"A","\xc3\x84"=>"A","\xc4\x8c"=>"C","\xc4\x8e"=>"D","\xc3\x89"=>"E","\xc4\x9a"=>"E","\xc3\x8d"=>"I","\xc4\xbd"=>"L","\xc4\xb9"=>"L","\xc5\x87"=>"N","\xc3\x93"=>"O","\xc3\x96"=>"O","\xc5\x90"=>"O","\xc3\x94"=>"O","\xc5\x98"=>"R","\xc5\x94"=>"R","\xc5\xa0"=>"S","\xc5\xa4"=>"T","\xc3\x9a"=>"U","\xc5\xae"=>"U","\xc3\x9c"=>"U","\xc5\xb0"=>"U","\xc3\x9d"=>"Y","\xc5\xbd"=>"Z")); 
   } else {
      $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
   }
   return $string;
}

/**
 * Funkce odstrani nepovolené znaky a diakritiku pro vložení jako klíč do db
 * @param string/array $string -- řetězec nebo pole pro převedení
 * @return string/array -- řetězec nebo pole s převedenými znaky
 */
function vve_cr_url_key($string, $removeSlashes = true)
{
   if (is_null($string)) {
      return $string;
   } else if (is_array($string)) {
      foreach ($string as $key => $variable) {
         $string[$key] = vve_cr_url_key($variable, $removeSlashes);
      }
   } else {
      $string = strip_tags($string);
      $string = vve_to_ascii($string);
      $slashes = null;
      if ($removeSlashes) {
         $slashes = '\/';
      }
      $regexp = array('/[^a-z0-9\/ _-]+/i', '/[ ' . $slashes . '-]+/', '/[\/]+/');
      $replacements = array('', '-', URL_SEPARATOR);
      $string = strtolower(preg_replace($regexp, $replacements, $string) );
   }
   return $string;
}

/**
 * Funkce odstrani nepovolené znaky a diakritiku pro vytvoření bezpečného názvu souboru
 * @param string/array $string -- řetězec nebo pole pro převedení
 * @return string/array -- řetězec nebo pole s převedenými znaky
 */
function vve_cr_safe_file_name($string)
{
   if (is_array($string)) {
      foreach ($string as $key => $variable) {
         $string[$key] = vve_cr_safe_file_name($variable);
      }
   } else {
      $string = vve_to_ascii($string);
      $string = preg_replace("/[ +]{1,}/", "-", $string);
      $string = preg_replace("/[\/()\"\'!?,]?/", "", $string);
   }
   return $string;
}

/**
 * Funkce rozparsuje řetězec z konfoigurace podle odělovače (možné variany: "left;right" nebo ("left=0;right=2"))
 * @param string $value -- řetězec pro parsování
 * @param string $delimiter -- oddělovač
 * @return array -- pole s hodnotami
 */
function vve_parse_cfg_value($value, $delimiter = ';')
{
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
function vve_create_full_url_path(&$string, $atrNames = 'src|href')
{
   $string = preg_replace('/(src|href)="(?!http)([^"]+)"/i', '\\1="' . Url_Link::getMainWebDir() . '\\2"', $string);
   return $string;
}

/**
 * Funkce odstraní html tagy z řetězce a pole (rekurzivní)
 * @param mixed $value -- samotný řetězec nebo pole
 * @return mixed -- řětezce nebo pole bez html tagů
 */
function vve_strip_tags($value)
{
   if (is_array($value)) {
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
 * @param int/string/DateTime $timestamp -- (option) čas
 * @return string -- formátované datum
 * <p>%d - Day of the month without leading zeros - 1 to 31</p>
 * <p>%D - Day of the month, 2 digits with leading zeros</p>
 * <p>%l - An abbreviated textual representation of the day</p>
 * <p>%L - A full textual representation of the day - Sunday through Saturday</p>
 * <p>%m - Numeric representation of a month, without leading zeros - 1 through 12</p>
 * <p>%M - Numeric representation of a month, with leading zeros - 01 through 12</p>
 * <p>%f(%b) - Abbreviated month name, based on the locale - Jan through Dec</p>
 * <p>%F(%B) - Full month name, based on the locale - January through December</p>
 * <p>%J - Celý název měsíce za řadovou číslovkou - ledna až prosince</p>
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
function vve_date($format, $timestamp = null)
{
   if ($timestamp instanceof DateTime) {
      $timestamp = $timestamp->format("U");
   } else if ($timestamp === null) {
      $timestamp = time();
   } else if(is_string($timestamp)){
      $timestamp = new DateTime($timestamp);
      $timestamp = $timestamp->format("U");
   }

   $replacementArray = array(
      '%d' => array('func' => 'date', 'param' => 'j'),
      '%D' => array('func' => 'date', 'param' => 'd'),
      '%l' => array('func' => 'strftime', 'param' => '%a'),
      '%L' => array('func' => 'strftime', 'param' => '%A'),
      '%m' => array('func' => 'date', 'param' => 'n'),
      '%M' => array('func' => 'date', 'param' => 'm'),
      '%f' => array('func' => 'strftime', 'param' => '%b'),
      '%b' => array('func' => 'strftime', 'param' => '%b'),
      '%F' => array('func' => 'strftime', 'param' => '%B'),
      '%B' => array('func' => 'strftime', 'param' => '%B'),
      '%x' => array('func' => 'strftime', 'param' => '%x'),
      '%X' => array('func' => 'strftime', 'param' => '%X'),
      '%Y' => array('func' => 'date', 'param' => 'Y'),
      '%y' => array('func' => 'date', 'param' => 'y'),
      '%G' => array('func' => 'date', 'param' => 'G'),
      '%H' => array('func' => 'date', 'param' => 'H'),
      '%g' => array('func' => 'date', 'param' => 'g'),
      '%h' => array('func' => 'date', 'param' => 'h'),
      '%i' => array('func' => 'date', 'param' => 'i'),
      '%s' => array('func' => 'date', 'param' => 's'),
      '%J' => array('func' => '_vve_date_mtr', 'param' => '')
   );

   foreach ($replacementArray as $str => $func) {
      $format = str_replace($str, call_user_func_array($func['func'], array($func['param'], (int) $timestamp)), $format);
   }
   return $format;
}

/**              
 * Interní funkce pro překlad názvu měsíce po řadové čísloce
 * @param $param
 * @param $timestam
 * @return string
 */
function _vve_date_mtr($param, $timestam){
   $monthTR = array(
      'cs' => array(
         1 => 'ledna',
         'února',
         'března',
         'dubna',
         'května',
         'června',
         'července',
         'srpna',
         'září',
         'října',
         'listopadu',
         'prosince',
      )
   );
   return isset($monthTR[Locales::getLang()]) ? $monthTR[Locales::getLang()][date('n', $timestam)] : strftime('%B', $timestam);
}

/**
 * Metoda kontroluje jestli zadaná URL adresa existuje
 * @param string $url -- url adresa
 * @return bool -- true pokud existuje
 */
function vve_url_exists($url)
{
   $headers = @get_headers($url);
   return (bool) preg_match('/^HTTP\/\d\.\d\s+(200|301|302)/', $headers[0]);
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
function vve_array_insert($array, $pos, $val, $valkey = null)
{
   $array2 = array_splice($array, $pos);
   if ($valkey == null) {
      $array[] = $val;
   } else {
      $array[$valkey] = $val;
   }
   $array = array_merge($array, $array2);

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
function vve_array_insert_by_key($array, $key, $val, $valkey = null, $sort = 'after')
{
   $pos = 0;
   foreach ($array as $lkey => $lval) {
      if ($lkey == $key)
         break;
      $pos++;
   }
   if ($sort == 'after') {
      $array = vve_array_insert($array, $pos + 1, $val, $valkey);
   } else {
      $array = vve_array_insert($array, $pos, $val, $valkey);
   }
   return $array;
}

/**
 * Funcke odstraní html komentáře
 * @param mixed $str -- řetězec nebo pole
 */
function vve_strip_html_comment($str)
{
   if (is_array($str)) {
      foreach ($str as $key => $s) {
         $str[$key] = vve_strip_html_comment($s);
      }
   } else {
      $str = preg_replace('/<!--(.|\s)*?-->/', '', $str);
   }
   return $str;
}

/**
 * Funkce rozparsuje a převede hodnotu velikosti na bajty
 * @param string $param -- řetězec s velikostí (např.: 5M => 5*1024*1024, 2k => 2*1024, ...)
 */
function vve_parse_size($str)
{
   $val = trim($str);
   $last = strtolower($str[strlen($str) - 1]);
   switch ($last) {
      case 'g': $val *= 1024;
      case 'm': $val *= 1024;
      case 'k': $val *= 1024;
   }
   return $val;
}

/**
 * Funkce vytvoří řetězec s velikostí souboru a přípony G/M/K
 * @param int $size -- velikost v Bajtech
 * @param int $round -- počet desetiných míst (def.: 1)
 * @return string
 */
function vve_create_size_str($size, $round = 1)
{
   if($size > 1073741824){
      return round($size/1073741824,$round)." GB";
   } else if($size > 1048576){
      return round($size/1048576,$round)." MB";
   } else if($size > 1024) {
      return round($size/1024,$round)." KB";
   } else {
      return $size." B";
   }
}

/**
 * Převede BR tagy na nl
 *
 * @param string - řetězec, který se má převést
 * @return string - převedený řetězec
 */
function vve_br2nl($string)
{
    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
}

/**
 * Funkce vygeneruje token
 * @param int $len -- délka tokenu
 * @param bool $md5 -- zakódování pomocí md5 (vhodné pro url adresy)
 * @return string
 * @author Andrew Johnson, Jun 15, 2009 (modified Sep 13, 2009) 
 * @see http://www.itnewb.com/tutorial/Generating-Session-IDs-and-Random-Passwords-with-PHP
 */
function vve_generate_token($len = 32, $md5 = true)
{

   # Seed random number generator
   # Only needed for PHP versions prior to 4.2
   mt_srand((double) microtime() * 1000000);

   # Array of characters, adjust as desired
   $chars = array(
      'Q', '@', '8', 'y', '%', '^', '5', 'Z', '(', 'G', '_', 'O', '`',
      'S', '-', 'N', '<', 'D', '{', '}', '[', ']', 'h', ';', 'W', '.',
      '/', '|', ':', '1', 'E', 'L', '4', '&', '6', '7', '#', '9', 'a',
      'A', 'b', 'B', '~', 'C', 'd', '>', 'e', '2', 'f', 'P', 'g', ')',
      '?', 'H', 'i', 'X', 'U', 'J', 'k', 'r', 'l', '3', 't', 'M', 'n',
      '=', 'o', '+', 'p', 'F', 'q', '!', 'K', 'R', 's', 'c', 'm', 'T',
      'v', 'j', 'u', 'V', 'w', ',', 'x', 'I', '$', 'Y', 'z', '*'
   );

   # Array indice friendly number of chars; empty token string
   $numChars = count($chars) - 1;
   $token = '';

   # Create random token at the specified length
   for ($i = 0; $i < $len; $i++)
      $token .= $chars[mt_rand(0, $numChars)];

   # Should token be run through md5?
   if ($md5) {

      # Number of 32 char chunks
      $chunks = ceil(strlen($token) / 32);
      $md5token = '';

      # Run each chunk through md5
      for ($i = 1; $i <= $chunks; $i++)
         $md5token .= md5(substr($token, $i * 32 - 32, 32));

      # Trim the token
      $token = substr($md5token, 0, $len);
   } 
   return $token;
}

function vve_image_cacher($path, $width = null, $height = null, $crop = false){
   if(strpos($path, AppCore::getAppWebDir()) !== false){ // absolutní cesta
      $path = str_replace(array(
         AppCore::getAppWebDir(), DIRECTORY_SEPARATOR,
      ),array(
         Url_Request::getBaseWebDir(), "/",
      ), $path);
   }

   // check if http
   if(substr($path, 0, 4) != "http"){
      $path = Url_Request::getBaseWebDir().$path;
   }

   $sizes = $width.'x'.$height.($crop == false ? '' : 'c');
   $origPath = str_replace(Url_Request::getBaseWebDir(), '', $path);
   $cachePath = 'cache/imgc/'.Template::face().'/'.$sizes.'/'.$origPath;
   $hash = sha1($sizes.VVE_DB_PASSWD);
   // pokud má obrázek url adresu ze současného webu
   if(strpos($path, Url_Request::getBaseWebDir()) !== false){
      $path = str_replace(Url_Request::getBaseWebDir(),
         Url_Request::getBaseWebDir().'cache/imgc/'.Template::face().'/'.$sizes.'/', $path);
      $path .= "?hash=". urlencode($hash);
   }
   // pokud je obrázek přímo z data
   else if(strpos($path, VVE_DATA_DIR."/") === 0){
      $path = Url_Request::getBaseWebDir().'cache/imgc/'.Template::face().'/'.$sizes.'/'.$path."?hash=". urlencode($hash);
   }
   // create abs paths
   $realOrigPath = realpath($origPath);
   $realCachedPath = realpath($cachePath);
   // check change time if cached is oldest then original remove
   if(is_file($realCachedPath) && filemtime($realCachedPath) < filemtime($realOrigPath)){
      @unlink($realCachedPath);
      $path .= '&t='.time();
   }
   return $path;
}

/**
 * Metoda vrací řetězec v daném jazyce, nebo pokud je prázdný tak ve výchoím
 * @param array $string
 */
function vve_get_lang_string($string)
{
   if(isset($string[Locales::getLang()]) && $string[Locales::getLang()] != null){
      return $string[Locales::getLang()];
   } else if(isset($string[Locales::getDefaultLang()]) && $string[Locales::getDefaultLang()] != null){
      return $string[Locales::getDefaultLang()];
   }
   return $string;
}
?>
