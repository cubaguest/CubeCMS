<?php

/**
 * Třída pro usnadnění práce s html řetězci
 *
 * @copyright  	Copyright (c) 2008-2014 Jakub Matas
 * @version    	$Id: $ CubeCMS 8.0.4 $Revision:  $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 */
class Utils_Html {

   /**
    * Funkce odstraní html tagy z řetězce a pole (rekurzivní)
    * @param mixed $value -- samotný řetězec nebo pole
    * @return mixed -- řětezce nebo pole bez html tagů
    */
   public static function stripTags($value, $allowedtags = null)
   {
      if (is_array($value)) {
         foreach ($value as $key => $val) {
            $value[$key] = vve_strip_tags($val, $allowedtags);
         }
      } else {
         $value = strip_tags($value, $allowedtags);
      }
      return $value;
   }

   /**
    * Odstraní html komentáře z kódu
    * @param type $str
    * @return type
    */
   public static function stripHtmlComment($str)
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

}
