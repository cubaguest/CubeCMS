<?php

/**
 * Abstraktní třída hlavního menu.
 * Třída slouží pro vytvoření hlavního menu aplikace z uživatelem definované
 * třídy pro menu, a poskytuje základní přístup k prvkům menu.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: menu_main.class.php 871 2010-01-30 11:33:17Z jakub $ VVE3.9.2 $Revision: 871 $
 * @author			$Author: jakub $ $Date: 2010-01-30 12:33:17 +0100 (Sat, 30 Jan 2010) $
 * 						$LastChangedBy: jakub $ $LastChangedDate: 2010-01-30 12:33:17 +0100 (Sat, 30 Jan 2010) $
 * @abstract 		Třída pro vytvoření hlavního menu
 */
class Menu_Admin {

   const SECTION_STRUCT = "struct";
   const SECTION_ACCOUNT = "account";
   const SECTION_CONTENT = "content";
   const SECTION_EMAIL = "email";
   const SECTION_SETTINGS = "settings";
   const SECTION_INFORMATION = "info";
   const SECTION_USER = "user";
   const SECTION_LISTS = "lists";
   const SECTION_SHOP = "shop";

   const POSITION_LEFT = 'l';
   const POSITION_RIGHT = 'r';
   const POSITION_CENTER = 'c';
   
   private static $instance = false;

   /**
    * Objekt s admin menu
    */
   protected $items = array();

   private function __construct()
   {
      $this->loadItems();
   }

   /**
    * 
    * @return self
    */
   public static function getInstance()
   {
      if ((self::$instance instanceof Menu_Admin) == false) {
         self::$instance = new Menu_Admin();
      }
      return self::$instance;
   }

   protected function loadItems()
   {
      $baseDir = dirname(__FILE__).DIRECTORY_SEPARATOR.'menus'.DIRECTORY_SEPARATOR;
      // load base items
      include_once $baseDir."base.php";
      // load shop items
      if(defined('CUBE_CMS_SHOP') && CUBE_CMS_SHOP){
         include_once $baseDir."shop.php";
      }
      // load custom items
      if(function_exists('extendAdminMenu')){
         extendAdminMenu($this);
      }
   }

   public function addItem($section, Menu_Admin_Item $item)
   {
      if($item->getId() > Menu_Admin_Item::$lastId){
         Menu_Admin_Item::$lastId = $item->getId();
      }
      $this->items[$section]['items'][] = $item;
      Model_CategoryAdm::addRecord($item);
   }
   
   public function addSection($name, $labels, $icon, $position = self::POSITION_LEFT)
   {
      $this->items[$name] = array(
          'labels' => $labels,
          'icon' => $icon,
          'position' => $position,
          'items' => array(),
      );
   }
   
   /**
    * 
    * @return Menu_Admin_Item[]
    */
   public function getItems($position = self::POSITION_LEFT)
   {
      $items = array();
      foreach ($this->items as $key => $section) {
         if( isset($section['position']) && $section['position'] == $position){
            $items[$key] = $section;
         }
      }
      return $items;
   }

}
