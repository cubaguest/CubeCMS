<?php

/**
 * Třída pro usnadnění práce s čísly
 *
 * @copyright  	Copyright (c) 2008-2014 Jakub Matas
 * @version    	$Id: $ CubeCMS 8.0.4 $Revision:  $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 */
class Utils_Number {

   /**
    * Funkce sformátuje float bez ohledu na locales a odebere nuly z konce
    * @param string $string -- číslo na formátování
    * @return string -- převedený řetězec
    */
   public static function formatFloat($number)
   {
      $number = rtrim($number, "0");
      $locale_info = localeconv();
      return rtrim(rtrim($number, $locale_info['decimal_point']), '.');
   }
}
