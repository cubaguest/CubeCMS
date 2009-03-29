<?php
/**
 * Třída obsluhuje práci se zvolenou kategorií.
 * Třída umožňuje základní přístu k vlastnostem kategorie a volbu jejího
 * obsahu podle práv uživatele. Načítá také kategorii, která je výchozí nebo zvolená.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu zvolené kategorie
 * @todo          Odstranit názvy sloupců, mají být v modelu kategorie
 */

class Category {
   /**
    * Odělovač parametrů v pramaterech kategorie
    * @var string
    */
   const CAT_PARAMS_SEPARATOR = ';';

   /**
    * Názvy sloupců v db tabulce
    * @var string
    */
   const COLUMN_CAT_LABEL 	= 'clabel';
   const COLUMN_SEC_LABEL 	= 'slabel';
   const COLUMN_CAT_ID		= 'id_category';
   const COLUMN_SEC_ID		= 'id_section';
   const COLUMN_CAT_URLKEY	= 'urlkey';
   const COLUMN_CAT_LPANEL	= 'left_panel';
   const COLUMN_CAT_RPANEL	= 'right_panel';
   const COLUMN_CAT_PARAMS	= 'cparams';
   const COLUMN_CAT_SHOW_IN_MENU	= 'show_in_menu';
   const COLUMN_CAT_PROTECTED	= 'protected';

   const COLUMN_CAT_LABEL_ORIG = 'label';
   const COLUMN_CAT_ALT_ORIG = 'alt';

   const COLUMN_CAT_ACTIVE = 'active';

   /**
    * Id aktuální kategorie
    * @var integer
    */
   private static $currentCategoryId = null;

   /**
    * Proměná s konektorem pro připojení db
    * @var Db
    */
   private static $_dbConnector = null;

   /**
    * proměná a autorizací přístupu
    * @var Auth
    */
   private static $_auth = null;

   /**
    * Proměné s informacemi o kategorii
    * @var string
    */
   private static $_categoryLabel = null;

   /**
    * Id kategorie
    * @var integer
    */
   private static $_categoryId = null;

   /**
    * Id Sekce
    * @var int
    */
   private static $_sectionId = null;

   /**
    * Proměné jesli jsou zapnuty panely
    * @var boolean
    */
   private static $_categoryLeftPanel = false;
   private static $_categoryRightPanel = false;

   /**
    * názvem sekce
    * @var string
    */
   private static $_sectionName = null;

   /**
    * Pole s parametry kategorie
    * @var array
    */
   private static $_categoryParams = array();

   /**
    * Proměná obsahuje jestli je vybraná kategorie jako hlavní kategorie
    * @var boolean
    */
   private static $_categoryIsDefault = false;

   /**
    * Konstruktor načte informace o kategorii
    * @param Db object -- konektor k databázi
    */
   public static function factory(Auth $auth) {
      //		nastavení db
      self::$_dbConnector = AppCore::getDbConnector();
      self::$_auth = $auth;

      //		vbrání kategorie
      if(self::$currentCategoryId != null){
         self::_loadSelectedFromDb(self::$currentCategoryId);
      } else {
         self::_loadDefaultFromDb();
      }
   }

   /**
    * Nastavuje id aktuální kategorie
    * @param integer $id -- id aktuální kategorie
    */
   public static function setCurrentCategoryId($id) {
      self::$currentCategoryId = $id;
   }

   /**
    * Metoda vrací část url adresy s kategorií
    * @return array -- pole s částmi pro URL
    */
   public static function getCurrentCategory() {
      $array = array(Links::LINK_ARRAY_ITEM_ID => self::$currentCategoryId,
         Links::LINK_ARRAY_ITEM_NAME => self::$currentCategoryName);
      return $array;
   }

   /**
    * metoda načte vybranou kategorii z databáze
    * @param integer $catId -- id kategorie
    */
   private static function _loadSelectedFromDb($catId) {
      $userNameGroup = self::$_auth->getGroupName();
      $catModel = new CategoryModel();
      $catArray = $catModel->getCategory(self::$currentCategoryId);
      //		Pokud nebyla načtena žádná kategorie
      if(empty($catArray)){
         AppCore::setErrorPage();
         return false;
      } else {
         self::$_categoryLabel = $catArray->{CategoryModel::COLUMN_CAT_LABEL};
         self::$_categoryId = $catArray->{CategoryModel::COLUMN_CAT_ID};
         self::$_sectionId = $catArray->{CategoryModel::COLUMN_SEC_ID};
         self::$_categoryLeftPanel = $catArray->{CategoryModel::COLUMN_CAT_LPANEL};
         self::$_categoryRightPanel = $catArray->{CategoryModel::COLUMN_CAT_RPANEL};
         self::$_sectionName = $catArray->{CategoryModel::COLUMN_SEC_LABEL};
         self::parseParams($catArray->{CategoryModel::COLUMN_CAT_PARAMS});
         //        načtení výchozí kategorie
         $defCatArr = self::getDefaultCategory();
         if($defCatArr[CategoryModel::COLUMN_CAT_ID] == self::$_categoryId){
            self::$_categoryIsDefault = true;
         }
      }
   }

   /**
    * metoda načte výchozí kategorii z databáze
    */
   private static function _loadDefaultFromDb() {
      $catModel = new CategoryModel();
      $catArray = $catModel->getCategory();
      try {
         if(empty ($catArray)){
            throw new RangeException(_('Nepodařilo se načíst výchozí kategorii. Chyba v konfiguraci.'),1);
         }
      } catch (RangeException $e) {
         new CoreErrors($e);
      }
      self::$_categoryLabel = $catArray->{CategoryModel::COLUMN_CAT_LABEL};
      self::$_categoryId = $catArray->{CategoryModel::COLUMN_CAT_ID};
      self::$_sectionId = $catArray->{CategoryModel::COLUMN_SEC_ID};
      self::$_categoryLeftPanel = $catArray->{CategoryModel::COLUMN_CAT_LPANEL};
      self::$_categoryRightPanel = $catArray->{CategoryModel::COLUMN_CAT_RPANEL};
      self::$_sectionName = $catArray->{CategoryModel::COLUMN_SEC_LABEL};
      self::parseParams($catArray->{CategoryModel::COLUMN_CAT_PARAMS});
      self::$_categoryIsDefault = true;
   }

   /**
    * Metoda načte informace o výchozí kategorii
    * @return array -- pole s prvky výchozí kategorie
    */
   public static function getDefaultCategory() {
      $catModel = new CategoryModel();
      try {
         $catArray = $catModel->getCategory();
         if(empty($catArray)){
            throw new RangeException(_('Nepodařilo se načíst výchozí kategorii. Chyba v konfiguraci.'),2);
         }
      } catch (RangeException $e) {
         new CoreErrors($e);
      }
      //		Pokud nebyla načtena žádná kategorie
      $catArr = array ();
      $catArr[self::COLUMN_CAT_LABEL] = $catArray->{CategoryModel::COLUMN_CAT_LABEL};
      $catArr[self::COLUMN_CAT_ID] = $catArray->{CategoryModel::COLUMN_CAT_ID};
      $catArr[self::COLUMN_SEC_ID] = $catArray->{CategoryModel::COLUMN_SEC_ID};
      $catArr[self::COLUMN_CAT_LPANEL] = $catArray->{CategoryModel::COLUMN_CAT_LPANEL};
      $catArr[self::COLUMN_CAT_RPANEL] = $catArray->{CategoryModel::COLUMN_CAT_RPANEL};
      $catArr[self::COLUMN_SEC_LABEL] = $catArray->{CategoryModel::COLUMN_SEC_LABEL};
      $catArr[self::COLUMN_CAT_PARAMS] = self::parseParams($catArray->{CategoryModel::COLUMN_CAT_PARAMS});
      return $catArr;
   }

   /**
    * Metoda vrací true pokud vybraná kategorie je výchozí kategorií
    * @return boolean -- true pokud je výchozí kategorie
    */
   public static function isDefault() {
      return self::$_categoryIsDefault;
   }

   /**
    * Metoda parsuje parametry kategorie a uloží je do pole
    * @param string -- řetězec s paramaetry
    */
   private static function parseParams($params){
      if ($params != null){
         $arrayValues = array();
         $arrayValues = explode(self::CAT_PARAMS_SEPARATOR, $params);
         //			print_r($arrayValues);

         foreach ($arrayValues as $value) {
            $tmpArrayValue = explode("=", $value);
            self::$_categoryParams[$tmpArrayValue[0]]=$tmpArrayValue[1];
         }
      }
   }

   /**
    * Metoda vrací název kategorie
    * @return string -- název kategorie
    */
   public static function getLabel() {
      return self::$_categoryLabel;
   }

   /**
    * Metoda vrací id kategorie
    * @return integer -- id kategorie
    */
   public static function getId() {
      return (int)self::$_categoryId;
   }

   /**
    * Metoda vrací id sekce kategorie
    * @return integer -- id sekce kategorie
    */
   public static function getSectionId() {
      return self::$_sectionId;
   }

   /**
    * Metoda vrací název sekce kategorie
    * @return string -- název sekce kategorie
    */
   public static function getSectionLabel() {
      return self::$_sectionName;
   }

   /**
    * Metoda vrací urlkey kategorie
    * @return string -- urlkey kategorie
    */
   public static function getUrlKey() {
      return self::$_categoryUrlkey;
   }

   /**
    * Metoda vrací jesli je zapnut levý panel
    * @return boolena -- true pokud je panel zapnut
    */
   public static function isLeftPanel(){
      return self::$_categoryLeftPanel;
   }

   /**
    * Metoda vrací jesli je zapnut pravý panel
    * @return boolena -- true pokud je panel zapnut
    */
   public static function isRightPanel(){
      return self::$_categoryRightPanel;
   }

   /**
    * Metoda vrací požedovaný parametr
    * @param string -- index parametru
    * @return string -- parametr
    */
   public static function getParam($param) {
      if(isset(self::$_categoryParams[$param])){
         return self::$_categoryParams[$param];
      } else {
         return null;
      }
   }
}
?>