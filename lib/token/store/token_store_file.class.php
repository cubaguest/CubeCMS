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

class Token_Store_File implements Token_Store {
   const TOKEN_EXPIRE = 3600; // 1hour

   public function check($token)
   {
      return is_file($this->dir().$token.'_'.Auth::getUserId().'.token');
   }

   public function save($token)
   {
      if(!is_dir($this->dir())){
         @mkdir($this->dir());
      }
      @touch($this->dir().$token.'_'.Auth::getUserId().'.token');
   }

   public function delete($token)
   {
      if(is_file($this->dir().$token.'_'.Auth::getUserId().'.token')){
         @unlink($this->dir().$token.'_'.Auth::getUserId().'.token');
      }
   }

   public function gc()
   {
      if(is_dir($this->dir())){
         foreach (glob($this->dir()."*.token") as $filename) {
            if(filectime($filename)+self::TOKEN_EXPIRE < time()){
               @unlink($filename);
            }
         }
      }
   }
   
   private function dir()
   {
      return AppCore::getAppCacheDir().'tokens'.DIRECTORY_SEPARATOR;
   }
}
?>
