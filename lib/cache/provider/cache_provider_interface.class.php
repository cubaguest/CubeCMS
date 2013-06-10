<?php
interface Cache_Provider_Interface {
   public function get($key);
   public function delete($key);
   public function set($key, $value, $expire = 36000, $compress = true);
   public function replace($key, $value, $expire = 36000, $compress = true);
   public function flush();
   public static function isEnabled();
}