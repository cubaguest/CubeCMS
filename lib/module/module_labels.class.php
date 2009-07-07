<?php
/**
 * Třída pro názvů modulu a příslušných kategorií
 * Třída slouží pro přístup k jednotlivým názvům modulu a kategorie ke které modul patří
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE 5.0.0 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu adresářů modulu
 */

class Module_Labels {
/**
 * Reálný název modulu
 * @var string
 */
   private $name = null;

   /**
    * Název modulu
    * @var string
    */
   private $label = null;

   /**
    * Popis modulu
    * @var string
    */
   private $alt = null;

   /**
    * Název kategorie do které modul patří
    * @var string
    */
   private $categoryLabel = null;

   /**
    * Popis kategorie do které modul patří
    * @var string
    */
   private $categoryAlt = null;


	/*
	 * ============ METODY
	 */


   /**
    * Konstruktor vytvoří základní adresářovou strukturu
    *
    * @param string -- adresář modulu (název)
    * @param string -- datový adresář modulu
    */
   function __construct(stdClass $moduleObject) {
      $this->name = $moduleObject->{Model_Module::COLUMN_NAME};
      if(isset ($moduleObject->{Model_Module::COLUMN_LABEL})){
         $this->label = $moduleObject->{Model_Module::COLUMN_LABEL};
         $this->alt = $moduleObject->{Model_Module::COLUMN_ALT};
      }
      if(isset ($moduleObject->{Model_Category::COLUMN_CAT_LABEL})){
      $this->categoryLabel = $moduleObject->{Model_Category::COLUMN_CAT_LABEL};
      $this->categoryAlt = $moduleObject->{Model_Category::COLUMN_CAT_ALT};
      }
   }

   /**
    * Metoda vrátí systémový název modulu
    * @return string
    */
   public function name() {
      return $this->name;
   }

   /**
    * Metoda vrátí název modulu
    * @return string
    */
   public function label() {
      return $this->label;
   }

   /**
    * Metoda vrátí popis modulu
    * @return string
    */
   public function alt() {
      return $this->alt;
   }

   /**
    * Metoda vrátí název kategorie modulu
    * @return string
    */
   public function categoryLabel() {
      return $this->categoryLabel;
   }

   /**
    * Metoda vrátí popis kategorie modulu
    * @return string
    */
   public function categoryAlt() {
      return $this->categoryAlt;
   }

   /**
    * (MAGIC) Při přímem přístupu na název se vrací název modulu
    * @return string
    */
   public function  __toString() {
      return (string)$this->label();
   }
}

?>
