<?php
/**
 * Třída Modelu pro práci se sitemap
 * Třída, která umožňuje pracovet s modelem sitemap
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.2 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro vytvoření modelu pro práci s sitemap
 */

class Model_Search extends Model_Db {
   const ITEMS_ARRAY_INDEX_CAT_ID   = 'id_category';
   const ITEMS_ARRAY_INDEX_CAT_NAME = 'name';

   const COLUMN_ID_ITEM = 'id_item';

   /**
    * Proměná s názvem tabulky s kategoriemi
    * @var string
    */
   private $catTable = null;

   /**
    * Proměná s názvem tabulky se sekcemi
    * @var string
    */
   private $modulesTable = null;

   /**
    * Proměná s názvem tabulky s itemi
    * @var string
    */
   private $itemsTable = null;

   /**
    * Metoda načte moduly ze kterých se bude vyhledávat
    * @return array -- pole modulů
    */
   public function getModules() {
      $this->getTables();
      $userNameGroup = AppCore::getAuth()->getGroupName();

      $sqlSelect = $this->getDb()->select()->table($this->modulesTable, 'modules')
      ->colums(Db::COLUMN_ALL)
      ->join(array('items' => $this->itemsTable),
         array(Model_Module::COLUMN_ID_MODULE, 'modules' => Model_Module::COLUMN_ID_MODULE),
         null, Db::COLUMN_ALL)
      ->join(array('cat' => $this->catTable), array(Model_Module::COLUMN_ITEM_CAT_ID,
         'items' => Model_Module::COLUMN_ITEM_CAT_ID), null,
            array(Model_Category::COLUMN_CAT_LABEL => "IFNULL(cat.".Model_Category::COLUMN_CAT_LABEL_ORIG
               .'_'.Locale::getLang().", cat.".Model_Category::COLUMN_CAT_LABEL_ORIG
               .'_'.Locale::getDefaultLang().")",
               Model_Category::COLUMN_CAT_ALT => "IFNULL(cat.".Model_Category::COLUMN_CAT_ALT_ORIG
               .'_'.Locale::getLang().", cat.".Model_Category::COLUMN_CAT_ALT_ORIG
               .'_'.Locale::getDefaultLang().")", Model_Category::COLUMN_CAT_ID))
      ->where("items.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup, "r__", Db::OPERATOR_LIKE)
      ->group('modules.'.Model_Module::COLUMN_ID_MODULE);
      return $this->getDb()->fetchObjectArray($sqlSelect);
   }

   /**
    * Metoda načte všechny items a zařadí je do modulů s kategorií
    */
   public function getItems() {
      $this->getTables();
      $userNameGroup = AppCore::getAuth()->getGroupName();

      $sqlSelect = $this->getDb()->select()->table($this->itemsTable, 'item')
      ->colums(array(Model_Module::COLUMN_ID_MODULE, self::COLUMN_ID_ITEM))
      ->join(array('cat' => $this->catTable), array(Model_Module::COLUMN_ITEM_CAT_ID,
         'item' => Model_Module::COLUMN_ITEM_CAT_ID), null,
            array(Model_Category::COLUMN_CAT_LABEL => "IFNULL(cat.".Model_Category::COLUMN_CAT_LABEL_ORIG
               .'_'.Locale::getLang().", cat.".Model_Category::COLUMN_CAT_LABEL_ORIG
               .'_'.Locale::getDefaultLang().")",
               Model_Category::COLUMN_CAT_ALT => "IFNULL(cat.".Model_Category::COLUMN_CAT_ALT_ORIG
               .'_'.Locale::getLang().", cat.".Model_Category::COLUMN_CAT_ALT_ORIG
               .'_'.Locale::getDefaultLang().")", Model_Category::COLUMN_CAT_ID))
      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup, "r__", Db::OPERATOR_LIKE);

      $modulesArray = array();
      while($result = $this->getDb()->fetchAssoc($sqlSelect)){
         if(!isset ($modulesArray[$result[Model_Module::COLUMN_ID_MODULE]])){
            $modulesArray[$result[Model_Module::COLUMN_ID_MODULE]] = array();
         }
         $itemArr = array(self::ITEMS_ARRAY_INDEX_CAT_ID => $result[self::COLUMN_ID_ITEM],
            self::ITEMS_ARRAY_INDEX_CAT_NAME => $result[Model_Category::COLUMN_CAT_LABEL]);
         
         $modulesArray[$result[Model_Module::COLUMN_ID_MODULE]][$result[self::COLUMN_ID_ITEM]] = $itemArr;
      }
      return $modulesArray;
   }

   /**
    * Metoda načte tabulky
    */
   private function getTables() {
      $this->catTable = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
		$this->modulesTable = AppCore::sysConfig()->getOptionValue("modules_table", "db_tables");
		$this->itemsTable = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
   }
}

?>