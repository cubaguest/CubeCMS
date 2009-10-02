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

class Model_Category extends Model_PDO {
/**
 * Tabulka s detaily
 */
   const DB_TABLE = 'categories';

   /**
    * Názvy sloupců v db tabulce
    * @var string
    */
   const COLUMN_CAT_LABEL 	= 'label';
   const COLUMN_CAT_ALT 	= 'alt';
   const COLUMN_CAT_ID		= 'id_category';
   const COLUMN_SEC_ID	= 'id_section';
   const COLUMN_MODULE_ID	= 'id_module';
   const COLUMN_URLKEY	= 'urlkey';
   const COLUMN_CAT_LPANEL	= 'left_panel';
   const COLUMN_CAT_RPANEL	= 'right_panel';
   const COLUMN_PARAMS	= 'params';
   const COLUMN_CAT_SHOW_IN_MENU	= 'show_in_menu';
   const COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY 	= 'show_when_login_only';
   const COLUMN_CAT_PROTECTED	= 'protected';
   const COLUMN_PRIORITY	= 'priority';
   const COLUMN_CAT_GROUP_PREFIX	= 'group_';
   const COLUMN_ACTIVE = 'active';

   const COLUMN_CAT_SITEMAP_CHANGE_FREQ = 'sitemap_changefreq';
   const COLUMN_CAT_SITEMAP_CHANGE_PRIORITY = 'sitemap_priority';

   /**
    * Metoda načte kategori, pokud je zadán klíč je načtena určitá, pokud ne je
    * načtena kategorie s nejvyšší prioritou
    * @param string $catKey -- (option) klíč kategorie
    */
   public function getCategory($catKey = null) {
      $userNameGroup = AppCore::getAuth()->getGroupName();

      $dbc = new Db_PDO();
      if($catKey != null) {
         $dbst = $dbc->query("SELECT cat.*, sec.*, module.* FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
             INNER JOIN ".Db_PDO::table(Model_Sections::DB_TABLE)." AS sec ON cat.".self::COLUMN_SEC_ID
             ." = sec.".Model_Sections::COLUMN_SEC_ID."
             INNER JOIN ".Db_PDO::table(Model_Module::DB_TABLE_MODULES)." AS module ON cat.".self::COLUMN_MODULE_ID
             ." = module.".Model_Module::COLUMN_ID_MODULE."
             WHERE (cat.".self::COLUMN_CAT_GROUP_PREFIX.$userNameGroup." LIKE 'r__')
             AND (cat.".self::COLUMN_ACTIVE." = 1) AND (cat.".self::COLUMN_URLKEY.'_'.Locale::getLang()
            ." = ".$dbc->quote($catKey).") LIMIT 0, 1");
      } else {
         $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
             INNER JOIN ".Db_PDO::table(Model_Sections::DB_TABLE)." AS sec ON cat.".self::COLUMN_SEC_ID
             ." = sec.".Model_Sections::COLUMN_SEC_ID."
             INNER JOIN ".Db_PDO::table(Model_Module::DB_TABLE_MODULES)." AS module ON cat.".self::COLUMN_MODULE_ID
             ." = module.".Model_Module::COLUMN_ID_MODULE."
             WHERE (cat.".self::COLUMN_CAT_GROUP_PREFIX.$userNameGroup." LIKE 'r__')
             AND (cat.".self::COLUMN_ACTIVE." = 1)
             ORDER BY cat.".self::COLUMN_PRIORITY." DESC LIMIT 0, 1");
      }
      $dbst->execute();

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);
      return $dbst->fetch();

   //      SELECT IFNULL(cat.label_cs, cat.label_cs) AS clabel, cat.`id_category`, cat.`left_panel`,
   //      cat.`right_panel`, cat.`id_section`, cat.`cparams`, IFNULL(sec.label_cs, sec.label_cs) AS slabel,
   //      IFNULL(sec.alt_cs, sec.alt_cs) AS salt FROM `vypecky_categories` AS cat
   //      INNER JOIN `vypecky_items` AS item ON cat.id_category = item.id_category
   //      INNER JOIN `vypecky_sections` AS sec ON cat.id_section = sec.id_section
   //      WHERE (item.group_guest LIKE 'r__') AND (cat.active = 1)
   //      ORDER BY sec.priority DESC, cat.priority DESC, clabel ASC LIMIT 0, 1
   }
   
   /**
    * Metoda načte všechny kategorie
    * @return PDOStatement -- objekt s daty
    */
   public function getCategoryList() {
      $dbc = new Db_PDO();
         $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
             INNER JOIN ".Db_PDO::table(Model_Sections::DB_TABLE)." AS sec ON cat.".self::COLUMN_SEC_ID
             ." = sec.".Model_Sections::COLUMN_SEC_ID."
             INNER JOIN ".Db_PDO::table(Model_Module::DB_TABLE_MODULES)." AS module ON cat.".self::COLUMN_MODULE_ID
             ." = module.".Model_Module::COLUMN_ID_MODULE."
             ORDER BY cat.".self::COLUMN_PRIORITY." DESC");
      $dbst->execute();

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);
      return $dbst;

   //      SELECT IFNULL(cat.label_cs, cat.label_cs) AS clabel, cat.`id_category`, cat.`left_panel`,
   //      cat.`right_panel`, cat.`id_section`, cat.`cparams`, IFNULL(sec.label_cs, sec.label_cs) AS slabel,
   //      IFNULL(sec.alt_cs, sec.alt_cs) AS salt FROM `vypecky_categories` AS cat
   //      INNER JOIN `vypecky_items` AS item ON cat.id_category = item.id_category
   //      INNER JOIN `vypecky_sections` AS sec ON cat.id_section = sec.id_section
   //      WHERE (item.group_guest LIKE 'r__') AND (cat.active = 1)
   //      ORDER BY sec.priority DESC, cat.priority DESC, clabel ASC LIMIT 0, 1
   }
}
?>