<?php
Class Cache_Provider_MemCache implements Cache_Provider_Interface {
   protected static $memcache = false;
   protected static $disable = false;
   
   public function __construct()
   {
      if(!self::$disable && !self::$memcache){
         self::$memcache = new Memcache();
         $server = VVE_MEMCACHE_SERVER == 'localhost' ? '127.0.0.1' : VVE_MEMCACHE_SERVER;
         self::$memcache->addServer($server, VVE_MEMCACHE_PORT);
         $stats = @self::$memcache->getExtendedStats();
         if (!(bool)$stats[$server.":".VVE_MEMCACHE_PORT] || !@self::$memcache->connect($server, VVE_MEMCACHE_PORT)){
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
      $ret = true;
      if(!self::$disable){
         $ret = self::$memcache->replace($key, $value, 0, $expire);
         if(!$ret){
            $ret = self::$memcache->set($key, $value, 0, $expire);
         }

      }
      return $ret;
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

   public static function isEnabled()
   {
      return !self::$disable;
   }
}