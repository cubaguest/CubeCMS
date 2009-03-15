<?php
/**
 * Třída Modelu pro práci s moduly.
 * Třída, která umožňuje pracovet s modelem modulů
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.2 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vytvoření modelu pro práci s moduly
 */

class ModuleModel extends DbModel {

	 /**
	  * Názvy sloupců v db tabulce
	  * @var string
	  */
	const COLUMN_LABEL      = 'label';
	const COLUMN_ALT        = 'alt';
	const COLUMN_ID_MODULE  = 'id_module';
	const COLUMN_PRIORITY   = 'priority';



	const COLUMN_SEC_LABEL 	= 'slabel';
	const COLUMN_CAT_ID		= 'id_category';
	const COLUMN_SEC_ID		= 'id_section';
	const COLUMN_CAT_URLKEY	= 'urlkey';
	const COLUMN_CAT_LPANEL	= 'left_panel';
	const COLUMN_CAT_RPANEL	= 'right_panel';
	const COLUMN_CAT_PARAMS	= 'cparams';
	const COLUMN_CAT_SHOW_IN_MENU	= 'show_in_menu';
	const COLUMN_CAT_PROTECTED	= 'protected';

   const COLUMN_CAT_LABEL_ORIG = 'label';
   const COLUMN_CAT_ALT_ORIG = 'alt';

   const COLUMN_CAT_ACTIVE = 'active';

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
    * Metoda načte moduly z db
    * @return array -- pole s moduly
    */
   public function getModules() {
      $this->getTables();
      $userNameGroup = AppCore::getAuth()->getGroupName();

      $sqlSelect = $this->getDb()->select()->table($this->itemsTable, 'item')
      ->colums(array(self::COLUMN_LABEL => "IFNULL(item.".self::COLUMN_LABEL
            .'_'.Locale::getLang().", item.".self::COLUMN_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_ALT => "IFNULL(item.".self::COLUMN_ALT.'_'.Locale::getLang().", item."
            .self::COLUMN_ALT.'_'.Locale::getDefaultLang().")", Db::COLUMN_ALL))
      ->join(array("module" => $this->modulesTable), 
         array("item" => self::COLUMN_ID_MODULE, self::COLUMN_ID_MODULE),null,Db::COLUMN_ALL)
      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup,"r__", Db::OPERATOR_LIKE)
      ->where("item.".CategoryModel::COLUMN_CAT_ID, Category::getId())
      ->order("item.".self::COLUMN_PRIORITY, Db::ORDER_DESC)
      ->order(self::COLUMN_LABEL);
      return $this->getDb()->fetchObjectArray($sqlSelect);
   }

   private function getTables() {
      $this->catTable = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
		$this->modulesTable = AppCore::sysConfig()->getOptionValue("modules_table", Config::SECTION_DB_TABLES);
		$this->itemsTable = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
   }
}

?>