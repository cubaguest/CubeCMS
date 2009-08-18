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
    * Tabulka s moduly
    */
    const DB_TABLE_MODULES = 'modules';

   /**
    * Tabulka s itemy
    */
    const DB_TABLE_ITEMS = 'items';

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
    * Název skupiny uživatele
    * @var atring 
    */
   private $userNameGroup = null;

   protected function init() {
      $this->userNameGroup = AppCore::getAuth()->getGroupName();
   }

   /**
    * Metoda načte moduly z db
    * @return array -- pole s moduly
    */
   public function getModules() {
      $sqlSelect = $this->getDb()->select()->table(Db::table(self::DB_TABLE_ITEMS), 'item')
      ->colums(array(self::COLUMN_ITEM_LABEL => "IFNULL(item.".self::COLUMN_ITEM_LABEL
            .'_'.Locale::getLang().", item.".self::COLUMN_ITEM_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_ITEM_ALT => "IFNULL(item.".self::COLUMN_ITEM_ALT.'_'.Locale::getLang().", item."
            .self::COLUMN_ITEM_ALT.'_'.Locale::getDefaultLang().")", Db::COLUMN_ALL))
      ->join(array("module" => Db::table(self::DB_TABLE_MODULES)),
         array("item" => self::COLUMN_ID_MODULE, self::COLUMN_ID_MODULE),null,Db::COLUMN_ALL)
      ->join(array("cat" => Db::table(Model_Category::DB_TABLE)),
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
    * Metoda načte požadovaný item i s modulem a kategoriílocalhost
    * @param integer $idItem  -- id item
    */
   public function getModule($idItem) {
      $sqlSelect = $this->getDb()->select()->table(Db::table(self::DB_TABLE_ITEMS), 'item')
      ->colums(array(self::COLUMN_ITEM_LABEL => "IFNULL(item.".self::COLUMN_ITEM_LABEL
            .'_'.Locale::getLang().", item.".self::COLUMN_ITEM_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_ITEM_ALT => "IFNULL(item.".self::COLUMN_ITEM_ALT.'_'.Locale::getLang().", item."
            .self::COLUMN_ITEM_ALT.'_'.Locale::getDefaultLang().")", Db::COLUMN_ALL))
      ->join(array("module" => Db::table(self::DB_TABLE_MODULES)),
         array("item" => self::COLUMN_ID_MODULE, self::COLUMN_ID_MODULE),null,Db::COLUMN_ALL)
      ->join(array("cat" => Db::table(Model_Category::DB_TABLE)),
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