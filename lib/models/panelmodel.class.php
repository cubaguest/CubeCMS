<?php
/**
 * Třída Modelu pro práci s panely modulů.
 * Třída, která umožňuje pracovet s modely panelů
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.2 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vytvoření modelu pro práci s panely
 */

class PanelModel extends DbModel {

   /**
    * Konstanty s názzvy sloupců
    */
   const COLUMN_LABEL = 'label';
   const COLUMN_ALT = 'alt';
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
//      ->colums(array(self::COLUMN_LABEL))
      ->join(array("item" => $this->itemsTable), "item.".self::COLUMN_ID_ITEM."=panel.".self::COLUMN_ID_ITEM,null,
         array(self::COLUMN_LABEL => "IFNULL(item.".self::COLUMN_LABEL.'_'.Locale::getLang()
            .", item.".self::COLUMN_LABEL.'_'.Locale::getDefaultLang().")",
             self::COLUMN_ALT => "IFNULL(item.".self::COLUMN_ALT.'_'.Locale::getLang()
             .", item.".self::COLUMN_ALT.'_'.Locale::getDefaultLang().")", Db::SQL_ALL))
       ->join(array("cat" => $this->catTable), "cat.".CategoryModel::COLUMN_CAT_ID."=item.".CategoryModel::COLUMN_CAT_ID,
          null, array(Category::COLUMN_CAT_LABEL => "IFNULL(cat.".CategoryModel::COLUMN_CAT_LABEL_ORIG.'_'
            .Locale::getLang().", cat.".CategoryModel::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getDefaultLang().")",
            CategoryModel::COLUMN_CAT_ID))
      ->join(array("module" => $this->modulesTable), "item.".ModuleModel::COLUMN_ID_MODULE."=module.".ModuleModel::COLUMN_ID_MODULE, null)
      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.AppCore::getAuth()->getGroupName(), "r__", Db::SQL_LIKE)
      ->where("panel.".self::COLUMN_POSITION , strtolower($side))
      ->where("panel.".self::COLUMN_ENABLE, (int)true)
      ->order("panel.".self::COLUMN_PRIORITY, Db::SQL_DESC);

      return $this->getDb()->fetchObjectArray($sqlSelect);
   }

   private function getTables() {
      $this->panelsTable = AppCore::sysConfig()->getOptionValue("panels_table", "db_tables");
      $this->modulesTable = AppCore::sysConfig()->getOptionValue("modules_table", "db_tables");
      $this->itemsTable = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
      $this->catTable = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
   }
}

?>