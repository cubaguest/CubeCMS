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
    * Tabulka s detaily
    */
    const DB_TABLE = 'panels';

   /**
    * Konstanty s názzvy sloupců
    */
   const COLUMN_POSITION = 'position';
   const COLUMN_ENABLE = 'enable';
   const COLUMN_PRIORITY = 'priority';
	const COLUMN_ID_ITEM    = 'id_item';

   /**
    * Metoda načte panely z db
    * @param string -- pozice panelu
    * @return array -- pole s panely
    */
   public function getPanel($side) {
      $sqlSelect = $this->getDb()->select()->table(Db::table(self::DB_TABLE), 'panel')
      ->join(array("item" => Db::table(Model_Module::DB_TABLE_ITEMS)), array(self::COLUMN_ID_ITEM, 'panel' => Model_Module::COLUMN_ITEM_ID),
            null, array(Model_Module::COLUMN_ITEM_LABEL => "IFNULL(item.".Model_Module::COLUMN_ITEM_LABEL.'_'.Locale::getLang()
            .", item.".Model_Module::COLUMN_ITEM_LABEL.'_'.Locale::getDefaultLang().")",
             Model_Module::COLUMN_ITEM_ALT => "IFNULL(item.".Model_Module::COLUMN_ITEM_ALT.'_'.Locale::getLang()
             .", item.".Model_Module::COLUMN_ITEM_ALT.'_'.Locale::getDefaultLang().")", Db::COLUMN_ALL))
      ->join(array("cat" => Db::table(Model_Category::DB_TABLE)),
          array('item' =>Model_Category::COLUMN_CAT_ID,Model_Category::COLUMN_CAT_ID),
          null, array(Model_Category::COLUMN_CAT_LABEL => "IFNULL(cat.".Model_Category::COLUMN_CAT_LABEL_ORIG.'_'
            .Locale::getLang().", cat.".Model_Category::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getDefaultLang().")",
            Model_Category::COLUMN_CAT_ALT => "IFNULL(cat.".Model_Category::COLUMN_CAT_ALT_ORIG.'_'
            .Locale::getLang().", cat.".Model_Category::COLUMN_CAT_ALT_ORIG.'_'.Locale::getDefaultLang().")",
            Model_Category::COLUMN_CAT_ID))
      ->join(array("module" => Db::table(Model_Module::DB_TABLE_MODULES)), array('item'=>Model_Module::COLUMN_ID_MODULE
            ,Model_Module::COLUMN_ID_MODULE), null, Db::COLUMN_ALL)
      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.AppCore::getAuth()->getGroupName(), "r__", Db::OPERATOR_LIKE)
      ->where("panel.".self::COLUMN_POSITION , strtolower($side))
      ->where("panel.".self::COLUMN_ENABLE, (int)true)
      ->order("panel.".self::COLUMN_PRIORITY, Db::ORDER_DESC)
      ->order(Model_Category::COLUMN_CAT_LABEL);

      return $this->getDb()->fetchObjectArray($sqlSelect);
   }
}

?>