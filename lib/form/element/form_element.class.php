<?php
/**
 * Interface elementu formuláře
 * Interface definující základní vlastnosti elemntu formuláře
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: form.class.php 630 2009-06-14 15:52:19Z jakub $ VVE 5.1.0 $Revision: 630 $
 * @author        $Author: jakub $ $Date: 2009-06-14 15:52:19 +0000 (Sun, 14 Jun 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-06-14 15:52:19 +0000 (Sun, 14 Jun 2009) $
 * @abstract      Třída pro obsluhu formulářů
 */
class Form_Element extends Html_Element implements Form_Element_Interface {
   /**
    * Název elementu
    * @var string
    */
   protected $elementName = null;

   /**
    * Prefix elementu
    * @var string
    */
    protected $elementPrefix = null;

   /**
    * Popisek elementu
    * @var string
    */
   protected $elementLabel = null;

   /**
    * Pole s validátory
    * @var array
    */
   protected $validators = array();

   /**
    * Jestli je prvek validní
    * @var boolean
    */
   protected $isValid = true;

   /**
    * Jestli byl prvek naplněn
    * @var boolean
    */
   protected $isPopulated = false;

   /**
    * Jestli se jedná o vícejazyčný prvek
    * @var boolean
    */
   protected $isMultilang = false;

   /**
    * Jestli se jedná o vícerozměrný prvek
    * @var boolean
    */
   protected $isMultiple = false;

   /**
    * Pole s odeslanými hodnotami
    * @var mixed
    */
   protected $values = null;

   /**
    * Pole s jazyky prvku
    * @var array
    */
   protected $langs = array();

   /**
    * Konstruktor elemntu
    * @param string $name -- název elemntu
    * @param string $label -- popis elemntu
    */
   public function  __construct($name = null, $label = null, $prefix = null) {
      $this->elementName = $name;
      $this->elementLabel = $label;
      $this->elementPrefix = $prefix;
   }

   /*
    * Metody pro práci s parametry elementu
    */

   /**
    * Metoda přidá elemntu pravidlo pro validace
    * @param Form_Validator_Interface $validator -- typ validace
    */
   final public function addValidation(Form_Validator_Interface $validator) {
//      // pokud jsou předány argumenty
//      $params = array();
//      if(func_num_args() > 1){
//         for ($i = 1 ; $i < func_num_args() ; $i++) {
//            $params[$i] = func_get_arg($i);
//         }
//      }
//      $this->validators[$name] = $params;
      array_push($this->validators, $validator);
   }

   /**
    * Metoda nastaví jestli je prvek vícerozměrný
    * @param array $names -- (option) názvy jednotlivých prvků, jinak jsou
    * použity čísla
    */
   public function setMultiple($names = null) {
      ;
   }

   /**
    * Metoda nastaví že se jedná o jazykový prvek
    * @param array $langs -- (option) pole jazyků, pokud není zadáno jsou použity
    * interní jazyky aplikace
    */
   public function setLangs($langs = null) {
      ;
   }

   /**
    * Metofda vrací jestli se jedná o vícerozměrný element
    * @param bool $multiple -- true pro nasatvení vícerozměrného elementu
    * @return bool -- true pokud je element vicerozměrný
    */
   public function multiple($multiple = null) {
      if($multiple !== null){

      }
      return $this->isMultiple;
   }

   /**
    * Metofda vrací jestli se jedná o vícerozměrný element
    * @param array $multiLang -- true nebo pole s jazyky
    * @return bool -- true pokud je element vicerozměrný
    */
   public function multiLang($multiLang = null) {
      if($multiLang !== null){
         $this->isMultilang = true;
      }
      return $this->isMultilang;
   }


   /**
    * Metoda vrací název elementu
    * @return string
    */
   final public function getName() {
      return $this->elementName;
   }

   /**
    * Metoda vrací hodnoty elementu
    * @return mixed (string/array)
    */
   public function getValues() {
      return $this->values;
   }

   /**
    * Metoda nastaví prefix elementu
    * @param string $prefix -- prefix elementu ve formuláři
    */
   final public function setPrefix($prefix) {
      $this->elementPrefix = $prefix;
   }

   /*
    * Metody pro vykreslení
    */

   /**
    * Metoda vrací jestli je element validní
    */
   public function isValid() {
      return $this->isValid;
   }

   /**
    * Metoda vrací jestli je prvek naplněn
    */
   public function isPopulated() {
      return $this->isPopulated;
   }

   /*
    * Interní metody
    */

   /**
    * Metoda naplní element
    * @param string $method -- typ metody přes kterou je prvek odeslán (POST|GET)
    */
   public function populate($method = 'post'){
   }

   /**
    * Metoda vrací objekt s chybovými zprávami
    * @return Messages -- objekt zpráv
    */
   final protected function errMsg() {
      return AppCore::getUserErrors();
   }
}
?>
