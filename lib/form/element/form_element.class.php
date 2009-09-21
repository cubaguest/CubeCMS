<?php
/**
 * Třída elementu formuláře
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: form_element.class.php 630 2009-06-14 15:52:19Z jakub $ VVE 5.1.0 $Revision: 630 $
 * @author        $Author: jakub $ $Date: 2009-06-14 15:52:19 +0000 (Sun, 14 Jun 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-06-14 15:52:19 +0000 (Sun, 14 Jun 2009) $
 * @abstract      Třída pro obsluhu formulářů
 */
class Form_Element implements Form_Element_Interface {
   /**
    * Název elementu
    * @var string
    */
   protected $formElementName = null;

   /**
    * Prefix elementu
    * @var string
    */
    protected $formElementPrefix = null;

   /**
    * Popisek elementu
    * @var string
    */
   protected $formElementLabel = null;

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
    * Objekt Html elementu pro daný prvek
    * @var Html_Element
    */
   protected $htmlElement = null;

   /**
    * Objekt Html elementu pro daný popisek prvku
    * @var Html_Element
    */
   protected $htmlElementLabel = null;

   /**
    * Konstruktor elemntu
    * @param string $name -- název elemntu
    * @param string $label -- popis elemntu
    */
   public function  __construct($name, $label = null, $prefix = null) {
      $this->formElementName = $name;
      $this->formElementLabel = $label;
      $this->formElementPrefix = $prefix;
      $this->init();
   }

   /**
    * Metoda pro inicializaci
    */
   protected function init() {
      $this->htmlElement = new Html_Element('input');
      $this->htmlElementLabel = new Html_Element('label');
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
      return $this->formElementPrefix.$this->formElementName;
   }

   /**
    * Metoda vrací popis prvku
    * @return string -- popis prvku, je zadáván při vytvoření
    */
   final public function getLabel() {
      return $this->formElementLabel;
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
      $this->formElementPrefix = $prefix.$this->formElementPrefix;
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

   /**
    * Metoda vrací popisek k prvku (html element label)
    * @return string
    */
   public function label() {
      $this->htmlLabel()->addContent($this->formElementLabel);
      $this->htmlLabel()->setAttrib('for', $this->getName());
      if(!$this->isValid AND $this->isPopulated){
         $this->htmlLabel()->addClass('formErrorLabel');
      }
      return (string)$this->htmlLabel();
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      return null;
   }

   /**
    * Metoda vyrenderuje celý element i s popiskem
    * @param string $type -- typ renderu (table,null,...)
    */
   public function render($type = "table") {
      $string = null;
      switch ($type) {
         case 'table':
         default:
            $tr = new Html_Element('tr');
            $td1 = new Html_Element('td');
            $td1->addContent($this->label());
            $tr->addContent($td1);
            $td2 = new Html_Element('td');
            $td2->addContent($this->controll());
            $tr->addContent($td2);
            $string = $tr;
            break;
      }
      return (string)$string;
   }

   public function  __toString() {
      return (string)$this->render();
   }

   /**
    * Metoda vrací objekt Html elementu, vhodné pro úpravu vlastností elementu
    * @return Html_Element
    */
   public function html() {
      return $this->htmlElement;
   }

   /**
    * Metoda vrací objekt Html elementu, vhodné pro úpravu vlastností elementu
    * @return Html_Element
    */
   public function htmlLabel() {
      return $this->htmlElementLabel;
   }
}
?>
