<?php

/**
 * Třída Modelu pro práci s kategoriemi.
 * Třída, která umožňuje pracovet s modelem kategorií
 *
 * @copyright     Copyright (c) 2008-2009 Jakub Matas
 * @version       $Id$ VVE3.9.2 $Revision$
 * @author        $Author$ $Date$
 *                   $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro vytvoření modelu pro práci s kategoriemi
 * @todo          nutný refaktoring
 */
class Model_Category extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'categories';
   protected $tableName = 'categories';

   /**
    * Názvy sloupců v db tabulce
    * @var string
    */
   const COLUMN_ID = 'id_category';
   const COLUMN_ID_USER_OWNER = 'id_owner_user';
//   const COLUMN_ID_GROUP = 'id_group';
   const COLUMN_NAME = 'label';
   const COLUMN_ALT = 'alt';
   const COLUMN_URLKEY = 'urlkey';
   const COLUMN_DISABLE = 'disable';
   const COLUMN_MODULE = 'module';
   const COLUMN_DATADIR = 'data_dir';
   const COLUMN_INDIVIDUAL_PANELS = 'individual_panels';
   const COLUMN_VISIBILITY = 'visibility';
   const COLUMN_SHOW_WHEN_LOGIN_ONLY = 'show_when_login_only';
   const COLUMN_PARAMS = 'ser_params';
   const COLUMN_PARAMS_OLD = 'params';
   const COLUMN_PRIORITY = 'priority';
   const COLUMN_DEF_RIGHT = 'default_right';
   const COLUMN_ACTIVE = 'active';
   const COLUMN_KEYWORDS = 'keywords';
   const COLUMN_DESCRIPTION = 'description';
   const COLUMN_CREATED = 'created';
   const COLUMN_CHANGED = 'changed';
   const COLUMN_FEEDS = 'feeds';
   const COLUMN_IMAGE = 'icon';
   const COLUMN_ICON = self::COLUMN_IMAGE;
   const COLUMN_BACKGROUND = 'background';
   const COLUMN_ALLOW_HANDLE_ACC = 'allow_handle_access';

   const COLUMN_SITEMAP_CHANGE_FREQ = 'sitemap_changefreq';
   const COLUMN_SITEMAP_CHANGE_PRIORITY = 'sitemap_priority';

   const COLUMN_CAT_LABEL = 'label'; // @deprecated
   const COLUMN_CAT_ALT = 'alt'; // @deprecated
   const COLUMN_CAT_ID = 'id_category'; // @deprecated
   const COLUMN_CAT_ID_PARENT = 'id_parent'; // @deprecated
   const COLUMN_CAT_SHOW_IN_MENU = 'show_in_menu'; // @deprecated
   const COLUMN_CAT_PROTECTED = 'protected'; // @deprecated
   const COLUMN_CAT_SHOW_WHEN_LOGIN_ONLY = 'show_when_login_only'; // @deprecated
   const COLUMN_CAT_SITEMAP_CHANGE_FREQ = 'sitemap_changefreq'; // @deprecated
   const COLUMN_CAT_SITEMAP_CHANGE_PRIORITY = 'sitemap_priority'; // @deprecated

   /**
    * Typy hodnot pro sloupec visibility (jak má být kategorie zobrazena)
    */
   const VISIBILITY_ALL = 1;
   const VISIBILITY_WHEN_LOGIN = 2;
   const VISIBILITY_WHEN_NOT_LOGIN = 3;
   const VISIBILITY_WHEN_ADMIN = 4;
   const VISIBILITY_HIDDEN = 5;
   const VISIBILITY_WHEN_ADMIN_ALL = 6;


   /**
    * Pole se všemi kategoriemi - cache
    * @var array of Model_Orm_Record
    */
   private static $allCatsRecords = null;

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_cats');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER_OWNER, array('datatype' => 'int', 'nn' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER+1));
      $this->addColumn(self::COLUMN_ALT, array('datatype' => 'varchar(200)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_URLKEY, array('datatype' => 'varchar(100)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DISABLE, array('datatype' => 'tinyint(1)', 'lang' => true, 'pdoparam' => PDO::PARAM_BOOL, 'nn' => true, 'default' => false));
      $this->addColumn(self::COLUMN_MODULE, array('datatype' => 'varchar(30)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DATADIR, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));

      $this->addColumn(self::COLUMN_INDIVIDUAL_PANELS, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_VISIBILITY, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 1));

      $this->addColumn(self::COLUMN_PARAMS, array('datatype' => 'varchar(1000)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_PRIORITY, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      // def_right má být ebnum, bohužel zatím není implementovaný v ORM
      $this->addColumn(self::COLUMN_DEF_RIGHT, array('datatype' => 'varchar(3)', 'pdoparam' => PDO::PARAM_STR, 'default' => 'r--'));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));

      $this->addColumn(self::COLUMN_KEYWORDS, array('datatype' => 'varchar(200)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DESCRIPTION, array('datatype' => 'varchar(500)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));

      $this->addColumn(self::COLUMN_CHANGED, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_CREATED, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));

      $this->addColumn(self::COLUMN_FEEDS, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));

      $this->addColumn(self::COLUMN_ICON, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_BACKGROUND, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));

      // opět enum
      $this->addColumn(self::COLUMN_SITEMAP_CHANGE_FREQ, array('datatype' => 'varchar(10)', 'pdoparam' => PDO::PARAM_STR, 'default' => 'yearly'));
      $this->addColumn(self::COLUMN_SITEMAP_CHANGE_PRIORITY, array('datatype' => 'float', 'pdoparam' => PDO::PARAM_STR, 'default' => 0));

      $this->setPk(self::COLUMN_ID);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Model_Rights', Model_Rights::COLUMN_ID_CATEGORY);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Model_Panel', Model_Panel::COLUMN_ID_CAT);
      $this->addForeignKey(self::COLUMN_MODULE, 'Model_Module', Model_Module::COLUMN_NAME);
   }

   protected function beforeSave(Model_ORM_Record $record, $type = 'I')
   {
      Cache::delete('cats_list_'.Auth::getUserId().'_1'); // allLangs true
      Cache::delete('cats_list_'.Auth::getUserId().'_'); // allLangs false
   } 
   
   /**
    * Metoda přidá práva ke kategorii do dotazu
    * @return Model_Category 
    */
   public function withRights()
   {
      $this->join(Model_Category::COLUMN_CAT_ID, array('t_r' => 'Model_Rights'), null,
                  array(Model_Rights::COLUMN_ID_GROUP, Model_Rights::COLUMN_RIGHT), self::JOIN_LEFT,
                  ' AND t_r.'.Model_Rights::COLUMN_ID_GROUP . ' = :idgrp', array('idgrp' => (int)Auth::getGroupId()));
      return $this;
   }

   /**
    * Metoda přidá informace o modulu kategorie
    * @return Model_Category
    */
   public function withModule()
   {
      $this->joinFK(Model_Category::COLUMN_MODULE);
      return $this;
   }

   /**
    * Metoda načte všechny kategorie
    * @return array of Model_ORM_Records -- pole s objekty
    */
   public function getCategoryList($allLangs = false)
   {
      if(self::$allCatsRecords == null){
         $key = 'cats_list_'.Auth::getUserId().'_'.(string)$allLangs;
         if( ($cats = Cache::get($key)) == false ){
            $this->columns(array(
               Model_Category::COLUMN_NAME, Model_Category::COLUMN_ALT,
               Model_Category::COLUMN_DESCRIPTION, Model_Category::COLUMN_KEYWORDS,
               Model_Category::COLUMN_DEF_RIGHT, Model_Category::COLUMN_ID_USER_OWNER, Model_Category::COLUMN_FEEDS,
               Model_Category::COLUMN_INDIVIDUAL_PANELS, Model_Category::COLUMN_MODULE, Model_Category::COLUMN_URLKEY,
               Model_Category::COLUMN_VISIBILITY, Model_Category::COLUMN_ICON,  Model_Category::COLUMN_BACKGROUND,
               Model_Category::COLUMN_PRIORITY, Model_Category::COLUMN_DISABLE,
               Model_Category::COLUMN_PARAMS, Model_Category::COLUMN_DATADIR,
               Model_Category::COLUMN_CREATED, Model_Category::COLUMN_CHANGED
   //            , 'uk_l' => 'LENGTH( '.self::COLUMN_URLKEY.'_'.Locales::getLang().' )'
            ));
            $this->setSelectAllLangs($allLangs)
               ->withRights()
               ->withModule();
            if(!Auth::isAdmin()){
               $this->where(Model_Category::COLUMN_DISABLE.' = 0', array());
            }
//             ->order(array('LENGTH('.Model_Category::COLUMN_URLKEY.')' => 'DESC')); // filesort
            $cats = $this->records(Model_ORM::FETCH_PKEY_AS_ARR_KEY);
            Cache::set($key, $cats);
         }
         // new length optimization - make filesort    ->order(array('urlkey_len_cs' => 'DESC'));
         self::$allCatsRecords = $cats;
      }
      return self::$allCatsRecords;
   }
   
   public function onlyWithAccess()
   {
      $this->withRights()->where(" ( SUBSTRING(`".Model_Rights::COLUMN_RIGHT."`, 1, 1) = 'r' OR "
         ." ( `".Model_Rights::COLUMN_RIGHT."` IS NULL AND SUBSTRING(`".Model_Category::COLUMN_DEF_RIGHT."`, 1, 1) = 'r' )) ", 
         array(), true);
      if(!Auth::isAdmin()){
         $this->where(' AND '.self::COLUMN_DISABLE.'_'.Locales::getLang().' = 0', array(), true);
      }
      return $this;
   }

   /**
    * Metoda načte všechny kategorie
    * @param bool $allCategories -- jestli mají být vráceny všechny kategorie
    * bez ohledu na práva (POZOR!! bezpečnostní riziko)
    * @return PDOStatement -- objekt s daty
    */
   public function getCategoriesWithIndPanels()
   {
      $dbc = Db_PDO::getInstance();
      $whereMenu = null;

      $dbst = $dbc->prepare("SELECT * FROM " . Db_PDO::table(self::DB_TABLE) . " AS cat"
            . " WHERE " . self::COLUMN_INDIVIDUAL_PANELS . " = 1"
            . " ORDER BY LENGTH(" . self::COLUMN_URLKEY . "_" . Locales::getLang() . ") DESC");

      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Model_LangContainer');

      return $dbst->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Model_LangContainer');
   }

   /**
    * Metoda nastaví změnu kategorie
    * @param int $id -- id kategorie
    */
   public static function setLastChange($idCategory)
   {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("UPDATE " . Db_PDO::table(self::DB_TABLE)
            . " SET `" . self::COLUMN_CHANGED . "` = NOW()"
            . " WHERE (" . self::COLUMN_CAT_ID . " = :idcat)");
      $dbst->bindParam(':idcat', $idCategory, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Metoda nastaví změnu kategorie
    * @param int $id -- id kategorie
    */
   public static function setImage($idc, $image, $col = self::COLUMN_IMAGE)
   {
      $m = new self();

      return $m
         ->where(self::COLUMN_ID." = :id", array('id' => $idc))
         ->update(array($col => $image));
   }

   /**
    * Metoda uloží parametry kategorie
    * @param int $catId -- id kategorie
    * @param string $params -- serializované pole s parametry
    */
   public function saveCatParams($catId, $params)
   {
      // pokud je pole serializujeme
      if (is_array($params)) {
         $params = serialize($params);
      }
      $model = new self();
      return $model->where(self::COLUMN_ID." = :idc", array('idc' => $catId))->update(array(self::COLUMN_PARAMS => $params));
   }

   public static function getCategoryListByModule($module, $onlyWithRights = true)
   {
      $model = new self();

      $whereStr = '1 = 1';
      $whereBind = array();
      if(!Auth::isAdmin()){
         $whereStr .= ' AND '.Model_Category::COLUMN_DISABLE.' = 0';
      }
      
      if(is_array($module)){
         $modBinds = array();
         foreach($module as $key => $mod){
            $modBinds[':mod_'.$key] = $mod;
         }
         $whereStr .= ' AND '.self::COLUMN_MODULE." IN (".implode(',', array_keys($modBinds)).")";
         $whereBind = $modBinds;
      } else {
         $whereStr .= ' AND '.self::COLUMN_MODULE." = :module";
         $whereBind['module'] = $module;
      }
      if($onlyWithRights){
         $model->onlyWithAccess();
         $model->where(" AND ".$whereStr, $whereBind, true);
      } else {
         $model->where($whereStr, $whereBind);
      }
      return $model->records();
   }
}

class Model_Category_Record extends Model_ORM_Record {
   
   protected $params = null;


   public function getParam($name, $default = null)
   {
      if($this->params === null){
         $this->params = false;
         if($this->{Model_Category::COLUMN_PARAMS} != null){
            $this->params = unserialize($this->{Model_Category::COLUMN_PARAMS});
         }
      }
      
      if(isset($this->params[$name]) && $this->params[$name] != null){
         return $this->params[$name];
      }
      return $default;
   }
}