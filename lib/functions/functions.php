<?php

/**
 * Soubor s globálními funkcemi, použitelnými v celém frameworku. každá funkce
 * začíná prefixem cube_cms_ např. cube_cms_názevfunkce
 * @todo převést na objekty typu Utils
 */

/**
 * Funkce převede znaky s diakritikou na znaky bez diakritiky
 * @param string $string -- řětezec pro převedení
 * @return string -- převedený řetězec
 */
function vve_to_ascii(&$string)
{
   return Utils_String::toAscii($string);
}


/**
 * Funkce odstrani nepovolené znaky a diakritiku pro vložení jako klíč do db
 * @param string/array $string -- řetězec nebo pole pro převedení
 * @return string/array -- řetězec nebo pole s převedenými znaky
 */
function vve_cr_url_key($string, $removeSlashes = true)
{
   return Utils_Url::toUrlKey($string, $removeSlashes);
}

/**
 * Funkce odstrani nepovolené znaky a diakritiku pro vytvoření bezpečného názvu souboru
 * @param string/array $string -- řetězec nebo pole pro převedení
 * @return string/array -- řetězec nebo pole s převedenými znaky
 */
function vve_cr_safe_file_name($string)
{
   return Utils_String::toSafeFileName($string);
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
   return Utils_Url::createFullUrlPaths($string, $atrNames);
}

/**
 * Funkce odstraní html tagy z řetězce a pole (rekurzivní)
 * @param mixed $value -- samotný řetězec nebo pole
 * @return mixed -- řětezce nebo pole bez html tagů
 */
function vve_strip_tags($value, $allowedtags = null)
{
   return Utils_Html::stripTags($value, $allowedtags);
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
   return Utils_DateTime::fdate($format, $timestamp);
}

///  TODO HERE !!!

/**
 * Metoda kontroluje jestli zadaná URL adresa existuje
 * @param string $url -- url adresa
 * @return bool -- true pokud existuje
 */
function vve_url_exists($url)
{
   return Utils_Url::exist($url);
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
   return Utils_Array::insert($array, $pos, $val, $valkey);
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
   return Utils_Array::insertByKey($array, $key, $val, $valkey, $sort);
}

/**
 * Funcke odstraní html komentáře
 * @param mixed $str -- řetězec nebo pole
 */
function vve_strip_html_comment($str)
{
   return Utils_Html::stripHtmlComment($str);
}

/**
 * Funkce rozparsuje a převede hodnotu velikosti na bajty
 * @param string $param -- řetězec s velikostí (např.: 5M => 5*1024*1024, 2k => 2*1024, ...)
 */
function vve_parse_size($str) 
{
   return Utils_String::parseSize($str);
}

/**
 * Funkce vytvoří řetězec s velikostí souboru a přípony G/M/K
 * @param int $size -- velikost v Bajtech
 * @param int $round -- počet desetiných míst (def.: 1)
 * @return string
 */
function vve_create_size_str($size, $round = 1)
{
   return Utils_String::createSizeString($size, $round);
}

/**
 * Převede BR tagy na nl
 *
 * @param string - řetězec, který se má převést
 * @return string - převedený řetězec
 */
function vve_br2nl($string) 
{
   return Utils_String::br2nl($string);
}

/**
 * Funkce vygeneruje token
 * @param int $len -- délka tokenu
 * @param bool $md5 -- zakódování pomocí md5 (vhodné pro url adresy)
 * @return string
 * @author Andrew Johnson, Jun 15, 2009 (modified Sep 13, 2009) 
 * @see http://www.itnewb.com/tutorial/Generating-Session-IDs-and-Random-Passwords-with-PHP
 */
function vve_generate_token($len = 32, $md5 = true){
   return cube_cms_generate_token($len, $md5);
}

function vve_image_cacher($path, $width = null, $height = null, $crop = false){
   return Utils_Image::cache($path, $width, $height, $crop);
}

/**
 * Metoda vrací řetězec v daném jazyce, nebo pokud je prázdný tak ve výchoím
 * @param array $string
 */
function vve_get_lang_string($string)
{
   return Utils_String::getLangString($string);
}


