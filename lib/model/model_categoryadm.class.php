<?php

/**
 * Třída Modelu pro práci s kategoriemi.
 * Třída, která umožňuje pracovet s modelem kategorií
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: model_category.class.php 1989 2011-03-16 18:47:03Z jakub $ VVE3.9.2 $Revision: 1989 $
 * @author			$Author: jakub $ $Date: 2011-03-16 19:47:03 +0100 (St, 16 bře 2011) $
 * 						$LastChangedBy: jakub $ $LastChangedDate: 2011-03-16 19:47:03 +0100 (St, 16 bře 2011) $
 * @abstract 		Třída pro vytvoření modelu pro práci s kategoriemi
 * @todo          nutný refaktoring
 */
class Model_CategoryAdm extends Model {

   /**
    * Objekt s admin menu
    * @var Menu_Admin_Item[]
    */
   private static $items = false;

   public function __construct()
   {
      
   }

   public function getCategory($urlkey)
   {
      return self::findItemByUrl($urlkey);
   }

   public static function getCategoryByID($id)
   {
      return isset(self::$items[$id]) ? isset(self::$items[$id]) : false;
   }

   public static function getCategoryByModule($module)
   {
      
   }

   /**
    * 
    * @param Menu_Admin_Item $url
    */
   public static function findItemByUrl($url)
   {
      if(self::$items === false){
         Menu_Admin::getInstance();
      }
      foreach (self::$items as $item) {
         if(strpos($url, $item->{Model_Category::COLUMN_URLKEY}) !== false){
            return $item;
         }
      }
      return false;
   }
   
   public static function addRecord(Menu_Admin_Item $item)
   {
      $obj = new Object();
      $obj->{Model_Category::COLUMN_URLKEY} = $item->getUrlKey();
      $obj->{Model_Category::COLUMN_NAME} = $item->getName();
      $obj->{Model_Category::COLUMN_MODULE} = $item->getModule();
      $obj->{Model_Category::COLUMN_PARAMS} = $item->getParams();
      $obj->{Model_Category::COLUMN_ID} = $item->getId();
      $obj->{Model_Category::COLUMN_DATADIR} = $item->getDataDir();
      self::$items[$item->getId()] = $obj;
   }

   /**
    * Metoda načte všechny kategorie
    * @return array of Model_ORM_Records -- pole s objekty
    */
   public function getCategoryList()
   {
      return self::$items;
   }

   /**
    * Metoda načte všechny kategorie i se strukturou
    * @return array of Model_ORM_Records -- pole s objekty
    */
   public function getStructure()
   {
      return self::$items;
   }

   public static function getCategoryListByModule($module, $onlyWithRights = true)
   {
      
   }

}
