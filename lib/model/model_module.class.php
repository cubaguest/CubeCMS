<?php
/**
 * Třída Modelu pro práci s moduly.
 * Třída, která umožňuje pracovet s modelem modulů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.2 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro vytvoření modelu pro práci s moduly
 */

class Model_Module extends Model_Db {

	 /**
	  * Názvy sloupců v db tabulce
	  * @var string
	  */
	const COLUMN_LABEL      = 'label';
	const COLUMN_ALT        = 'alt';
	const COLUMN_ID_MODULE  = 'id_module';
	const COLUMN_PRIORITY   = 'priority';
	const COLUMN_NAME       = 'name';
	const COLUMN_DATADIR    = 'datadir';

	const COLUMN_ITEM_ID    = 'id_item';
	const COLUMN_ITEM_PARAMS= 'params';
	const COLUMN_ITEM_LABEL = 'label';
	const COLUMN_ITEM_ALT   = 'alt';
	const COLUMN_ITEM_CAT_ID= 'id_category';

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
    * Název skupiny uživatele
    * @var atring 
    */
   private $userNameGroup = null;

   protected function init() {
      $this->getTables();
      $this->userNameGroup = AppCore::getAuth()->getGroupName();
   }

   /**
    * Metoda načte moduly z db
    * @return array -- pole s moduly
    */
   public function getModules() {
      $sqlSelect = $this->getDb()->select()->table($this->itemsTable, 'item')
      ->colums(array(self::COLUMN_ITEM_LABEL => "IFNULL(item.".self::COLUMN_ITEM_LABEL
            .'_'.Locale::getLang().", item.".self::COLUMN_ITEM_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_ITEM_ALT => "IFNULL(item.".self::COLUMN_ITEM_ALT.'_'.Locale::getLang().", item."
            .self::COLUMN_ITEM_ALT.'_'.Locale::getDefaultLang().")", Db::COLUMN_ALL))
      ->join(array("module" => $this->modulesTable), 
         array("item" => self::COLUMN_ID_MODULE, self::COLUMN_ID_MODULE),null,Db::COLUMN_ALL)
      ->join(array("cat" => $this->catTable),
         array("item" => self::COLUMN_ITEM_CAT_ID, Model_Category::COLUMN_CAT_ID),null,
         array(Model_Category::COLUMN_CAT_LABEL => "IFNULL(cat.".Model_Category::COLUMN_CAT_LABEL_ORIG
            .'_'.Locale::getLang().", cat.".Model_Category::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getDefaultLang().")",
            Model_Category::COLUMN_CAT_ALT => "IFNULL(cat.".Model_Category::COLUMN_CAT_ALT_ORIG.'_'.Locale::getLang().", cat."
            .Model_Category::COLUMN_CAT_ALT_ORIG.'_'.Locale::getDefaultLang().")"))
      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$this->userNameGroup,"r__", Db::OPERATOR_LIKE)
      ->where("item.".Model_Category::COLUMN_CAT_ID, Category::getId())
      ->order("item.".self::COLUMN_PRIORITY, Db::ORDER_DESC)
      ->order(self::COLUMN_LABEL);
      return $this->getDb()->fetchObjectArray($sqlSelect);
   }

   /**
    * Metoda načte tabulky
    */
   private function getTables() {
      $this->catTable = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
		$this->modulesTable = AppCore::sysConfig()->getOptionValue("modules_table", Config::SECTION_DB_TABLES);
		$this->itemsTable = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
   }

   /**
    * Metoda načte požadovaný item i s modulem a kategoriílocalhost
    * @param integer $idItem  -- id item
    */
   public function getModule($idItem) {
      $sqlSelect = $this->getDb()->select()->table($this->itemsTable, 'item')
      ->colums(array(self::COLUMN_ITEM_LABEL => "IFNULL(item.".self::COLUMN_ITEM_LABEL
            .'_'.Locale::getLang().", item.".self::COLUMN_ITEM_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_ITEM_ALT => "IFNULL(item.".self::COLUMN_ITEM_ALT.'_'.Locale::getLang().", item."
            .self::COLUMN_ITEM_ALT.'_'.Locale::getDefaultLang().")", Db::COLUMN_ALL))
      ->join(array("module" => $this->modulesTable),
         array("item" => self::COLUMN_ID_MODULE, self::COLUMN_ID_MODULE),null,Db::COLUMN_ALL)
      ->join(array("cat" => $this->catTable),
         array("item" => self::COLUMN_ITEM_CAT_ID, Model_Category::COLUMN_CAT_ID),null,
         array(Model_Category::COLUMN_CAT_LABEL => "IFNULL(cat.".Model_Category::COLUMN_CAT_LABEL_ORIG
            .'_'.Locale::getLang().", cat.".Model_Category::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getDefaultLang().")",
            Model_Category::COLUMN_CAT_ALT => "IFNULL(cat.".Model_Category::COLUMN_CAT_ALT_ORIG.'_'.Locale::getLang().", cat."
            .Model_Category::COLUMN_CAT_ALT_ORIG.'_'.Locale::getDefaultLang().")"))
      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$this->userNameGroup,"r__", Db::OPERATOR_LIKE)
      ->where("item.".Model_Module::COLUMN_ITEM_ID, $idItem);
      return $this->getDb()->fetchObject($sqlSelect);
   }
}
?>