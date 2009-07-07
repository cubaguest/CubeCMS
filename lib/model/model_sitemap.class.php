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

class Model_Sitemap extends Model_Db {
   const COLUMN_SITEMAP_FREQUENCY = 'sitemap_changefreq';
   const COLUMN_SITEMAP_PRIORITY = 'sitemap_priority';

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
    * Proměná s názvem tabulky se sekcemi
    * @var string
    */
   private $sectionsTable = null;

   /**
    * Metoda načte kategori, pokud je zadáno Id je načtena určitá, pokud ne je
    * načtena kategorie s nejvyšší prioritou
    * @param integer $idCat -- (option) id kategorie
    */
   public function getItems() {
      $this->getTables();
      $userNameGroup = AppCore::getAuth()->getGroupName();

      $sqlSelect = $this->getDb()->select()->table($this->catTable, 'cat')
      ->colums(array("label" => "IFNULL(cat.".Model_Category::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getLang()
            .", cat.".Model_Category::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getDefaultLang().")",
            "alt" => "IFNULL(item.".Model_Category::COLUMN_CAT_ALT_ORIG.'_'.Locale::getLang()
            .", item.".Model_Category::COLUMN_CAT_ALT_ORIG.'_'.Locale::getDefaultLang().")", Db::COLUMN_ALL))
      ->join(array("item" => $this->itemsTable), array(Model_Category::COLUMN_CAT_ID, 'cat'=>Model_Category::COLUMN_CAT_ID), null, Db::COLUMN_ALL)
      ->join(array("module" => $this->modulesTable), array(Model_Module::COLUMN_ID_MODULE,
          'item'=>Model_Module::COLUMN_ID_MODULE), Db::JOIN_LEFT, Db::COLUMN_ALL)
      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup, "r__", Db::OPERATOR_LIKE)
      ->where('cat.'.Model_Category::COLUMN_CAT_SHOW_IN_MENU, (int)true)
      ->where('cat.'.Model_Category::COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY, (int)false)
      ->group('item.'.Model_Category::COLUMN_CAT_ID)
      ->order("cat.".Model_Category::COLUMN_CAT_PRIORITY, Db::ORDER_DESC)
      ->order("item.".Model_Module::COLUMN_PRIORITY, Db::ORDER_DESC);

//      if(!$this->auth()->isLogin()){
//         $menuSelect->where("cat.".self::COLUMN_CATEGORY_SHOW_WHEN_LOGIN_ONLY, (int)false);
//      }

      return $this->getDb()->fetchObjectArray($sqlSelect);
   }

   /**
    * Načte tabulky
    */
   private function getTables() {
      $this->catTable = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
		$this->modulesTable = AppCore::sysConfig()->getOptionValue("modules_table", "db_tables");
		$this->itemsTable = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
		$this->sectionsTable = AppCore::sysConfig()->getOptionValue("section_table", "db_tables");
   }

   /**
    * Načte Itemy podle sekcí
    * @return array -- pole s itemy
    */
   public function getItemsOrderBySections() {
      $this->getTables();
      $userNameGroup = AppCore::getAuth()->getGroupName();
      
      $sqlSelect = $this->getDb()->select()->table($this->itemsTable, 'items')
      ->colums()
      ->join(array('cat' => $this->catTable), array('items' => Model_Category::COLUMN_CAT_ID,
            Model_Category::COLUMN_CAT_ID), null,
         array(Model_Category::COLUMN_CAT_LABEL=> 'IFNULL(cat.'.Model_Category::COLUMN_CAT_LABEL_ORIG
            .'_'.Locale::getLang().', cat.'.Model_Category::COLUMN_CAT_LABEL_ORIG.'_'
            .Locale::getDefaultLang().')',
            Model_Category::COLUMN_CAT_ALT=> 'IFNULL(cat.'.Model_Category::COLUMN_CAT_ALT_ORIG
            .'_'.Locale::getLang().', cat.'.Model_Category::COLUMN_CAT_ALT_ORIG.'_'
            .Locale::getDefaultLang().')',
            Model_Category::COLUMN_CAT_SEC_ID, Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ,
            Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY))
      ->join(array('sec'=>$this->sectionsTable), array(Model_Sections::COLUMN_SEC_ID, 'cat'=>Model_Category::COLUMN_CAT_SEC_ID),
         null, array(Model_Sections::COLUMN_SEC_LABEL=>'IFNULL(sec.'.Model_Sections::COLUMN_SEC_LABEL_ORIG
            .'_'.Locale::getLang().', sec.'.Model_Sections::COLUMN_SEC_LABEL_ORIG.'_'
            .Locale::getDefaultLang().')',
            Model_Sections::COLUMN_SEC_ID))
      ->join(array('modules' => $this->modulesTable),array(Model_Module::COLUMN_ID_MODULE, 'items'=>Model_Module::COLUMN_ID_MODULE),
         null, Db::COLUMN_ALL)
      ->where("items.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup, "r__", Db::OPERATOR_LIKE)
      ->where('cat.'.Model_Category::COLUMN_CAT_SHOW_IN_MENU, (int)true)
      ->where('cat.'.Model_Category::COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY, (int)false)
      ->order('sec.'.Model_Sections::COLUMN_SEC_PRIORITY, Db::ORDER_DESC)
      ->order('sec.'.Model_Sections::COLUMN_SEC_ID)
      ->order('cat.'.Model_Category::COLUMN_CAT_PRIORITY, Db::ORDER_DESC)
      ->order('cat.'.Model_Category::COLUMN_CAT_ID);
      return $this->getDb()->fetchObjectArray($sqlSelect);
   }
}
?>