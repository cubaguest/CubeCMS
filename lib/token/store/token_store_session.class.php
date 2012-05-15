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
   const MAX_TOKEN_IN_STACK = 40;

   public function check($token)
   {
      if(isset($_SESSION[self::TOKENS_SES_NAME][$token]) && $_SESSION[self::TOKENS_SES_NAME][$token] <= time()){
         return true;
      }
      return isset ($_SESSION[self::TOKENS_SES_NAME][$token]);
   }

   public function save($token)
   {
      if(!isset ($_SESSION[self::TOKENS_SES_NAME])){
         $_SESSION[self::TOKENS_SES_NAME] = array();
      }
      $s = &$_SESSION[self::TOKENS_SES_NAME];
      $s[$token] = time()+self::TOKEN_EXPIRE; // platnost 1h
      if(count($s) > self::MAX_TOKEN_IN_STACK){
         $s = array_slice($s, -self::MAX_TOKEN_IN_STACK+10);
      }
   }

   public function delete($token)
   {
      if(isset ($_SESSION[self::TOKENS_SES_NAME][$token])){
         unset ($_SESSION[self::TOKENS_SES_NAME][$token]);
      }
   }

   public function gc()
   {
      if(isset ($_SESSION[self::TOKENS_SES_NAME])){
         $s = &$_SESSION[self::TOKENS_SES_NAME];
         reset($s);
         foreach ($s as $token => $exp) {
            if($exp < time()){
               unset ($s[$token]);
            } else {
               break;
            }
         }
      }
   }
}
?>
