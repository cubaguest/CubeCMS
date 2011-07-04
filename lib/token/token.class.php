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
   private static $tokenStore = false;

   private static $token = false;

   public static function getToken()
   {
      $classStoreName = 'Token_Store_'.ucfirst(VVE_TOKENS_STORE);
      if(!self::$token){
         self::$token = self::generateToken();
      }
      if(self::$tokenStore instanceof Token_Store === false){
         self::$tokenStore = new $classStoreName();
      }
      self::$tokenStore->save(self::$token);
      if(rand(1, 10) == 3){
         self::$tokenStore->gc();
      }
      return self::$token;
   }

   /**
    * Metoda kontroluje existenci tokenu
    * @param string $token -- řetězec tokenu
    * @param bool $delete (option) jestli se má token odstranit (pro další kontroly)
    */
   public static function check($token, $delete = true)
   {
      $classStoreName = 'Token_Store_'.ucfirst(VVE_TOKENS_STORE);
      if(!self::$tokenStore){
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
//      return 'de94wACtzqbpFybCPDTgOnXDpMqxPnyas'; // testing
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
