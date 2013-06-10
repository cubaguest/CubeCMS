<?php
Class Cache_Provider_NoCache implements Cache_Provider_Interface {
   public function get($key) {
      return false;
   }
   
   public function delete($key) {
      return true;
   }
   
   public function set($key, $value, $expire = 36000, $compress = true) {
      return true;
   }
   
   public function replace($key, $value, $expire = 36000, $compress = true) {
      return true;
   }
   
   public function flush() {
      return true;
   }

   public static function isEnabled()
   {
      return false;
   }
}