<?php
class Cache {
   /**
    * Objekt pro kešování
    * @var Cache_Provider_Interface
    */
   private static $provider = null;
   
   protected static function checkProvider() {
      if(self::$provider === null){
         if(defined('VVE_MEMCACHE_SERVER') && VVE_MEMCACHE_SERVER != null){
            self::$provider = new Cache_Provider_MemCache();
         }

         if(!self::$provider || !self::$provider->isEnabled()){
            self::$provider = new Cache_Provider_NoCache();
         }
      }
   }
   
   public static function get($key)
   {
      self::checkProvider();
      return self::getProvider()->get($_SERVER['SERVER_NAME'].'_'.$key);
   }
   
   public static function set($key, $value, $expire = 3600, $compress = true)
   {
      self::checkProvider();
      self::getProvider()->set($_SERVER['SERVER_NAME'].'_'.$key, $value, $expire, $compress);
   }
   
   public static function delete($key)
   {
      self::checkProvider();
      return self::getProvider()->delete($_SERVER['SERVER_NAME'].'_'.$key);
   }

   public static function isEnabled()
   {
      self::checkProvider();
      return self::getProvider()->isEnabled();
   }

   /**
    * Vrací provider pro kešování
    * @return bool|Cache_Provider_Interface
    */
   public static function getProvider()
   {
      self::checkProvider();
      return self::$provider;
   }
}
