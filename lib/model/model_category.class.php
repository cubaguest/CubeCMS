<?php
/**
 * Třída Modelu pro práci s kategoriemi.
 * Třída, která umožňuje pracovet s modelem kategorií
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.2 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro vytvoření modelu pro práci s kategoriemi
 */

class Model_Category extends Model_Db {
   /**
    * Tabulka s detaily
    */
    const DB_TABLE = 'categories';

   /**
    * Názvy sloupců v db tabulce
    * @var string
    */
   const COLUMN_CAT_LABEL 	= 'clabel';
   const COLUMN_CAT_ALT 	= 'calt';
   const COLUMN_CAT_ID		= 'id_category';
   const COLUMN_CAT_SEC_ID	= 'id_section';
   const COLUMN_CAT_URLKEY	= 'urlkey';
   const COLUMN_CAT_LPANEL	= 'left_panel';
   const COLUMN_CAT_RPANEL	= 'right_panel';
   const COLUMN_CAT_PARAMS	= 'cparams';
   const COLUMN_CAT_SHOW_IN_MENU	= 'show_in_menu';
   const COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY 	= 'show_when_login_only';
   const COLUMN_CAT_PROTECTED	= 'protected';
   const COLUMN_CAT_PRIORITY	= 'priority';

   const COLUMN_CAT_LABEL_ORIG = 'label';
   const COLUMN_CAT_ALT_ORIG = 'alt';

   const COLUMN_CAT_ACTIVE = 'active';

   const COLUMN_CAT_SITEMAP_CHANGE_FREQ = 'sitemap_changefreq';
   const COLUMN_CAT_SITEMAP_CHANGE_PRIORITY = 'sitemap_priority';

   /**
    * Metoda načte kategori, pokud je zadáno Id je načtena určitá, pokud ne je
    * načtena kategorie s nejvyšší prioritou
    * @param integer $idCat -- (option) id kategorie
    */
   public function getCategory($idCat = null) {
      $userNameGroup = AppCore::getAuth()->getGroupName();
      $sqlSelect = $this->getDb()->select()->table(Db::table(self::DB_TABLE), 'cat')
          ->colums(array(self::COLUMN_CAT_LABEL => "IFNULL(cat.".self::COLUMN_CAT_LABEL_ORIG."_".Locale::getLang()
          .", cat.".self::COLUMN_CAT_LABEL_ORIG."_".Locale::getDefaultLang().")", self::COLUMN_CAT_ID, self::COLUMN_CAT_LPANEL,
          self::COLUMN_CAT_RPANEL, self::COLUMN_CAT_SEC_ID, self::COLUMN_CAT_PARAMS))
          ->join(array('item' => Db::table(Model_Module::DB_TABLE_ITEMS)),
          array('cat' => self::COLUMN_CAT_ID, self::COLUMN_CAT_ID), Db::JOIN_INNER)
          ->join(array('sec'=>Db::table(Model_Sections::DB_TABLE)),
          array('cat' => self::COLUMN_CAT_SEC_ID, Model_Sections::COLUMN_SEC_ID), Db::JOIN_INNER,
          array(Model_Sections::COLUMN_SEC_LABEL => 'IFNULL(sec.'.Model_Sections::COLUMN_SEC_LABEL_ORIG.'_'.Locale::getLang()
          .', sec.'.Model_Sections::COLUMN_SEC_LABEL_ORIG.'_'.Locale::getDefaultLang().")",
            Model_Sections::COLUMN_SEC_ALT => 'IFNULL(sec.'.Model_Sections::COLUMN_SEC_ALT_ORIG.'_'.Locale::getLang()
          .', sec.'.Model_Sections::COLUMN_SEC_ALT_ORIG.'_'.Locale::getDefaultLang().")"))
          ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup, 'r__', Db::OPERATOR_LIKE)
          ->where("cat.".self::COLUMN_CAT_ACTIVE, 1)
          ->order("sec.".Model_Sections::COLUMN_SEC_PRIORITY, Db::ORDER_DESC)
          ->order("cat.".Model_Category::COLUMN_CAT_PRIORITY, Db::ORDER_DESC)
          ->order(Model_Category::COLUMN_CAT_LABEL)
          ->limit(0,1);

      if($idCat != null) {
         $sqlSelect->where("cat.".self::COLUMN_CAT_ID, (int)$idCat);
      }

      return $this->getDb()->fetchObject($sqlSelect);
   }
}
?>