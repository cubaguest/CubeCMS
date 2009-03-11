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
    * Metoda načte kategori, pokud je zadáno Id je načtena určitá, pokud ne je
    * načtena kategorie s nejvyšší prioritou
    * @param integer $idCat -- (option) id kategorie
    */
   public function getItems() {
      $this->getTables();
      $userNameGroup = AppCore::getAuth()->getGroupName();


//      ->from(array("cat" => $categoryTable), array('label' => "IFNULL(item.".Category::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getLang()
//            .", item.".Category::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getDefaultLang().")",
//            "alt" => "IFNULL(item.alt_".Locale::getLang().", item.alt_".Locale::getDefaultLang().")", '*'))
//      ->join(array("item" => $itemsTable), "item.".Category::COLUMN_CAT_ID."=cat.".Category::COLUMN_CAT_ID, null)
//      ->join(array("module" => $modulesTable), "module.id_module=item.id_module", 'LEFT')
//      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
//      ->group('item.id_category')
//      ->order("cat.priority", "desc")
//      ->order("item.priority", 'desc');


      $sqlSelect = $this->getDb()->select()->table($this->catTable, 'cat')
      ->colums(array("label" => "IFNULL(cat.".CategoryModel::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getLang()
            .", cat.".CategoryModel::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getDefaultLang().")",
            "alt" => "IFNULL(item.".CategoryModel::COLUMN_CAT_ALT_ORIG.'_'.Locale::getLang()
            .", item.".CategoryModel::COLUMN_CAT_ALT_ORIG.'_'.Locale::getDefaultLang().")", Db::SQL_ALL))
      ->join(array("item" => $this->itemsTable), "item.".CategoryModel::COLUMN_CAT_ID."=cat.".CategoryModel::COLUMN_CAT_ID, null)
      ->join(array("module" => $this->modulesTable), "module.".ModuleModel::COLUMN_ID_MODULE."=item.".ModuleModel::COLUMN_ID_MODULE, 'LEFT')
      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup, "r__", Db::SQL_LIKE)
      ->group('item.'.CategoryModel::COLUMN_CAT_ID)
      ->order("cat.".CategoryModel::COLUMN_CAT_PRIORITY, "desc")
      ->order("item.".ModuleModel::COLUMN_PRIORITY, 'desc');

      return $this->getDb()->fetchObjectArray($sqlSelect);
   }

   private function getTables() {
      $this->catTable = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
		$this->modulesTable = AppCore::sysConfig()->getOptionValue("modules_table", "db_tables");
		$this->itemsTable = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
   }
}

?>