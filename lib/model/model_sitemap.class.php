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
    * Metoda načte kategori, pokud je zadáno Id je načtena určitá, pokud ne je
    * načtena kategorie s nejvyšší prioritou
    * @param integer $idCat -- (option) id kategorie
    */
   public function getItems() {
      $userNameGroup = AppCore::getAuth()->getGroupName();

      $sqlSelect = $this->getDb()->select()->table(Db::table(Model_Category::DB_TABLE), 'cat')
      ->colums(array("label" => "IFNULL(cat.".Model_Category::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getLang()
            .", cat.".Model_Category::COLUMN_CAT_LABEL_ORIG.'_'.Locale::getDefaultLang().")",
            "alt" => "IFNULL(item.".Model_Category::COLUMN_CAT_ALT_ORIG.'_'.Locale::getLang()
            .", item.".Model_Category::COLUMN_CAT_ALT_ORIG.'_'.Locale::getDefaultLang().")", Db::COLUMN_ALL))
      ->join(array("item" => Db::table(Model_Module::DB_TABLE_ITEMS)), array(Model_Category::COLUMN_CAT_ID, 'cat'=>Model_Category::COLUMN_CAT_ID), null, Db::COLUMN_ALL)
      ->join(array("module" => Db::table(Model_Module::DB_TABLE_MODULES)), array(Model_Module::COLUMN_ID_MODULE,
          'item'=>Model_Module::COLUMN_ID_MODULE), Db::JOIN_LEFT, Db::COLUMN_ALL)
      ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup, "r__", Db::OPERATOR_LIKE)
      ->where('cat.'.Model_Category::COLUMN_CAT_SHOW_IN_MENU, (int)true)
      ->where('cat.'.Model_Category::COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY, (int)false)
      ->group('item.'.Model_Category::COLUMN_CAT_ID)
      ->order("cat.".Model_Category::COLUMN_CAT_PRIORITY, Db::ORDER_DESC)
      ->order("item.".Model_Module::COLUMN_PRIORITY, Db::ORDER_DESC);

      return $this->getDb()->fetchObjectArray($sqlSelect);
   }

   /**
    * Načte Itemy podle sekcí
    * @return array -- pole s itemy
    */
   public function getItemsOrderBySections() {
      $userNameGroup = AppCore::getAuth()->getGroupName();
      
      $sqlSelect = $this->getDb()->select()->table(Db::table(Model_Module::DB_TABLE_ITEMS), 'items')
      ->colums()
      ->join(array('cat' => Db::table(Model_Category::DB_TABLE)), array('items' => Model_Category::COLUMN_CAT_ID,
            Model_Category::COLUMN_CAT_ID), null,
         array(Model_Category::COLUMN_CAT_LABEL=> 'IFNULL(cat.'.Model_Category::COLUMN_CAT_LABEL_ORIG
            .'_'.Locale::getLang().', cat.'.Model_Category::COLUMN_CAT_LABEL_ORIG.'_'
            .Locale::getDefaultLang().')',
            Model_Category::COLUMN_CAT_ALT=> 'IFNULL(cat.'.Model_Category::COLUMN_CAT_ALT_ORIG
            .'_'.Locale::getLang().', cat.'.Model_Category::COLUMN_CAT_ALT_ORIG.'_'
            .Locale::getDefaultLang().')',
            Model_Category::COLUMN_CAT_SEC_ID, Model_Category::COLUMN_CAT_SITEMAP_CHANGE_FREQ,
            Model_Category::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY))
      ->join(array('sec'=>Db::table(Model_Sections::DB_TABLE)), array(Model_Sections::COLUMN_SEC_ID, 'cat'=>Model_Category::COLUMN_CAT_SEC_ID),
         null, array(Model_Sections::COLUMN_SEC_LABEL=>'IFNULL(sec.'.Model_Sections::COLUMN_SEC_LABEL_ORIG
            .'_'.Locale::getLang().', sec.'.Model_Sections::COLUMN_SEC_LABEL_ORIG.'_'
            .Locale::getDefaultLang().')',
            Model_Sections::COLUMN_SEC_ID))
      ->join(array('modules' => Db::table(Model_Module::DB_TABLE_MODULES)),array(Model_Module::COLUMN_ID_MODULE, 'items'=>Model_Module::COLUMN_ID_MODULE),
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