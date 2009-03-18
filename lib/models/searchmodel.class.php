<?php
/**
 * Třída Modelu pro práci se sitemap
 * Třída, která umožňuje pracovet s modelem sitemap
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.2 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vytvoření modelu pro práci s sitemap
 */

class SearchModel extends DbModel {

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
//
//      $sqlSelect = $this->getDb()->select()->table($this->catTable, 'cat')
//      ->colums(array("label" => "IFNULL(cat.".CategoryModel::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getLang()
//            .", cat.".CategoryModel::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getDefaultLang().")",
//            "alt" => "IFNULL(item.".CategoryModel::COLUMN_CAT_ALT_ORIG.'_'.Locale::getLang()
//            .", item.".CategoryModel::COLUMN_CAT_ALT_ORIG.'_'.Locale::getDefaultLang().")", Db::COLUMN_ALL))
//      ->join(array("item" => $this->itemsTable), array(CategoryModel::COLUMN_CAT_ID, 'cat'=>CategoryModel::COLUMN_CAT_ID), null, Db::COLUMN_ALL)
//      ->join(array("module" => $this->modulesTable), array(ModuleModel::COLUMN_ID_MODULE,
//          'item'=>ModuleModel::COLUMN_ID_MODULE), Db::JOIN_LEFT, Db::COLUMN_ALL)
//      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup, "r__", Db::OPERATOR_LIKE)
//      ->group('item.'.CategoryModel::COLUMN_CAT_ID)
//      ->order("cat.".CategoryModel::COLUMN_CAT_PRIORITY, Db::ORDER_DESC)
//      ->order("item.".ModuleModel::COLUMN_PRIORITY, Db::ORDER_DESC);
//      return $this->getDb()->fetchObjectArray($sqlSelect);
   }

   private function getTables() {
      $this->catTable = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
		$this->modulesTable = AppCore::sysConfig()->getOptionValue("modules_table", "db_tables");
		$this->itemsTable = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
   }
}

?>