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
    * Metoda načte moduly ze kterých se bude vyhledávat
    * @return array -- pole modulů
    */
   public function getModules() {
      $userNameGroup = AppCore::getAuth()->getGroupName();

      $sqlSelect = $this->getDb()->select()->table(Db::table(Model_Module::DB_TABLE_MODULES), 'modules')
      ->colums(Db::COLUMN_ALL)
      ->join(array('items' => Db::table(Model_Module::DB_TABLE_ITEMS)),
         array(Model_Module::COLUMN_ID_MODULE, 'modules' => Model_Module::COLUMN_ID_MODULE),
         null, Db::COLUMN_ALL)
      ->join(array('cat' => Db::table(Model_Category::DB_TABLE)), array(Model_Module::COLUMN_ITEM_CAT_ID,
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
      $userNameGroup = AppCore::getAuth()->getGroupName();

      $sqlSelect = $this->getDb()->select()->table(Db::table(Model_Module::DB_TABLE_ITEMS), 'item')
      ->colums(array(Model_Module::COLUMN_ID_MODULE, self::COLUMN_ID_ITEM))
      ->join(array('cat' => Db::table(Model_Category::DB_TABLE)), array(Model_Module::COLUMN_ITEM_CAT_ID,
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
         $itemArr = array(self::ITEMS_ARRAY_INDEX_CAT_ID => $result[Model_Category::COLUMN_CAT_ID],
            self::ITEMS_ARRAY_INDEX_CAT_NAME => $result[Model_Category::COLUMN_CAT_LABEL]);
         
         $modulesArray[$result[Model_Module::COLUMN_ID_MODULE]][$result[self::COLUMN_ID_ITEM]] = $itemArr;
      }
      return $modulesArray;
   }
}

?>