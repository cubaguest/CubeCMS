<?php

abstract class Shop_Feed implements Shop_Feed_Interface {
   /**
    * @retun string
    */
   public function getFilePath()
   {
      return '';
   }
   
   public function generate()  {
      
   }
   
   /**
    * Vrací pole kategorií
    * @param string $q
    * @return array pole kategorií
    */
   public static function getCategories($q = null) {
      return array();
   }

}
