<?php

/**
 * Třída pro usnadnění práce s adresáři
 *
 * @copyright  	Copyright (c) 2008-2014 Jakub Matas
 * @version    	$Id: $ CubeCMS 8.0.4 $Revision:  $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 */
class Utils_Dir {

   
   public static function secureUpload($path, $checkWritableDirs = true)
   {
      $path = str_replace(array(
          '..', // remove ..
      ), array(
          null,
      ), $path);
      if($checkWritableDirs){
         if(strpos($path, AppCore::getAppCacheDir()) === false 
                 && strpos($path, AppCore::getAppDataDir()) === false
                 && strpos($path, CUBE_CMS_DATA_DIR) !== 0
                 ){
            $tr = new Translator();
            throw new Exception(sprintf($tr->tr('Předaná cesta "%s" není v adresáři pro uložení'), $path));
         }
      }
      return $path;
   }
   
}
