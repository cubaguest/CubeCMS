<?php

/**
 * Třída pro usnadnění práce se sítí
 *
 * @copyright  	Copyright (c) 2008-2014 Jakub Matas
 * @version    	$Id: $ CubeCMS 8.0.4 $Revision:  $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 */
class Utils_Net {

   /**
    * Vrací reálnou IP adresu uživatele
    * @return string
    */
   public static function getClientIP()
   {
      $ip = $_SERVER['REMOTE_ADDR'];
      if (isset($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
         $ip = $_SERVER['HTTP_CLIENT_IP'];
      } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
         $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      }
      if(strpos($ip, ',') !== false){
         $ips = explode(',', $ip);
         $ip = $ips[0];
      }
      return $ip;
   }

}
