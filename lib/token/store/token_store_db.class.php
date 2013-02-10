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

class Token_Store_Db implements Token_Store {
   const TOKEN_EXPIRE = 3600; // 1hour

   public function check($token)
   {
      return (bool)(new Model_Tokens())->where(
         Model_Tokens::COLUMN_TOKEN." = :token "
            ." AND ".Model_Tokens::COLUMN_ID_USER." = :idu"
            ." AND ".Model_Tokens::COLUMN_TIME_ADD." > DATE_ADD(NOW(), INTERVAL :secs SECOND )",
            array('token' => $token, 'idu' => Auth::getUserId(), 'secs' => -(self::TOKEN_EXPIRE) ))
         ->count();
   }

   public function save($token)
   {
      $tokenRec = (new Model_Tokens())->newRecord();
      $tokenRec->{Model_Tokens::COLUMN_ID_USER} = Auth::getUserId();
      $tokenRec->{Model_Tokens::COLUMN_TOKEN} = $token;
      $tokenRec->save();
   }

   public function delete($token)
   {
      (new Model_Tokens())
         ->where(Model_Tokens::COLUMN_TOKEN." = :token AND ".Model_Tokens::COLUMN_ID_USER." = :idu",
            array('token' => $token, 'idu' => Auth::getUserId()))
         ->delete();
   }

   public function gc()
   {
      (new Model_Tokens())
         ->where(Model_Tokens::COLUMN_TIME_ADD." < DATE_ADD(NOW(), INTERVAL :secs SECOND )",
         array('secs' => -(self::TOKEN_EXPIRE)))
         ->delete();
   }
}
