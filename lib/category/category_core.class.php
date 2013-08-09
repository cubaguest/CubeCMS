<?php

/**
 * Třída obsluhuje práci se zvolenou kategorií, její hlavní reprezentace.
 * Třída umožňuje základní přístu k vlastnostem kategorie a volbu jejího
 * obsahu podle práv uživatele. Načítá také kategorii, která je výchozí nebo zvolená.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: category.class.php 1390 2010-08-09 06:40:37Z jakub $ VVE3.9.4 $Revision: 1390 $
 * @author        $Author: jakub $ $Date: 2010-08-09 08:40:37 +0200 (Po, 09 srp 2010) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2010-08-09 08:40:37 +0200 (Po, 09 srp 2010) $
 * @abstract 		Třída pro obsluhu zvolené kategorie
 */
class Category_Core extends TrObject {
   /**
    * Název adresáře s ikonou kategorie
    */
   const DIR_IMAGES = 'categories';
   const DIR_ICON = 'icons';
   const DIR_IMAGE = 'icons';
   const DIR_BACKGROUND = 'backgrounds';


   /**
    * Odělovač parametrů v pramaterech kategorie
    * @var string
    */
   const CAT_PARAMS_SEPARATOR = ';';

   /**
    * Objekt kategorie
    * @var Object
    */
   protected $category = null;
   /**
    * Proměná obsahuje jestli je vybraná kategorie jako hlavní kategorie
    * @var boolean
    */
   protected $categoryIsDefault = false;
   /**
    * Objekt hlavní kategorie
    * @var Category
    */
   protected static $selectedCategory = null;
   /**
    * Objekt s právy kategorie
    * @var Rights
    */
   protected $categoryRights = null;
   /**
    * Objekt modulu
    * @var Module
    */
   protected $module = null;
   /**
    * Pole s parametry kategorie
    * @var array
    */
   protected $catParams = array();

   /**
    * Konstruktor načte informace o kategorii
    * @string/int $catKey --  klíč nebo id kategorie
    * @bool $isMainCategory --  (option) jest-li se jedná o hlavní kategorii
    */
   public function __construct($catKey = null, $isSelectedCategory = false, $categoryDataObj = null)
   {
      $this->category = $categoryDataObj;
      if ($isSelectedCategory) {
         self::$selectedCategory = $this;
      }
      $this->createModuleObject();
      $this->categoryRights = new Rights();
      $this->loadRights();
   }

   protected function createModuleObject()
   {
      $mClass = ucfirst((string)$this->getDataObj()->{Model_Category::COLUMN_MODULE}).'_Module';
      if(!class_exists($mClass)){
         $mClass = 'Module';
      }

      if($this->getDataObj()->{Model_Category::COLUMN_PARAMS} != null){
         $this->catParams = array_merge($this->catParams, unserialize($this->getDataObj()->{Model_Category::COLUMN_PARAMS}));
      }
      if($this->getDataObj()->{Model_Category::COLUMN_PARAMS_OLD} != null){
         $this->catParams = array_merge($this->catParams, $this->parseParams($this->getDataObj()->{Model_Category::COLUMN_PARAMS_OLD}));
      }

      $this->module = new $mClass(
         (string)$this->getDataObj()->{Model_Category::COLUMN_MODULE},
         $this->catParams,
         $this->getDataObj()->{Model_Module::COLUMN_VERSION}
      );

      // pokud je zadána vlastní složka pro data
      if($this->getDataObj()->{Model_Category::COLUMN_DATADIR} != null){
         $this->module->setDataDir($this->getDataObj()->{Model_Category::COLUMN_DATADIR});
      }
   }

   /**
    * Metoda načte práva ke kategorii
    */
   public function loadRights()
   {
      // admin může vše
      if (Auth::isAdmin()) {
         $this->categoryRights->addRight('rwc');
      } else {
         $this->categoryRights->addRight('r--');
      }
   }

   /**
    * Metoda vrací true pokud vybraná kategorie je výchozí kategorií
    * @return boolean -- true pokud je výchozí kategorie
    */
   public function isDefault()
   {
      return $this->categoryIsDefault;
   }
   
   /**
    * Metoda vrací požadovaný parametr
    * @param string $param -- index parametru
    * @param mixed $defaultParam -- výchozí hodnota
    * @return string -- parametr
    */
   public function getParam($param, $defaultParam = null)
   {
      if (isset($this->catParams[$param])) {
         return $this->catParams[$param];
      } else {
         return $defaultParam;
      }
   }

   /**
    * Metoda vrací požadovaný parametr
    * @param string $param -- index parametru
    * @param mixed $defaultParam -- výchozí hodnota
    * @return string -- parametr
    */
   public function setParam($param, $value)
   {
      $this->catParams[$param] = $value;
      $catModel = new Model_Category();
      $catModel->saveCatParams($this->getId(), serialize($this->catParams));
   }

   /**
    * Metoda vrací jestli kategorie má feed export
    * @return bool
    */
   public function haveFeed()
   {
      return false;
   }

   /**
    * Metoda vrací název kategorie alias pro getName
    * @return string -- název kategorie
    * @deprecated -- použít getName
    */
   public function getLabel()
   {
      return $this->getName();
   }

   /**
    * Metoda vrací název kategorie
    * @return string -- název kategorie
    */
   public function getName($tryAlt = false)
   {
      if($tryAlt && (string) $this->category->{Model_Category::COLUMN_ALT} != null){
         return (string) $this->category->{Model_Category::COLUMN_ALT};
      }
      return (string) $this->category->{Model_Category::COLUMN_NAME};
   }

   /**
    * Metoda vrací id kategorie
    * @return integer -- id kategorie
    */
   public function getId()
   {
      return (int) $this->category->{Model_Category::COLUMN_ID};
   }
   
   /**
    * Metoda vrací id vlastníka
    * @return integer -- id vlastníka
    */
   public function getIdOwner()
   {
      return (int) $this->category->{Model_Category::COLUMN_ID_USER_OWNER};
   }

   /**
    * Metoda vrací urlkey kategorie
    * @return string -- urlkey kategorie
    */
   public function getUrlKey()
   {
      if (isset($this->category->{Model_Category::COLUMN_URLKEY})) {
         return (string) $this->category->{Model_Category::COLUMN_URLKEY};
      } else {
         return null;
      }
   }

   /**
    * Metoda vrací jesli jsou pro danou kategorii individuální panely
    * @return boolena -- true pokud jsou panely individuální
    */
   public function isIndividualPanels()
   {
      return (bool) $this->category->{Model_Category::COLUMN_INDIVIDUAL_PANELS};
   }

   /**
    * Metoda vrací jestli se jedná o validní kategorii
    * @return bool
    */
   public function isValid()
   {
      if (empty($this->category)) {
         return false;
      }
      return true;
   }

   /**
    * Metoda vrací práva ke kategorii
    * @return Rights
    */
   public function getRights()
   {
      return $this->categoryRights;
   }

   /**
    * Metoda vrací objekt hlavní kategorie
    * @return Category
    */
   public static function getSelectedCategory()
   {
      return self::$selectedCategory;
   }

   /**
    * Metoda vrací objekt modulu pro zadanou kategorii
    * @return Module
    */
   public function getModule()
   {
      return $this->module;
   }

   /**
    * Metoda vrací objekt data kategorie (nejčastěji načtené přes model)
    * @return Object
    * @deprecated -- používat getDataObj 
    */
   public function getCatDataObj()
   {
      return $this->getDataObj();
   }
   /*
    * Metoda vrací datový objek kategorie
    * @return Model_ORM_Record
    */
   public function getDataObj()
   {
      return $this->category;
   }

   /**
    * Metoda vrací adresář s ikonami kategorií
    * @return string
    */
   public static function getCatIconDir()
   {
      return self::getImageDir(self::DIR_ICON, false);
   }

   /**
    * Metoda vrátí cestu k obrázkům kategorie
    * @param string $type -- typ obrázku (const: DIR_ICONS|DIR_BACKGROUNDS)
    * @param bool $realpath -- jestli má být vrácena reálná cesta nebo url adresa
    * @return string -- adresář
    */
   public static function getImageDir($type = self::DIR_ICON, $realpath = false)
   {
      if ($realpath) {
         return AppCore::getAppDataDir() . self::DIR_IMAGES . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
      } else {
         return Url_Request::getBaseWebDir() . VVE_DATA_DIR . URL_SEPARATOR . self::DIR_IMAGES . URL_SEPARATOR . $type . URL_SEPARATOR;
      }
   }

}
?>