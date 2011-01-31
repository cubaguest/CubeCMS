<?php
/**
 * Třída token_store_session pro ukládání tokenů do session
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id:  $ VVE 7.1 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída 
 */

class Token_Store_Session implements Token_Store {
   const TOKENS_SES_NAME = 'tokens';
   const TOKEN_EXPIRE = 3600; // 1hour

      public static function check($token)
   {
      return isset ($_SESSION[self::TOKENS_SES_NAME][$token]);
   }

   public static function save($token)
   {
      if(!isset ($_SESSION[self::TOKENS_SES_NAME])){
         $_SESSION[self::TOKENS_SES_NAME] = array();
      }
      $_SESSION[self::TOKENS_SES_NAME][$token] = time()+self::TOKEN_EXPIRE; // platnost 1h
   }

   public static function delete($token)
   {
      if(isset ($_SESSION[self::TOKENS_SES_NAME][$token])){
         unset ($_SESSION[self::TOKENS_SES_NAME][$token]);
      }
   }

   public static function gc()
   {
      if(isset ($_SESSION[self::TOKENS_SES_NAME])){
         foreach ($_SESSION[self::TOKENS_SES_NAME] as $token => $exp) {
            if($exp < time()){
               unset ($_SESSION[self::TOKENS_SES_NAME][$token]);
            }
         }
      }
   }
}
?>
