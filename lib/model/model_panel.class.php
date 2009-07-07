<?php
/**
 * Třída Modelu pro práci s panely modulů.
 * Třída, která umožňuje pracovet s modely panelů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.2 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro vytvoření modelu pro práci s panely
 */

class Model_Panel extends Model_Db {

   /**
    * Konstanty s názzvy sloupců
    */
   const COLUMN_POSITION = 'position';
   const COLUMN_ENABLE = 'enable';
   const COLUMN_PRIORITY = 'priority';
	const COLUMN_ID_ITEM    = 'id_item';

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
    * Proměná s názvem tabulky s panely
    * @var string
    */
   private $panelsTable = null;

   /**
    * Metoda načte panely z db
    * @param string -- pozice panelu
    * @return array -- pole s panely
    */
   public function getPanel($side) {
      $this->getTables();

      $sqlSelect = $this->getDb()->select()->table($this->panelsTable, 'panel')
      ->join(array("item" => $this->itemsTable), array(self::COLUMN_ID_ITEM, 'panel' => Model_Module::COLUMN_ITEM_ID),
            null, array(Model_Module::COLUMN_ITEM_LABEL => "IFNULL(item.".Model_Module::COLUMN_ITEM_LABEL.'_'.Locale::getLang()
            .", item.".Model_Module::COLUMN_ITEM_LABEL.'_'.Locale::getDefaultLang().")",
             Model_Module::COLUMN_ITEM_ALT => "IFNULL(item.".Model_Module::COLUMN_ITEM_ALT.'_'.Locale::getLang()
             .", item.".Model_Module::COLUMN_ITEM_ALT.'_'.Locale::getDefaultLang().")", Db::COLUMN_ALL))
      ->join(array("cat" => $this->catTable),
          array('item' =>Model_Category::COLUMN_CAT_ID,Model_Category::COLUMN_CAT_ID),
          null, array(Model_Category::COLUMN_CAT_LABEL => "IFNULL(cat.".Model_Category::COLUMN_CAT_LABEL_ORIG.'_'
            .Locale::getLang().", cat.".Model_Category::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getDefaultLang().")",
            Model_Category::COLUMN_CAT_ALT => "IFNULL(cat.".Model_Category::COLUMN_CAT_ALT_ORIG.'_'
            .Locale::getLang().", cat.".Model_Category::COLUMN_CAT_ALT_ORIG.'_'.Locale::getDefaultLang().")",
            Model_Category::COLUMN_CAT_ID))
      ->join(array("module" => $this->modulesTable), array('item'=>Model_Module::COLUMN_ID_MODULE
            ,Model_Module::COLUMN_ID_MODULE), null, Db::COLUMN_ALL)
      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.AppCore::getAuth()->getGroupName(), "r__", Db::OPERATOR_LIKE)
      ->where("panel.".self::COLUMN_POSITION , strtolower($side))
      ->where("panel.".self::COLUMN_ENABLE, (int)true)
      ->order("panel.".self::COLUMN_PRIORITY, Db::ORDER_DESC)
      ->order(Model_Category::COLUMN_CAT_LABEL);

      return $this->getDb()->fetchObjectArray($sqlSelect);
   }

   /**
    * Metoda načte tabulky
    */
   private function getTables() {
      $this->panelsTable = AppCore::sysConfig()->getOptionValue("panels_table", "db_tables");
      $this->modulesTable = AppCore::sysConfig()->getOptionValue("modules_table", "db_tables");
      $this->itemsTable = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
      $this->catTable = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
   }
}

?>