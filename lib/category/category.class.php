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
    * Objekt kategorie
    * @var Object
    */
   private $category = null;

   /**
    * Proměná obsahuje jestli je vybraná kategorie jako hlavní kategorie
    * @var boolean
    */
   private $categoryIsDefault = false;

   /**
    * Objekt hlavní kategorie
    * @var Category
    */
   private static $selectedCategory = null;

   /**
    * Objekt s právy kategorie
    * @var Rights
    */
   private $categoryRights = null;

   /**
    * Objekt modulu
    * @var Module
    */
   private $module = null;

   /**
    * Konstruktor načte informace o kategorii
    * @string/int $catKey --  klíč nebo id kategorie
    * @bool $isMainCategory --  (option) jest-li se jedná o hlavní kategorii
    */
   public function  __construct($catKey = null, $isSelectedCategory = false, $categoryDataObj = null) {
   //		vbrání kategorie
      if($catKey != null) {
         $this->loadCat($catKey, $categoryDataObj);
         $this->categoryIsDefault = false;
      } else {
         $this->loadCat(null, $categoryDataObj);
         $this->categoryIsDefault = true;
      }
      if(!empty ($this->category)) {
         $this->categoryRights = new Rights();
         $this->loadRights();
      }
      if($isSelectedCategory) {
         self::$selectedCategory = $this;
      }
   }

   /**
    * metoda načte vybranou kategorii z databáze
    * @param string $catKey -- klíč kategorie
    */
   private function loadCat($catKey = null, $catDataObj = null) {
   // zjistíme jestli nemáme načtená data
      if($catDataObj === null) {
         $catModel = new Model_Category();
         if(!is_numeric($catKey)){
            $catArray = $catModel->getCategory($catKey);
         } else {
            $catArray = $catModel->getCategoryById($catKey);
         }
      } else {
         $catArray = $catDataObj;
      }
      //		Pokud nebyla načtena žádná kategorie
      if(empty($catArray)) {
      //         AppCore::setErrorPage();
         $this->category = false;
         return false;
      } else {
         $this->category = $catArray;
      }
      // vytvoření objektu Modulu
      $this->module = new Module((string)$this->category->{Model_Category::COLUMN_MODULE},
          $this->parseParams($this->category->{Model_Category::COLUMN_PARAMS}));
   }

   /**
    * Metoda načte práva ke kategorii
    */
   public function loadRights() {
      // admin může vše
      if(Auth::getUserName() == 'admin'){
         $this->categoryRights->addRight('rwc');
      } else {
         $this->categoryRights->addRight($this->getCatDataObj()->{Model_Rights::COLUMN_RIGHT});
      }
   }

   /**
    * Metoda vrací true pokud vybraná kategorie je výchozí kategorií
    * @return boolean -- true pokud je výchozí kategorie
    */
   public function isDefault() {
      return $this->categoryIsDefault;
   }

   /**
    * Metoda parsuje parametry kategorie a uloží je do pole
    * @param string -- řetězec s paramaetry
    */
   private function parseParams($params) {
      $rParams = array();
      if ($params != null) {
         $arrayValues = array();
         $arrayValues = explode(self::CAT_PARAMS_SEPARATOR, $params);
         foreach ($arrayValues as $value) {
            $tmpArrayValue = explode('=', $value);
            $rParams[$tmpArrayValue[0]]=$tmpArrayValue[1];
         }
      }
      return $rParams;
   }

   /**
    * Metoda vrací název kategorie alias pro getName
    * @return string -- název kategorie
    * @deprecated -- použít getName
    */
   public function getLabel() {
      return $this->getName();
   }

   /**
    * Metoda vrací název kategorie
    * @return string -- název kategorie
    */
   public function getName() {
      return (string)$this->category->{Model_Category::COLUMN_CAT_LABEL};
   }

   /**
    * Metoda vrací id kategorie
    * @return integer -- id kategorie
    */
   public function getId() {
      return (int)$this->category->{Model_Category::COLUMN_CAT_ID};
   }

   /**
    * Metoda vrací id sekce kategorie
    * @return integer -- id sekce kategorie
    */
   public function getSectionId() {
      return (int)$this->category->{Model_Sections::COLUMN_SEC_ID};
   }

   /**
    * Metoda vrací název sekce kategorie
    * @return string -- název sekce kategorie
    */
   public function getSectionLabel() {
      return (string)$this->category->{Model_Sections::COLUMN_SEC_LABEL};
   }

   /**
    * Metoda vrací urlkey kategorie
    * @return string -- urlkey kategorie
    */
   public function getUrlKey() {
      if(isset ($this->category->{Model_Category::COLUMN_URLKEY})) {
         return (string)$this->category->{Model_Category::COLUMN_URLKEY};
      } else {
         return null;
      }
   }

   /**
    * Metoda vrací jesli jsou pro danou kategorii individuální panely
    * @return boolena -- true pokud jsou panely individuální
    */
   public function isIndividualPanels() {
      return $this->category->{Model_Category::COLUMN_INDIVIDUAL_PANELS};
   }

   /**
    * Metoda vrací jestli se jedná o validní kategorii
    * @return bool
    */
   public function isValid() {
      if(empty ($this->category)) {
         return false;
      }
      return true;
   }

   /**
    * Metoda vrací práva ke kategorii
    * @return Rights
    */
   public function getRights() {
      return $this->categoryRights;
   }

   /**
    * Metoda vrací objekt hlavní kategorie
    * @return Category
    */
   public static function getSelectedCategory() {
      return self::$selectedCategory;
   }

   /**
    * Metoda vrací objekt modulu pro zadanou kategorii
    * @return Module
    */
   public function getModule() {
      return $this->module;
   }

   /**
    * Metoda vrací objekt data kategorie (nejčastěji načtené přes model)
    * @return Object
    */
   public function getCatDataObj() {
      return $this->category;
   }
}
?>