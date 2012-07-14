<?php
Class Cache_Provider_MemCache implements Cache_Provider_Interface {
   protected static $memcache = false;
   protected static $disable = false;
   
   public function __construct()
   {
      if(!self::$disable && !self::$memcache){
         self::$memcache = new Memcache();
         if(!self::$memcache->connect(VVE_MEMCACHE_SERVER, VVE_MEMCACHE_PORT) ) {
            self::$disable = true;
         }
      }
   }
   
   public function get($key) {
      if(!self::$disable){
         return self::$memcache->get($key);
      }
      return false;
   }
   
   public function delete($key) {
      if(!self::$disable){
         return self::$memcache->delete($key);
      }
      return true;
   }
   
   public function set($key, $value, $expire = 36000, $compress = true) {
      if(!self::$disable){
         return self::$memcache->set($key, $value, $compress == true ? MEMCACHE_COMPRESSED : 0 ,$expire);
      }
      return true;
   }
   
   public function replace($key, $value, $expire = 36000, $compress = true) {
      if(!self::$disable){
         return self::$memcache->replace($key, $value, $compress == true ? MEMCACHE_COMPRESSED : 0 ,$expire);
      }
      return true;
   }
   
   public function flush() {
      if(!self::$disable){
         return self::$memcache->flush();
      }
      return true;
   }
}