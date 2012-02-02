<?php

/**
 * Třída obsluhuje práci se zvolenou kategorií administrace.
 * Třída umožňuje základní přístu k vlastnostem kategorie a volbu jejího
 * obsahu podle práv uživatele. Načítá také kategorii, která je výchozí nebo zvolená.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: category.class.php 1390 2010-08-09 06:40:37Z jakub $ VVE3.9.4 $Revision: 1390 $
 * @author        $Author: jakub $ $Date: 2010-08-09 08:40:37 +0200 (Po, 09 srp 2010) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2010-08-09 08:40:37 +0200 (Po, 09 srp 2010) $
 * @abstract 		Třída pro obsluhu zvolené kategorie
 */
class Category_Admin extends Category_Core {
   /**
    * Konstruktor načte informace o kategorii
    * @string/int $catKey --  klíč nebo id kategorie
    * @bool $isMainCategory --  (option) jest-li se jedná o hlavní kategorii
    */
   public function __construct($catKey = null, $isSelectedCategory = false, $categoryDataObj = null)
   {
      $this->loadCat($catKey, $categoryDataObj);

      if ($isSelectedCategory) {
         self::$selectedCategory = $this;
      }

      if($categoryDataObj == null){
         $model = new Model_CategoryAdm();
         $categoryDataObj = $model->getCategory($catKey);

      }
      $this->category = $categoryDataObj;
//       Debug::log($this->category);

      $this->module = new Module($this->category->{Model_Category::COLUMN_MODULE}, null);
      $this->module->setDataDir($this->category->{Model_Category::COLUMN_DATADIR});
      $this->categoryRights = new Rights();
      $this->loadRights();

      if($this->category->{Model_Category::COLUMN_PARAMS} != null){
         $this->catParams = array_merge($this->catParams, unserialize($this->category->{Model_Category::COLUMN_PARAMS}));
      }

   }

   private function loadCat(){

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
    * Metoda vrací požadovaný parametr
    * @param string $param -- index parametru
    * @param mixed $defaultParam -- výchozí hodnota
    * @return string -- parametr
    */
   public function setParam($param, $value)
   {
      throw new UnderflowException($this->tr("U administrační aktegorie nelze vložit parametry"));
      return $param;
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
    * Metoda vrací název kategorie
    * @return string -- název kategorie
    */
   public function getName()
   {
      return (string) $this->category->{Model_Category::COLUMN_NAME};
   }

   /**
    * Metoda vrací id kategorie
    * @return integer -- id kategorie
    */
   public function getId()
   {
      return (int) $this->category->{Model_Category::COLUMN_CAT_ID};
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
      return false;
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
    */
   public function getCatDataObj()
   {
      return $this->category;
   }
}
?>