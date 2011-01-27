<?php
/**
 * Abstraktní třída hlavního menu.
 * Třída slouží pro vytvoření hlavního menu aplikace z uživatelem definované
 * třídy pro menu, a poskytuje základní přístup k prvkům menu.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: menu_main.class.php 871 2010-01-30 11:33:17Z jakub $ VVE3.9.2 $Revision: 871 $
 * @author			$Author: jakub $ $Date: 2010-01-30 12:33:17 +0100 (Sat, 30 Jan 2010) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2010-01-30 12:33:17 +0100 (Sat, 30 Jan 2010) $
 * @abstract 		Třída pro vytvoření hlavního menu
 */

class Menu_Admin extends Menu_Main {
   /**
    * Objekt s admin menu
    * @var Category_Structure
    */
   public static $menu = null;

   /**
    * Metoda provede inicializaci menu
    */
   public static function factory() {
//      $newAdminMenu = new Category_Structure(0);
//      $newAdminMenu->addChild(new Category_Structure(1));
//      $newAdminMenu->saveStructure(true);
//      var_dump($newAdminMenu);

      // načtení menu z
      if(defined('VVE_ADMIN_MENU_STRUCTURE')){
         self::$menu = unserialize(VVE_ADMIN_MENU_STRUCTURE);
         $catModel = new Model_Category();
         self::$menu->setCategories($catModel->getCategoryList());
      }
   }
   public function view() {
      $this->template()->menu = self::$menu;
      $this->template()->addTplFile("menu_admin.phtml", true);
      $this->template()->addJsPlugin(new JsPlugin_JQuery());
   }
}
?>