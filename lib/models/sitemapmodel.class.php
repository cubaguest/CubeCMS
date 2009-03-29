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

class SitemapModel extends DbModel {
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
      ->colums(array("label" => "IFNULL(cat.".CategoryModel::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getLang()
            .", cat.".CategoryModel::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getDefaultLang().")",
            "alt" => "IFNULL(item.".CategoryModel::COLUMN_CAT_ALT_ORIG.'_'.Locale::getLang()
            .", item.".CategoryModel::COLUMN_CAT_ALT_ORIG.'_'.Locale::getDefaultLang().")", Db::COLUMN_ALL))
      ->join(array("item" => $this->itemsTable), array(CategoryModel::COLUMN_CAT_ID, 'cat'=>CategoryModel::COLUMN_CAT_ID), null, Db::COLUMN_ALL)
      ->join(array("module" => $this->modulesTable), array(ModuleModel::COLUMN_ID_MODULE,
          'item'=>ModuleModel::COLUMN_ID_MODULE), Db::JOIN_LEFT, Db::COLUMN_ALL)
      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup, "r__", Db::OPERATOR_LIKE)
      ->group('item.'.CategoryModel::COLUMN_CAT_ID)
      ->order("cat.".CategoryModel::COLUMN_CAT_PRIORITY, Db::ORDER_DESC)
      ->order("item.".ModuleModel::COLUMN_PRIORITY, Db::ORDER_DESC);
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
      //SELECT items.*, cat.label_cs AS clabel, cat.*, sec.*, sec.label_cs AS slabel, modules.* FROM `vypecky_items` AS items
      //JOIN vypecky_categories AS cat ON items.id_category = cat.id_category
      //JOIN vypecky_sections AS sec ON cat.id_section = sec.id_section
      //JOIN vypecky_modules AS modules ON items.id_module = modules.id_module
      //WHERE group_guest LIKE 'r__'
      //ORDER BY sec.priority, sec.id_section, cat.priority DESC, cat.id_category
      $sqlSelect = $this->getDb()->select()->table($this->itemsTable, 'items')
      ->colums()
      ->join(array('cat' => $this->catTable), array('items' => CategoryModel::COLUMN_CAT_ID,
            CategoryModel::COLUMN_CAT_ID), null,
         array(CategoryModel::COLUMN_CAT_LABEL=> 'IFNULL(cat.'.CategoryModel::COLUMN_CAT_LABEL_ORIG
            .'_'.Locale::getLang().', cat.'.CategoryModel::COLUMN_CAT_LABEL_ORIG.'_'
            .Locale::getDefaultLang().')',
//            CategoryModel::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getLang(),
            CategoryModel::COLUMN_CAT_ID))
      ->join(array('sec'=>$this->sectionsTable), array(CategoryModel::COLUMN_SEC_ID, 'cat'=>CategoryModel::COLUMN_SEC_ID),
         null, array(CategoryModel::COLUMN_SEC_LABEL=>'IFNULL(cat.'.CategoryModel::COLUMN_SEC_LABEL_ORIG
            .'_'.Locale::getLang().', cat.'.CategoryModel::COLUMN_SEC_LABEL_ORIG.'_'
            .Locale::getDefaultLang().')',
//            CategoryModel::COLUMN_SEC_LABEL_ORIG.'_'.Locale::getLang(),
            SectionsModel::COLUMN_SEC_ID))
      ->join(array('modules' => $this->modulesTable),array(ModuleModel::COLUMN_ID_MODULE, 'items'=>ModuleModel::COLUMN_ID_MODULE),
         null, Db::COLUMN_ALL)
      ->where("items.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup, "r__", Db::OPERATOR_LIKE)
      ->order('sec.'.SectionsModel::COLUMN_SEC_PRIORITY, Db::ORDER_DESC)
      ->order('sec.'.SectionsModel::COLUMN_SEC_ID)
      ->order('cat.'.CategoryModel::COLUMN_CAT_PRIORITY, Db::ORDER_DESC)
      ->order('cat.'.CategoryModel::COLUMN_CAT_ID);
      return $this->getDb()->fetchObjectArray($sqlSelect);
   }
}
?>