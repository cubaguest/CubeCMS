<?php
/**
 * Třída token pro práci s tokeny, (ochrana před útoky)
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id:  $ VVE 7.1 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída 
 */

class Token {
   /**
    * Objekt s uloženými tokeny
    * @var Token_Store
    */
   private static $tokenStore = null;


   public static function getToken()
   {
      $classStoreName = 'Token_Store_'.ucfirst(VVE_TOKENS_STORE);
      if(self::$tokenStore == null){
         self::$tokenStore = new $classStoreName();
      }
      
      $token = self::generateToken();
      // náhodně provedem gc
      if(rand(1, 10) == 1){
         self::$tokenStore->gc();
      }
      self::$tokenStore->save($token);
      return $token;
   }

   /**
    * Metoda kontroluje existenci tokenu
    * @param string $token -- řetězec tokenu
    * @param bool $delete (option) jestli se má token odstranit (pro další kontroly)
    */
   public static function check($token, $delete = true)
   {
      $classStoreName = 'Token_Store_'.ucfirst(VVE_TOKENS_STORE);
      if(self::$tokenStore == null){
         self::$tokenStore = new $classStoreName();
      }
      
      $ok = self::$tokenStore->check($token);
      if($delete){
         self::$tokenStore->delete($token);
      }
      return $ok;
   }

   /**
    * Metoda vageneruje token
    * @return string
    */
   private static function generateToken()
   {
//      return 'de94wACtzqbpFybCPDTgOnXDpMqxPnyas'; // test
      $len = 32;
      $base='ABCDEFGHKLMNOPQRSTWXYZabcdefghjkmnpqrstwxyz123456789';
      $max=strlen($base)-1;
      $activatecode='';
      mt_srand((double)microtime()*1000000);
      $token = null;
      while (strlen($token)<$len+1){
         $token.=$base[mt_rand(0,$max)];
      }
      return $token;
   }
}
?>
