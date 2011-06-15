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
 */

class Category extends Category_Core {
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
      $this->categoryRights = new Rights();
      if($this->isValid()) {
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
         if(is_int($catKey)){
            $catModel->setGroupPermissions()->where('AND '.Model_Category::COLUMN_ID .' = :id', array('id' => (int)$catKey), true);
         } else if($catKey != null) {
            $catModel->setGroupPermissions()->where('AND '.Model_Category::COLUMN_URLKEY.' = :urlkey', array('urlkey' => $catKey), true);
         } else {
            $catModel->setGroupPermissions()->order(array(Model_Category::COLUMN_PRIORITY => Model_ORM::ORDER_DESC));
         }
         $this->category = $catModel->record();
      } else {
         $this->category = $catDataObj;
      }
      //		Pokud nebyla načtena žádná kategorie
      if($this->category == null) {
//         $this->isValid = false;
         return;
      }
      // vytvoření objektu Modulu
      if($this->category->{Model_Category::COLUMN_PARAMS} != null){
         $this->catParams = array_merge($this->catParams, unserialize($this->category->{Model_Category::COLUMN_PARAMS}));
      }
      if($this->category->{Model_Category::COLUMN_PARAMS_OLD} != null){
         $this->catParams = array_merge($this->catParams, $this->parseParams($this->category->{Model_Category::COLUMN_PARAMS_OLD}));
      }
      $this->module = new Module((string)$this->category->{Model_Category::COLUMN_MODULE},$this->catParams);
      // pokud je zadána vlastní složka pro data
      if($this->category->{Model_Category::COLUMN_DATADIR} != null){
         $this->module->setDataDir($this->category->{Model_Category::COLUMN_DATADIR});
      } else {
         $datadir = $this->category[Model_Category::COLUMN_URLKEY][Locales::getDefaultLang()];
         $last = strrpos($datadir,URL_SEPARATOR);
         if($last !== false){
            $datadir = substr($datadir,$last+1);
         }
         $this->module->setDataDir($datadir);
      }
   }

   /**
    * Metoda načte práva ke kategorii
    */
   public function loadRights() {
      // admin může vše
      if(Auth::isAdmin()){
         $this->categoryRights->addRight('rwc');
      } else {
         $this->categoryRights->addRight($this->getCatDataObj()->{Model_Rights::COLUMN_RIGHT});
      }
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
            if(strpos($value, '=') !== false){
               $tmpArrayValue = explode('=', $value);
               $rParams[$tmpArrayValue[0]]=$tmpArrayValue[1];
            } else {
               array_push($rParams, $value);
            }
         }
      }
      return $rParams;
   }

   /**
    * Metoda vrací požadovaný parametr
    * @param string $param -- index parametru
    * @param mixed $defaultParam -- výchozí hodnota
    * @return string -- parametr
    */
   public function getParam($param, $defaultParam = null) {
      if(isset($this->catParams[$param])){
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
   public function setParam($param, $value) {
      $this->catParams[$param] = $value;
      $catModel = new Model_Category();
      $catModel->saveCatParams($this->getId(), serialize($this->catParams));
   }

   /**
    * Metoda vrací jesli jsou pro danou kategorii individuální panely
    * @return boolena -- true pokud jsou panely individuální
    */
   public function isIndividualPanels() {
      return (bool)$this->category->{Model_Category::COLUMN_INDIVIDUAL_PANELS};
   }

   /**
    * Metoda vrací jestli kategorie má feed export
    * @return bool
    */
   public function haveFeed() {
      return $this->getCatDataObj()->{Model_Category::COLUMN_FEEDS};
   }
}
?>