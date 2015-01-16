<?php
/**
 * Třída pro překlad textů
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id:  $ VVE 6.4 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro překlady
 * @todo          Dodělat načítání řetězců podle md5 hashe kíče
 */

class Translator {
   const TYPE_SINGULAR = 1;
   const TYPE_PLURAL = 2;
   
   const PRIMARY_DOMAIN = '-';

   const LOAD_BOOTH = 0;
   const LOAD_LIB = 1;
   const LOAD_FACE = 2;

   private $load = self::LOAD_BOOTH;

   /**
    * Singulars
    * @var array
    */
   protected $translationsS = array();

   /**
    * Plurals
    * @var array
    */
   protected  $translationsP = array();

   /**
    * Doména pro překlady
    * @var string
    */
   protected  $domain = self::PRIMARY_DOMAIN;

   protected $locale = null;

   protected static $translators = array();

   /**
    * Kontruktor pro vytvoření objektu překladatele
    * @param string $domain -- název modulu. pokud je null je použit překlad jádra
    */
   public function  __construct($domain = self::PRIMARY_DOMAIN)
   {
      $this->domain = strtolower($domain);
      if(!isset (self::$translators[$this->domain])){
         $this->loadTranslations();
         self::$translators[$this->domain] = $this;
      } else {
         $this->translationsS = self::$translators[$this->domain]->getSingulars();
         $this->translationsP = self::$translators[$this->domain]->getPlurals();
      }
   }

   public function tr($str, $count = 0, $replaceCount = true)
   {
      if(is_array($str)){
         // Plural
         $key = $str[0];
         $md5key = md5($key);
         if(isset ($this->translationsP[$key])){
            if(abs($count) == 1){
               $str = ($this->translationsP[$key][0] != '') ? $this->translationsP[$key][0] : $str[0];
            } else if(abs ($count) > 1 AND abs ($count) < 5 OR !isset ($this->translationsP[$key][2])){
               $str = ($this->translationsP[$key][1] != '') ? $this->translationsP[$key][1] : $str[1];
            } else if(isset ($this->translationsP[$key][2])) {
               $str = ($this->translationsP[$key][2] != '') ? $this->translationsP[$key][2] : $str[2];
            } else {
               $str = $str[2];
            } 
         } else if(isset ($this->translationsP[$md5key])){
            if(abs($count) == 1){
               $str = ($this->translationsP[$md5key][0] != '') ? $this->translationsP[$md5key][0] : $str[0];
            } else if(abs ($count) > 1 AND abs ($count) < 5 OR !isset ($this->translationsP[$md5key][2])){
               $str = ($this->translationsP[$md5key][1] != '') ? $this->translationsP[$md5key][1] : $str[1];
            } else if(isset ($this->translationsP[$md5key][2])) {
               $str = ($this->translationsP[$md5key][2] != '') ? $this->translationsP[$md5key][2] : $str[2];
            } else {
               $str = $str[2];
            }
         } else {
            if(abs($count) == 1){
               $str = $str[0];
            } else if(abs ($count) > 1 AND abs ($count) < 5 OR !isset ($str[2])){
               $str = $str[1];
            } else {
               $str = $str[2];
            }
         }
         if($replaceCount) $str = sprintf($str, $count);
         return $str;
      } else {
         // Singular
         $md5key = md5($str);
         if(isset ($this->translationsS[$str]) AND $this->translationsS[$str] != null){
            return $this->translationsS[$str];
         } else if(isset ($this->translationsS[$md5key]) AND $this->translationsS[$md5key] != null){
            return $this->translationsS[$md5key];
         } 
         if($this->domain != '-'){ // zkusit hlavní translator jestli nezná překlad
            return self::$translators['-']->tr($str);
         }
         return $str;
      }
   }

   /**
    *  Metoda načte překlady
    */
   protected function loadTranslations()
   {
      $this->loadFile(AppCore::getAppWebDir().'locale'.DIRECTORY_SEPARATOR, Template::faceDir(true).'locale'.DIRECTORY_SEPARATOR);
      $this->loadFile(AppCore::getAppWebDir().'locale'.DIRECTORY_SEPARATOR, Template::faceDir().'locale'.DIRECTORY_SEPARATOR);
   }

   protected function loadFile($pathBase, $pathFace)
   {
      if($this->locale != null){
         $file = $this->locale.'.php';
      } else {
         $file = Locales::getLangLocale().'.php';
      }
      if(file_exists($pathBase.$file) AND ($this->load == self::LOAD_BOOTH OR $this->load == self::LOAD_LIB)){
         $singular = $plural = array();
         include $pathBase.$file;
         $this->translationsS = array_merge($this->translationsS, $singular);
         $this->translationsP = array_merge($this->translationsP, $plural);
      }
      if(file_exists($pathFace.$file) AND ($this->load == self::LOAD_BOOTH OR $this->load == self::LOAD_FACE)){
         $singular = $plural = array();
         include $pathFace.$file;
         $this->translationsS = array_merge($this->translationsS, $singular);
         $this->translationsP = array_merge($this->translationsP, $plural);
      }
   }

   /**
    * Metoda nastaví locale
    * @param string $locale -- locale (např. cs_CS, sk_SK)
    */
   public function setLocale($locale)
   {
      $this->locale = $locale;
      $this->loadTranslations();
   }

   /**
    * Metoda nastaví které překlady se mají načítat. Výchozí je jak face tak lib
    * @param int $load -- konstanty Translator::LOAD_XXXX
    */
   public function setLoadTarget($load = self::LOAD_BOOTH)
   {
      $this->load = $load;
      $this->translationsS = array();
      $this->translationsP = array();
      $this->loadTranslations();
   }


   public function getSingulars() {
      return $this->translationsS;
   }
   public function getPlurals() {
      return $this->translationsP;
   }
}
