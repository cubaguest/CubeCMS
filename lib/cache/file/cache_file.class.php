<?php
class Cache_File {

   protected $file = null;

   protected $expire = 3600;

   public function __construct($key, $expire = 3600)
   {
      FS_Dir::checkStatic(AppCore::getAppCacheDir().'cachefiles'.DIRECTORY_SEPARATOR);
      $this->file = AppCore::getAppCacheDir().'cachefiles'.DIRECTORY_SEPARATOR.$key.'.cache';
      $this->expire = $expire;
   }

   public function get()
   {
      if(is_file($this->file) && (filemtime($this->file) > (time() - $this->expire ))){
         $cnt = file_get_contents($this->file);
         if($cnt != null){
            $cnt = unserialize($cnt);
         }
         return $cnt;
      }
      return false;
   }
   
   public function set($value)
   {
      return file_put_contents($this->file, serialize($value), LOCK_EX);
   }
   
   public function delete()
   {
      if(is_file($this->file)){
         return unlink($this->file);
      }
      return true;
   }
}
