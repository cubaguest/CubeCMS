<?php
class Cache {
   /**
    * Objekt pro kešování
    * @var Cache_Provider_Interface
    */
   private $cacher = false;
   
   function __construct() {
      if(defined('VVE_MEMCACHE_SERVER') && VVE_MEMCACHE_SERVER != null){
         $this->cacher = new Cache_Provider_MemCache();
      } else {
         $this->cacher = new Cache_Provider_NoCache();
      }
   }
   
   public function get($key) 
   {
      return $this->cacher->get($key);
   }
   
   public function set($key, $value, $expire = 3600, $compress = true) 
   {
      $this->cacher->set($key, $value, $expire, $compress);
   }
   
   public function delete($key) 
   {
      $this->cacher->delete($key);
   }
   
}


?>