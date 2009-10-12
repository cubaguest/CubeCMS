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
   //   const COLUMN_SEC_ID	= 'id_section';
   //   const COLUMN_MODULE_ID	= 'id_module';
   const COLUMN_MODULE	= 'module';
   const COLUMN_URLKEY	= 'urlkey';
   const COLUMN_CAT_LPANEL	= 'left_panel';
   const COLUMN_CAT_RPANEL	= 'right_panel';
   const COLUMN_PARAMS	= 'params';
   const COLUMN_CAT_SHOW_IN_MENU	= 'show_in_menu';
   const COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY 	= 'show_when_login_only';
   const COLUMN_CAT_PROTECTED	= 'protected';
   const COLUMN_PRIORITY	= 'priority';
   const COLUMN_GROUP_PREFIX	= 'group_';
   const COLUMN_ACTIVE = 'active';
   const COLUMN_KEYWORDS = 'keywords';
   const COLUMN_DESCRIPTION = 'description';

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
      //         $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
      //             INNER JOIN ".Db_PDO::table(Model_Sections::DB_TABLE)." AS sec ON cat.".self::COLUMN_SEC_ID
      //             ." = sec.".Model_Sections::COLUMN_SEC_ID."
      //             WHERE (cat.".self::COLUMN_GROUP_PREFIX.$userNameGroup." LIKE 'r__')
      //             AND (cat.".self::COLUMN_ACTIVE." = 1) AND (cat.".self::COLUMN_URLKEY.'_'.Locale::getLang()
      //             ." = ".$dbc->quote($catKey).") LIMIT 0, 1");
         $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
             WHERE (cat.".self::COLUMN_GROUP_PREFIX.$userNameGroup." LIKE 'r__')
             AND (cat.".self::COLUMN_ACTIVE." = 1) AND (cat.".self::COLUMN_URLKEY.'_'.Locale::getLang()
             ." = ".$dbc->quote($catKey).") LIMIT 0, 1");
      } else {
         $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
             WHERE (cat.".self::COLUMN_GROUP_PREFIX.$userNameGroup." LIKE 'r__')
             AND (cat.".self::COLUMN_ACTIVE." = 1)
             ORDER BY cat.".self::COLUMN_PRIORITY." ASC LIMIT 0, 1");
      //         $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
      //             INNER JOIN ".Db_PDO::table(Model_Sections::DB_TABLE)." AS sec ON cat.".self::COLUMN_SEC_ID
      //             ." = sec.".Model_Sections::COLUMN_SEC_ID."
      //             WHERE (cat.".self::COLUMN_GROUP_PREFIX.$userNameGroup." LIKE 'r__')
      //             AND (cat.".self::COLUMN_ACTIVE." = 1)
      //             ORDER BY cat.".self::COLUMN_PRIORITY." ASC LIMIT 0, 1");
      }
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_INTO, new Model_LangContainer());
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
    * Metoda načte kategori se zadaným id
    * @param string $catId -- id kategorie
    */
   public function getCategoryWoutRights($catId) {
      $userNameGroup = AppCore::getAuth()->getGroupName();

      $dbc = new Db_PDO();
      //         $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
      //             INNER JOIN ".Db_PDO::table(Model_Sections::DB_TABLE)." AS sec ON cat.".self::COLUMN_SEC_ID
      //             ." = sec.".Model_Sections::COLUMN_SEC_ID."
      //             WHERE (cat.".self::COLUMN_ACTIVE." = 1) AND (cat.".self::COLUMN_CAT_ID
      //             ." = ".$dbc->quote($catId).") LIMIT 0, 1");
      $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
             WHERE (cat.".self::COLUMN_ACTIVE." = 1) AND (cat.".self::COLUMN_CAT_ID
          ." = ".$dbc->quote($catId).") LIMIT 0, 1");
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_INTO, new Model_LangContainer());
      return $dbst->fetch();

   }

   /**
    * Metoda načte všechny kategorie
    * @return PDOStatement -- objekt s daty
    */
   public function getCategoryList($withRights = true) {
      $dbc = new Db_PDO();
      //      $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
      //             INNER JOIN ".Db_PDO::table(Model_Sections::DB_TABLE)." AS sec ON cat.".self::COLUMN_SEC_ID
      //          ." = sec.".Model_Sections::COLUMN_SEC_ID."
      //             ORDER BY cat.".self::COLUMN_PRIORITY." DESC");
      if($withRights) {
         $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
             WHERE (cat.".self::COLUMN_GROUP_PREFIX.AppCore::getAuth()->getGroupName()." LIKE 'r__')
             ORDER BY cat.".self::COLUMN_PRIORITY." DESC");
      } else {
         $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
            ORDER BY cat.".self::COLUMN_PRIORITY." DESC");
      }
      $dbst->execute();

      //      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      return $dbst->fetchAll();

   //      SELECT IFNULL(cat.label_cs, cat.label_cs) AS clabel, cat.`id_category`, cat.`left_panel`,
   //      cat.`right_panel`, cat.`id_section`, cat.`cparams`, IFNULL(sec.label_cs, sec.label_cs) AS slabel,
   //      IFNULL(sec.alt_cs, sec.alt_cs) AS salt FROM `vypecky_categories` AS cat
   //      INNER JOIN `vypecky_items` AS item ON cat.id_category = item.id_category
   //      INNER JOIN `vypecky_sections` AS sec ON cat.id_section = sec.id_section
   //      WHERE (item.group_guest LIKE 'r__') AND (cat.active = 1)
   //      ORDER BY sec.priority DESC, cat.priority DESC, clabel ASC LIMIT 0, 1
   }

   /**
    * Metoda uloží novou kategorii
    * @param <type> $name
    * @param <type> $alt
    * @param <type> $keywords
    * @param <type> $description
    * @param <type> $urlkey
    * @param <type> $priority
    * @param <type> $panelLeft
    * @param <type> $panelRight
    * @param <type> $showInMenu
    * @param <type> $showWhenLoginOnly
    * @param <type> $rights
    * @param <type> $sitemapPriority
    * @param <type> $sitemapFrequency
    */
   public function saveNewCategory($name, $alt, $module, $keywords, $description, $urlkey,
       $priority, $panelLeft, $panelRight, $showInMenu, $showWhenLoginOnly, $rights, $sitemapPriority,
       $sitemapFrequency) {

      $this->setIUValues(array(self::COLUMN_CAT_LABEL => $name,
                   self::COLUMN_CAT_ALT => $alt,
          self::COLUMN_MODULE => $module, self::COLUMN_KEYWORDS => $keywords,
          self::COLUMN_DESCRIPTION => $description, self::COLUMN_URLKEY => $urlkey,
          self::COLUMN_PRIORITY => $priority, self::COLUMN_CAT_SHOW_IN_MENU => $showInMenu,
          self::COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY => $showWhenLoginOnly,
          self::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY => $sitemapPriority,
          self::COLUMN_CAT_SITEMAP_CHANGE_FREQ => $sitemapFrequency));

      $this->setIUValues($rights);

      $dbc = new Db_PDO();
      $dbst = $dbc->exec("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
          ." ".$this->getInsertLabels()." VALUES ".$this->getInsertValues());

      return true;
   }

   /**
    * Metoda uloží novou kategorii
    * @param <type> $name
    * @param <type> $alt
    * @param <type> $keywords
    * @param <type> $description
    * @param <type> $urlkey
    * @param <type> $priority
    * @param <type> $panelLeft
    * @param <type> $panelRight
    * @param <type> $showInMenu
    * @param <type> $showWhenLoginOnly
    * @param <type> $rights
    * @param <type> $sitemapPriority
    * @param <type> $sitemapFrequency
    */
   public function saveEditCategory($id, $name, $alt, $module, $keywords, $description, $urlkey,
       $priority, $panelLeft, $panelRight, $showInMenu, $showWhenLoginOnly, $rights, $sitemapPriority,
       $sitemapFrequency) {

      $this->setIUValues(array(self::COLUMN_CAT_LABEL => $name,
                   self::COLUMN_CAT_ALT => $alt,
          self::COLUMN_MODULE => $module, self::COLUMN_KEYWORDS => $keywords,
          self::COLUMN_DESCRIPTION => $description, self::COLUMN_URLKEY => $urlkey,
          self::COLUMN_PRIORITY => $priority, self::COLUMN_CAT_SHOW_IN_MENU => $showInMenu,
          self::COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY => $showWhenLoginOnly,
          self::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY => $sitemapPriority,
          self::COLUMN_CAT_SITEMAP_CHANGE_FREQ => $sitemapFrequency));

      $this->setIUValues($rights);

      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET ".$this->getUpdateValues()
          ." WHERE ".self::COLUMN_CAT_ID." = :id");

      return $dbst->execute(array(':id' => $id));

      return true;
   }

   public function deleteCategory($id) {
      $dbc = new Db_PDO();
      $st = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          . " WHERE ".self::COLUMN_CAT_ID." = :id");
      return $st->execute(array(':id' => $id));

   }
}
?>