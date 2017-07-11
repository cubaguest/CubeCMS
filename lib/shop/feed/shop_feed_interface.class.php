<?php
interface Shop_Feed_Interface {
   /**
    * @retun string
    */
   public function getFilePath();
   
   /**
    * 
    */
   public function generate();
   
   /**
    * Vrací pole kategorií
    * @param string $q
    */
   public static function getCategories($q = null);
}