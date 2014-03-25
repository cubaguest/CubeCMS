<?php
Class Cache_Provider_Files implements Cache_Provider_Interface {
   protected static $disable = false;

   protected static $cacheDir = null;

   public function __construct()
   {
      if(!self::$disable && self::$cacheDir == null){
         self::$disable = true;
         self::$cacheDir = AppCore::getAppCacheDir().'datacache';
//         $dir = new FS_Dir(self::$cacheDir);
         FS_Dir::checkStatic(self::$cacheDir);

      }
   }
   
   public function get($key) {
      if(!self::$disable){

      }
      return false;
   }
   
   public function delete($key) {
      if(!self::$disable){

      }
      return true;
   }
   
   public function set($key, $value, $expire = 3600, $compress = true) {
      $ret = true;
      if(!self::$disable){

      }
      return $ret;
   }
   
   public function replace($key, $value, $expire = 36000, $compress = true) {
      if(!self::$disable){

      }
      return true;
   }
   
   public function flush() {
      if(!self::$disable){

      }
      return true;
   }

   public static function isEnabled()
   {
      return !self::$disable;
   }
}