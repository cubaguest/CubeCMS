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
   public static function toAscii($string)
   {
      if (defined('VVE_USE_ICONV') && VVE_USE_ICONV == false) {
         $string = strtr($string, array("\xc3\xa1" => "a", "\xc3\xa4" => "a", "\xc4\x8d" => "c", "\xc4\x8f" => "d", "\xc3\xa9" => "e", "\xc4\x9b" => "e", "\xc3\xad" => "i", "\xc4\xbe" => "l", "\xc4\xba" => "l", "\xc5\x88" => "n", "\xc3\xb3" => "o", "\xc3\xb6" => "o", "\xc5\x91" => "o", "\xc3\xb4" => "o", "\xc5\x99" => "r", "\xc5\x95" => "r", "\xc5\xa1" => "s", "\xc5\xa5" => "t", "\xc3\xba" => "u", "\xc5\xaf" => "u", "\xc3\xbc" => "u", "\xc5\xb1" => "u", "\xc3\xbd" => "y", "\xc5\xbe" => "z", "\xc3\x81" => "A", "\xc3\x84" => "A", "\xc4\x8c" => "C", "\xc4\x8e" => "D", "\xc3\x89" => "E", "\xc4\x9a" => "E", "\xc3\x8d" => "I", "\xc4\xbd" => "L", "\xc4\xb9" => "L", "\xc5\x87" => "N", "\xc3\x93" => "O", "\xc3\x96" => "O", "\xc5\x90" => "O", "\xc3\x94" => "O", "\xc5\x98" => "R", "\xc5\x94" => "R", "\xc5\xa0" => "S", "\xc5\xa4" => "T", "\xc3\x9a" => "U", "\xc5\xae" => "U", "\xc3\x9c" => "U", "\xc5\xb0" => "U", "\xc3\x9d" => "Y", "\xc5\xbd" => "Z"));
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

}
