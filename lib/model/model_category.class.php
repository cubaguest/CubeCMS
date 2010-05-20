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
 * @todo          nutný refaktoring
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
   const COLUMN_CAT_ID_PARENT		= 'id_parent';
   const COLUMN_ICON	= 'icon';
   //   const COLUMN_MODULE_ID	= 'id_module';
   const COLUMN_MODULE	= 'module';
   const COLUMN_DATADIR	= 'data_dir';
   const COLUMN_URLKEY	= 'urlkey';
   const COLUMN_INDIVIDUAL_PANELS	= 'individual_panels';
   const COLUMN_PARAMS_OLD	= 'params';
   const COLUMN_PARAMS	= 'ser_params';
   const COLUMN_CAT_SHOW_IN_MENU	= 'show_in_menu';
   const COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY 	= 'show_when_login_only';
   const COLUMN_CAT_PROTECTED	= 'protected';
   const COLUMN_PRIORITY	= 'priority';
   const COLUMN_DEF_RIGHT	= 'default_right';
   const COLUMN_ID_GROUP	= 'id_group';
   const COLUMN_ACTIVE = 'active';
   const COLUMN_KEYWORDS = 'keywords';
   const COLUMN_DESCRIPTION = 'description';
   const COLUMN_ORDER = 'order';
   const COLUMN_LEVEL = 'level';
   const COLUMN_CHANGED = 'changed';
   const COLUMN_FEEDS = 'feeds';

   const COLUMN_CAT_SITEMAP_CHANGE_FREQ = 'sitemap_changefreq';
   const COLUMN_CAT_SITEMAP_CHANGE_PRIORITY = 'sitemap_priority';

   /**
    * Pole s kategoriemi
    * @var array
    */
   private $catList = null;

   /**
    * Metoda načte kategori, pokud je zadán klíč je načtena určitá, pokud ne je
    * načtena kategorie s nejvyšší prioritou
    * @param string $catKey -- (option) klíč kategorie
    */
   public function getCategory($catKey = null) {
      $userNameGroup = AppCore::getAuth()->getGroupName();

      $dbc = new Db_PDO();
      if($catKey != null) {
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat"
         ." JOIN ".Model_Rights::getRightsTable()." AS rights ON rights."
         .Model_Rights::COLUMN_ID_CATEGORY." = cat.".self::COLUMN_CAT_ID
             ." WHERE (rights.".Model_Rights::COLUMN_ID_GROUP." = :idgrp AND rights.".Model_Rights::COLUMN_RIGHT." LIKE 'r__')"
             ." AND (cat.".self::COLUMN_ACTIVE." = 1) AND (cat.".self::COLUMN_URLKEY.'_'.Locale::getLang()
             ." = :catkey OR cat.".self::COLUMN_URLKEY.'_'.Locale::getDefaultLang()
             ." = :catkey2) LIMIT 0, 1");

         $dbst->bindValue(":catkey", $catKey);
         $dbst->bindValue(":catkey2", $catKey);
         $dbst->bindValue(":idgrp", AppCore::getAuth()->getGroupId(), PDO::PARAM_INT);
      } else {
            $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat"
         ." JOIN ".Model_Rights::getRightsTable()." AS rights ON rights."
         .Model_Rights::COLUMN_ID_CATEGORY." = cat.".self::COLUMN_CAT_ID
         ." WHERE (rights.".Model_Rights::COLUMN_ID_GROUP." = :idgrp AND rights.".Model_Rights::COLUMN_RIGHT." LIKE 'r__')"
         ." ORDER BY cat.".self::COLUMN_PRIORITY." DESC LIMIT 0, 1");
      
         $dbst->bindValue(":idgrp", AppCore::getAuth()->getGroupId(), PDO::PARAM_INT);
      }
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_INTO, new Model_LangContainer());
      return $dbst->fetch();
   }

   /**
    * Metoda vrací kategorii podle zadaného id
    * @param int $id -- id kategorie
    * @return Object
    */
   public function getCategoryById($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat
             WHERE (cat.".self::COLUMN_CAT_ID." = :idcat) LIMIT 0, 1");
      $dbst->bindValue(':idcat', (int)$id, PDO::PARAM_INT);
      $dbst->setFetchMode(PDO::FETCH_INTO, new Model_LangContainer());
      $dbst->execute();

      return $dbst->fetch();
   }


   /**
    * Metoda načte kategori se zadaným id
    * @param string $catId -- id kategorie
    */
   public function getCategoryWoutRights($catId) {
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
    * @param bool $allCategories -- jestli mají být vráceny všechny kategorie
    * bez ohledu na práva (POZOR!! bezpečnostní riziko)
    * @return PDOStatement -- objekt s daty
    */
   public function getCategoryList($allCategories = false, $forMenu = false) {
         $dbc = new Db_PDO();
         $whereMenu = null;
         if($forMenu === true) $whereMenu = " AND cat.".self::COLUMN_CAT_SHOW_IN_MENU." = 1";
         if(!Auth::isLogin() AND $forMenu === true){
            $whereMenu .= " AND cat.".self::COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY." = 0";
         }

         if(!$allCategories) {
            $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat"
                    ." JOIN ".Model_Rights::getRightsTable()." AS rights ON rights."
                    .Model_Rights::COLUMN_ID_CATEGORY." = cat.".self::COLUMN_CAT_ID
                    ." WHERE (rights.".Model_Rights::COLUMN_ID_GROUP." = :idgrp"
                    ." AND rights.".Model_Rights::COLUMN_RIGHT." LIKE 'r__'"
                    .$whereMenu.")"
                    ." ORDER BY LENGTH(".self::COLUMN_URLKEY."_".Locale::getLang().") DESC");
            $dbst->bindValue(":idgrp", AppCore::getAuth()->getGroupId(), PDO::PARAM_INT);

         } else {
            $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat"
                    ." JOIN ".Model_Rights::getRightsTable()." AS rights ON rights."
                    .Model_Rights::COLUMN_ID_CATEGORY." = cat.".self::COLUMN_CAT_ID
                    ." ORDER BY LENGTH(".self::COLUMN_URLKEY."_".Locale::getLang().") DESC");
         }
         $dbst->execute();
         $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');

         $cats = array();
         //      foreach ($categories as $row) {
         while($row = $dbst->fetch()) {
            $cats[$row->{Model_Category::COLUMN_CAT_ID}] = $row;
         }
         if($allCategories === false) {
            $this->catList = $cats;
         }
         return $cats;
   }

   /**
    * Metoda načte všechny kategorie
    * @param bool $allCategories -- jestli mají být vráceny všechny kategorie
    * bez ohledu na práva (POZOR!! bezpečnostní riziko)
    * @return PDOStatement -- objekt s daty
    */
   public function getCategoriesWithIndPanels() {
         $dbc = new Db_PDO();
         $whereMenu = null;

         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat"
                 ." WHERE ".self::COLUMN_INDIVIDUAL_PANELS." = 1"
                 ." ORDER BY LENGTH(".self::COLUMN_URLKEY."_".Locale::getLang().") DESC");

         $dbst->execute();
         $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');

//         $cats = array();
//         //      foreach ($categories as $row) {
//         while($row = $dbst->fetch()) {
//            $cats[$row->{Model_Category::COLUMN_CAT_ID}] = $row;
//         }
//         if($allCategories === false) {
//            $this->catList = $cats;
//         }
         return $dbst->fetchAll();
   }

   /**
    * Metoda uloží novou kategorii
    * @param <type> $name
    * @param <type> $alt
    * @param <type> $keywords
    * @param <type> $description
    * @param <type> $urlkey
    * @param <type> $priority
    * @param <type> $inidividualPanels
    * @param <type> $showInMenu
    * @param <type> $showWhenLoginOnly
    * @param <type> $sitemapPriority
    * @param <type> $sitemapFrequency
    */
   public function saveNewCategory($name, $alt, $module, $keywords, $description, $urlkey,
       $priority, $inidividualPanels, $showInMenu, $showWhenLoginOnly, $sitemapPriority,
       $sitemapFrequency, $defRight, $feeds, $dataDir = null) {

      $this->setIUValues(array(self::COLUMN_CAT_LABEL => $name,
          self::COLUMN_CAT_ALT => $alt, self::COLUMN_INDIVIDUAL_PANELS => $inidividualPanels,
          self::COLUMN_MODULE => $module, self::COLUMN_KEYWORDS => $keywords,
          self::COLUMN_DESCRIPTION => $description, self::COLUMN_URLKEY => $urlkey,
          self::COLUMN_PRIORITY => $priority, self::COLUMN_CAT_SHOW_IN_MENU => $showInMenu,
          self::COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY => $showWhenLoginOnly,
          self::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY => $sitemapPriority,
          self::COLUMN_CAT_SITEMAP_CHANGE_FREQ => $sitemapFrequency,
          self::COLUMN_DEF_RIGHT => $defRight, self::COLUMN_FEEDS => $feeds,
          self::COLUMN_DATADIR => $dataDir));

      $dbc = new Db_PDO();
//      print ("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
//          ." ".$this->getInsertLabels()." VALUES ".$this->getInsertValues());

      $dbc->exec("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
          ." ".$this->getInsertLabels()." VALUES ".$this->getInsertValues());

      return $dbc->lastInsertId();
   }

   /**
    * Metoda uloží novou kategorii
    * @param <type> $name
    * @param <type> $alt
    * @param <type> $keywords
    * @param <type> $description
    * @param <type> $urlkey
    * @param <type> $priority
    * @param <type> $inidividualPanels
    * @param <type> $showInMenu
    * @param <type> $showWhenLoginOnly
    * @param <type> $sitemapPriority
    * @param <type> $sitemapFrequency
    */
   public function saveEditCategory($id, $name, $alt, $module, $keywords, $description, $urlkey,
       $priority, $inidividualPanels, $showInMenu, $showWhenLoginOnly, $sitemapPriority,
       $sitemapFrequency, $defRight, $feeds, $dataDir = null, $icon = null) {

      $this->setIUValues(array(self::COLUMN_CAT_LABEL => $name,
          self::COLUMN_CAT_ALT => $alt,self::COLUMN_INDIVIDUAL_PANELS => $inidividualPanels,
          self::COLUMN_MODULE => $module, self::COLUMN_KEYWORDS => $keywords,
          self::COLUMN_DESCRIPTION => $description, self::COLUMN_URLKEY => $urlkey,
          self::COLUMN_PRIORITY => $priority, self::COLUMN_CAT_SHOW_IN_MENU => $showInMenu,
          self::COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY => $showWhenLoginOnly,
          self::COLUMN_CAT_SITEMAP_CHANGE_PRIORITY => $sitemapPriority,
          self::COLUMN_CAT_SITEMAP_CHANGE_FREQ => $sitemapFrequency,
          self::COLUMN_DEF_RIGHT => $defRight, self::COLUMN_FEEDS => $feeds,
          self::COLUMN_DATADIR => $dataDir, self::COLUMN_ICON => $icon));

      $dbc = new Db_PDO();
      return $dbc->exec("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET ".$this->getUpdateValues()
          ." WHERE ".self::COLUMN_CAT_ID." = ".$dbc->quote($id));
   }

   public function deleteCategory($id) {
      $dbc = new Db_PDO();
      $st = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          . " WHERE ".self::COLUMN_CAT_ID." = :id");
      return $st->execute(array(':id' => $id));
   }
   
   /**
    * Metoda nastaví změnu kategorie
    * @param int $id -- id kategorie
    */
   public static function setLastChange($idCategory) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET `".self::COLUMN_CHANGED."` = NOW()"
          ." WHERE (".self::COLUMN_CAT_ID." = :idcat)");
      $dbst->bindParam(':idcat', $idCategory, PDO::PARAM_INT);
      return $dbst->execute();
   }

   public function search($string){
      $dbc = new Db_PDO();
      $clabel = self::COLUMN_CAT_LABEL.'_'.Locale::getLang();
      $ctext = self::COLUMN_DESCRIPTION.'_'.Locale::getLang();

      $dbst = $dbc->prepare('SELECT *, ('.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER+1).' * MATCH(cat.'.$clabel.') AGAINST (:sstring)'
              .' + MATCH(cat.'.$ctext.') AGAINST (:sstring)) as '.Search::COLUMN_RELEVATION
              .' FROM '.Db_PDO::table(self::DB_TABLE).' as cat'
              ." LEFT JOIN ".Model_Rights::getRightsTable()." AS rights ON rights.".Model_Rights::COLUMN_ID_CATEGORY." = cat.".self::COLUMN_CAT_ID
              .' WHERE MATCH(cat.'.$clabel.', cat.'.$ctext.') AGAINST (:sstring IN BOOLEAN MODE)'
//              .' AND (rights.'.Model_Rights::COLUMN_ID_GROUP.' = :idgrp AND rights.'.Model_Rights::COLUMN_RIGHT.' LIKE :rightstr)'
              .' AND rights.'.Model_Rights::COLUMN_RIGHT.' LIKE :rightstr'
              .' GROUP BY cat.'.self::COLUMN_CAT_ID
              .' ORDER BY '.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER+1)
              .' * MATCH(cat.'.$clabel.') AGAINST (:sstring) + MATCH(cat.'.$ctext.') AGAINST (:sstring) DESC');

      $dbst->bindValue(':sstring', $string, PDO::PARAM_STR);
      $dbst->bindValue(":idgrp", AppCore::getAuth()->getGroupId(), PDO::PARAM_INT);
      $dbst->bindValue(":rightstr", "r__", PDO::PARAM_STR);
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda uloží parametry kategorie
    * @param int $catId -- id kategorie
    * @param string $params -- serializované pole s parametry
    */
   public function saveCatParams($catId, $params){
      // pokud je pole serializujeme
      if(is_array($params)) $params = serialize($params);
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET ".self::COLUMN_PARAMS." = :params WHERE ".self::COLUMN_CAT_ID." = :idcat");
      $dbst->bindValue(':params', $params, PDO::PARAM_STR);
      $dbst->bindValue(':idcat', $catId, PDO::PARAM_INT);
      return $dbst->execute();
   }

   public function getCategoryListByModule($module){
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS cat"
                    ." WHERE (cat.".Model_Category::COLUMN_MODULE." = :module)");
      $dbst->bindValue(":module", $module, PDO::PARAM_STR);
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->execute();
      return $dbst;
   }
}
?>