<?php

/**
 * Třída pro usnadnění práce s řetězci
 *
 * @copyright  	Copyright (c) 2008-2014 Jakub Matas
 * @version    	$Id: $ CubeCMS 8.0.4 $Revision:  $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 */
class Utils_String {

   /**
    * Funkce převede znaky s diakritikou na znaky bez diakritiky
    * @param string $string -- řětezec pro převedení
    * @return string -- převedený řetězec
    */
   public static function toAscii($string, $lang = false)
   {
      // pokud je azbuke a jazyk, který azbuku má, proveď převod z azbuky na latinku
      if($lang && in_array($lang, array('ru'))){
         $string = strtr($string, array(
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'jj', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'eh', 'ю' => 'ju', 'я' => 'ja',
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'JO', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I', 'Й' => 'JJ', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'KH', 'Ц' => 'C', 'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SHH', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'EH', 'Ю' => 'JU', 'Я' => 'JA',
         ));
      }
      if(defined('VVE_USE_ICONV') && VVE_USE_ICONV == false){
         $string =  strtr($string, array("\xc3\xa1"=>"a","\xc3\xa4"=>"a","\xc4\x8d"=>"c","\xc4\x8f"=>"d","\xc3\xa9"=>"e","\xc4\x9b"=>"e","\xc3\xad"=>"i","\xc4\xbe"=>"l","\xc4\xba"=>"l","\xc5\x88"=>"n","\xc3\xb3"=>"o","\xc3\xb6"=>"o","\xc5\x91"=>"o","\xc3\xb4"=>"o","\xc5\x99"=>"r","\xc5\x95"=>"r","\xc5\xa1"=>"s","\xc5\xa5"=>"t","\xc3\xba"=>"u","\xc5\xaf"=>"u","\xc3\xbc"=>"u","\xc5\xb1"=>"u","\xc3\xbd"=>"y","\xc5\xbe"=>"z","\xc3\x81"=>"A","\xc3\x84"=>"A","\xc4\x8c"=>"C","\xc4\x8e"=>"D","\xc3\x89"=>"E","\xc4\x9a"=>"E","\xc3\x8d"=>"I","\xc4\xbd"=>"L","\xc4\xb9"=>"L","\xc5\x87"=>"N","\xc3\x93"=>"O","\xc3\x96"=>"O","\xc5\x90"=>"O","\xc3\x94"=>"O","\xc5\x98"=>"R","\xc5\x94"=>"R","\xc5\xa0"=>"S","\xc5\xa4"=>"T","\xc3\x9a"=>"U","\xc5\xae"=>"U","\xc3\x9c"=>"U","\xc5\xb0"=>"U","\xc3\x9d"=>"Y","\xc5\xbd"=>"Z")); 
      } else {
         $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
      }
      return $string;
   }

   /**
    * Funkce odstrani nepovolené znaky a diakritiku pro vytvoření bezpečného názvu souboru
    * @param string/array $string -- řetězec nebo pole pro převedení
    * @return string/array -- řetězec nebo pole s převedenými znaky
    */
   public static function toSafeFileName($string)
   {
      if (is_array($string)) {
         foreach ($string as $key => $variable) {
            $string[$key] = self::toSafeFileName($variable);
         }
      } else {
         $string = vve_to_ascii($string);
         $string = preg_replace(array(
               "/[ +]{1,}/",
               "/[\/\()\"\'!?,&]?/",
               "/[-]{2,}/",
             ), array(
                 "-",
                 "",
                 "-",
            ), $string);
      }
      return $string;
   }

   /**
    * Funkce rozparsuje a převede hodnotu velikosti na bajty
    * @param string $param -- řetězec s velikostí (např.: 5M => 5*1024*1024, 2k => 2*1024, ...)
    */
   public static function parseSize($str)
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
   public static function createSizeString($size, $round = 1)
   {
      if ($size > 1073741824) {
         return round($size / 1073741824, $round) . " GB";
      } else if ($size > 1048576) {
         return round($size / 1048576, $round) . " MB";
      } else if ($size > 1024) {
         return round($size / 1024, $round) . " KB";
      } else {
         return $size . " B";
      }
   }

   /**
    * Převede BR tagy na nl
    *
    * @param string - řetězec, který se má převést
    * @return string - převedený řetězec
    */
   public static function br2nl($string)
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
   public static function generateToken($len = 32, $md5 = true)
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

   /**
    * Metoda vrací řetězec v daném jazyce, nebo pokud je prázdný tak ve výchoím
    * @param array $string
    */
   public static function getLangString($string)
   {
      if (isset($string[Locales::getLang()]) && $string[Locales::getLang()] != null) {
         return $string[Locales::getLang()];
      } else if (isset($string[Locales::getDefaultLang()]) && $string[Locales::getDefaultLang()] != null) {
         return $string[Locales::getDefaultLang()];
      }
      return $string;
   }
   
   /**
    * Funkce ořeže řetězec na zadanou délku
    * @param string $text -- řetězec
    * @param int $numb -- počet znaků
    * @param string $etc -- ukončovací znaky
    * @return string -- ořezaný řetězec
    * @author alishahnovin@hotmail.com 2007
    * @link http://php.net/manual/en/function.substr-replace.php
    */
   public static function truncate($text, $numb, $etc = "...") 
   {
      if (strlen($text) > $numb) {
         $text = substr($text, 0, $numb);
         $text = substr($text,0,strrpos($text," "));

         $punctuation = ".!?:;,-"; //punctuation you want removed

         $text = (strspn(strrev($text),  $punctuation)!=0)
                 ?
                 substr($text, 0, -strspn(strrev($text),  $punctuation))
                 :
                 $text;

         $text = $text.$etc;
      }
      return $text;
   }

   /**
    * Funkce rozdělí řetězce na poloviny a jednu vrátí (první polovina je vždy větší)
    * @param string $text -- řetězec
    * @param bool $firstHalf -- jestli se má vrátit první půlka nebo druhá
    */
   public static function splitToHalf($text, $firstHalf = true)
   {
      $text = strip_tags($text);
      $splitstring1 = substr($text, 0, floor(strlen($text) / 2));
      $splitstring2 = substr($text, floor(strlen($text) / 2));

      if (substr($splitstring1, 0, -1) != ' ' AND substr($splitstring2, 0, 1) != ' ') {
         $middle = strlen($splitstring1) + strpos($splitstring2, ' ') + 1;
      } else {
         $middle = strrpos(substr($text, 0, floor(strlen($text) / 2)), ' ') + 1;
      }

      if($firstHalf){
         return substr($text, 0, $middle);
      }
      return substr($text, $middle);
   }
   
   public static function ucFirst($string, $encoding = 'UTF-8')
   {
      $strlen = mb_strlen($string, $encoding);
      $firstChar = mb_substr($string, 0, 1, $encoding);
      $then = mb_substr($string, 1, $strlen - 1, $encoding);
      return mb_strtoupper($firstChar, $encoding) . $then;
   }
}
