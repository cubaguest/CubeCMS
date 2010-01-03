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
    * Subpopisek elementu
    * @var string
    */
   protected $formElementSubLabel = null;

   /**
    * Pole s validátory
    * @var array
    */
   protected $validators = array();

   /**
    * Pole s filtry aplikovanými na element
    * @var array
    */
   protected $filters = array();

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
   protected $dimensional = false;

   /**
    * Pole s odeslanými hodnotami
    * @var mixed
    */
   protected $values = null;

   /**
    * Pole s nefiltrovanými hodnotami
    * @var array
    */
   protected $unfilteredValues = null;

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
    * Objekt Html elementu pro daný subpopisek prvku
    * @var Html_Element
    */
   protected $htmlElementSubLabel = null;

   /**
    * Objekt s popiskem validací
    * @var Html_Element
    */
   protected $htmlElementValidaionLabel = null;

   /**
    * Pole s texty k validacím
    * @var array
    */
   protected $htmlValidationsLabels = array();

   /**
    * Konstruktor elemntu
    * @param string $name -- název elemntu
    * @param string $label -- popis elemntu
    */
   public function  __construct($name, $label = null, $prefix = null) {
      $this->formElementName = $name;
      $this->formElementLabel = $label;
      $this->formElementPrefix = $prefix;
      $this->initHtmlElements();
      $this->init();
   }

   /**
    * Metoda pro inicializaci
    */
   protected function init() {
   }

   /**
    * Metoda pro inicializaci html elementu
    */
   protected function initHtmlElements() {
      $this->htmlElement = new Html_Element('input');
      $this->htmlElementLabel = new Html_Element('label');
      $this->htmlElementValidaionLabel = new Html_Element('p');
      $this->htmlElementSubLabel = new Html_Element('p');
   }

   /*
    * Metody pro práci s parametry elementu
    */

   /**
    * Metoda přidá elemntu pravidlo pro validace
    * @param Form_Validator_Interface $validator -- typ validace
    */
   final public function addValidation(Form_Validator_Interface $validator) {
      $this->validators[get_class($validator)] = $validator;
      // doplnění popisků k validaci
      $validator->addHtmlElementParams($this);
   //      array_push($this->validators, $validator);
   }
   
   /**
    * Metoda přidá elemntu filtr, který upraví výstup elementu
    * @param Form_Filter_Interface $filter -- typ filtru
    */
   final public function addFilter(Form_Filter_Interface $filter) {
      $this->filters[get_class($filter)] = $filter;
      // doplnění popisků k validaci
      $filter->addHtmlElementParams($this);
   //      array_push($this->validators, $validator);
   }

   /**
    * Metoda nastaví hodnoty do prvku
    * @param mixed $values -- hodnoty
    * @return Form_Element
    */
   public function setValues($values) {
      $this->values = $this->unfilteredValues = $values;
      return $this;
   }

   /**
    * Metoda nastaví nefiltrované hodnoty do prvku
    * @param mixed $values -- hodnoty
    * @return Form_Element
    */
   public function setUnfilteredValues($values) {
      $this->unfilteredValues = $values;
      return $this;
   }

   /**
    * Metoda nastaví jestli je prvek vícerozměrný
    * @param string $name -- (option) název prvku pro pole
    * @@return Form_Element
    */
   public function setDimensional($name = null) {
      $this->dimensional = $name;
      return $this;
   }

   /**
    * Metoda přidá popisek k validaci
    * @param string $text -- popisek
    */
   public function addValidationConditionLabel($text) {
      array_push($this->htmlValidationsLabels, $text);
   }

   /**
    * Metoda nastaví že se jedná o jazykový prvek
    * @param array $langs -- (option) pole jazyků, pokud není zadáno jsou použity
    * interní jazyky aplikace
    * @return Form_Element -- vrací samo sebe
    */
   public function setLangs($langs = null) {
      if($langs == null) {
         $langs = Locale::getAppLangsNames();
      }
      $this->isMultilang = true;
      $this->langs = $langs;
      return $this;
   }

   /**
    * Metoda nastaví subpopisek k elementu
    * @param string $string -- subpopisek
    * @return Form_Element
    */
   public function setSubLabel($string) {
      $this->formElementSubLabel = $string;
      return $this;
   }

   /**
    * Metofda vrací jestli se jedná o vícerozměrný element
    * @param bool $multiple -- true pro nasatvení vícerozměrného elementu
    * @return bool -- true pokud je element vicerozměrný
    */
   public function isDimensional() {
      if($this->dimensional === false){
         return false;
      }
      return true;
   }

   /**
    * Metofda vrací jestli se jedná o vícerozměrný element
    * @return bool -- true pokud je element vicerozměrný
    */
   public function isMultiLang() {
      return $this->isMultilang;
   }

   /**
    * Metofda vrací pole s jazyky prvku
    * @return array -- pole s jazyky
    */
   public function getLangs() {
      return $this->langs;
   }

   /**
    * Metoda vrací název elementu
    * @return string
    */
   final public function getName() {
      return $this->formElementPrefix.$this->formElementName;
   }

   /**
    * Metoda nasatví popis elementu
    * @param string $label -- popis elementu
    */
   final public function setLabel($label) {
      $this->formElementLabel = $label;
   }

   /**
    * Metoda vrací popis prvku
    * @return string -- popis prvku, je zadáván při vytvoření
    */
   final public function getLabel() {
      return $this->formElementLabel;
   }

   /**
    * Metoda vrací hodnotu prvku
    * @param $key -- (option) klíč hodnoty (pokud je pole)
    * @return mixed -- hodnota prvku
    */
   public function getValues($key = null) {
      if($key !== null AND isset($this->values[$key])) {
         return $this->values[$key];
      }
      return $this->values;
   }

   /**
    * Metoda vrací nefiltrované hodnoty prvku
    * @return mixed -- hodnota prvku
    */
   public function getUnfilteredValues($key = null) {
      if($key !== null AND isset($this->unfilteredValues[$key])) {
         return $this->unfilteredValues[$key];
      }
      return $this->unfilteredValues;
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

   /**
    * Metoda vrací jestli je element prázdný
    */
   public function isEmpty() {
//      if($this->isMultilang OR $this->isDimensional()) {
         return $this->checkEmpty($this->getValues(null, false));
//      } else {
//         if($this->getValues() == null OR $this->getValues() == '') {
//            return true;
//         }
//         return false;
//      }
   }

   /*
    * Interní metody
    */

    /**
     * metoda zkorntroluje jestli je prvek prázdný
     * @param array/string $array -- pole nebo řetězec
     * @return boolean -- true pro prázdný prvek
     */
   private function checkEmpty($array) {
      if(is_array($array)) {
         foreach ($array as $var) {
            if(!$this->checkEmpty($var)) {
               return false;
            }
         }
      } else if($array != null AND $array != '') {
         return false;
      }
      return true;
   }

   /**
    * Metoda naplní element
    */
   public function populate() {
      if(isset ($_REQUEST[$this->getName()])) {
         $this->values = $_REQUEST[$this->getName()];
      } else {
         $this->values = null;
      }
      $this->unfilteredValues = $this->values;
      $this->isPopulated = true;
   }

   /**
    * Metoda provede validace
    */
   public function validate() {
   // validace prvku
      foreach ($this->validators as $validator) {
         if(!$validator->validate($this)) {
            $this->isValid = false;
            break;
         }
      }
   }

   /**
    * Metodda provede přefiltrování obsahu elementu
    */
   public function filter(){
      $this->values = $this->unfilteredValues;
      foreach ($this->filters as $filter) {
         $filter->filter($this);
      }
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
      $elem = clone $this->htmlLabel();
      $elem->clearContent();
//      $this->htmlLabel()->clearClasses();
      if(!$this->isValid AND $this->isPopulated) {
         $elem->addClass('formErrorLabel');
      }
      if($this->formElementLabel !== null){
         $elem->addContent($this->formElementLabel.":");
      }
      if($this->isDimensional()){
         $elem->addClass($this->getName()."_".$this->dimensional."_label_class");
      } else {
         $elem->addClass($this->getName()."_label_class");
      }
      if($this->isMultilang()) {
         $cnt = $langButtons = null;
         foreach ($this->getLangs() as $langKey => $langLabel) {
            if($this->isDimensional()){
               $elem->setAttrib('for', $this->getName()."_".$this->dimensional.'_'.$langKey);
               $elem->setAttrib('id', $this->getName()."_".$this->dimensional.'_label_'.$langKey);
               $elem->setAttrib('lang', $langKey);
            } else {
               $elem->setAttrib('for', $this->getName().'_'.$langKey);
               $elem->setAttrib('id', $this->getName().'_label_'.$langKey);
               $elem->setAttrib('lang', $langKey);
            }

            $cnt .= $elem;
         }
         return $cnt;
      } else {
         if(!$this->isDimensional()){
            $elem->setAttrib('for', $this->getName());
         } else {
            $elem->setAttrib('for', $this->getName()."_".$this->dimensional);
         }
      }

      return (string)$elem;
   }

   /**
    * Metoda vrací subpopisek
    * @return string -- řetězec z html elementu
    */
   public function subLabel() {
      $this->htmlSubLabel()->addContent($this->formElementSubLabel);
      return $this->htmlSubLabel();
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      $this->html()->clearContent();
//      $this->html()->clearClasses();
      if(!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass('formError');
      }

      $values = $this->getUnfilteredValues();
//      var_dump($values);
      $this->html()->addClass($this->getName()."_class");

      if($this->isMultiLang()) {
         $cnt = null;
         $first = true;
         foreach ($this->getLangs() as $langKey => $langLabel) {
            $container = new Html_Element('p', $this->html());
            if($this->isDimensional()){
               $this->html()->setAttrib('name', $this->getName().'['.$this->dimensional.']['.$langKey.']');
               $this->html()->setAttrib('id', $this->getName()."_".$this->dimensional.'_'.$langKey);
               $this->html()->setAttrib('value', htmlspecialchars($values[$this->dimensional][$langKey]));
               $container->setAttrib('id', $this->getName()."_".$this->dimensional.'_container_'.$langKey);
            } else {
               $this->html()->setAttrib('name', $this->getName().'['.$langKey.']');
               $this->html()->setAttrib('id', $this->getName().'_'.$langKey);
               $this->html()->setAttrib('value', htmlspecialchars($values[$langKey]));
               $container->setAttrib('id', $this->getName().'_container_'.$langKey);
            }

            $this->html()->setAttrib('lang', $langKey);
            $container->addClass("elem_container_class");
            $container->setAttrib('lang', $langKey);


            $cnt .= $container;
         }
         return $cnt;
      } else {
//         $container = new Html_Element('p');
         if($this->isDimensional()){
            $this->html()->setAttrib('name', $this->getName()."[".$this->dimensional."]");
            $this->html()->setAttrib('id', $this->getName()."_".$this->dimensional);
            if(is_array($values)){
               $this->html()->setAttrib('value', htmlspecialchars((string)$values[$this->dimensional]));
            } else {
               $this->html()->setAttrib('value', htmlspecialchars((string)$values));
            }
         } else {
            $this->html()->setAttrib('name', $this->getName());
            $this->html()->setAttrib('id', $this->getName());
            $this->html()->setAttrib('value', htmlspecialchars((string)$values));
         }
         //         $this->html()->setAttrib('type', 'text');
         
//         $container->addContent($this->html());
//         return $container;
         return $this->html();
      }
   }

   /**
    * Metoda vrací všechny prvky, keré patří ke kontrolu, tj controll, labelValidations, subLabel
    * @return string -- včechny prvky vyrenderované
    */
   public function controllAll() {
      $ret = $this->controll();
      $ret .= $this->labelValidations();
      $ret .= $this->subLabel();
      return $ret;
   }

   /**
    * Metoda vrací popisek k validacím
    * @return string
    */
   public function labelValidations() {
      if(!empty($this->htmlValidationsLabels)) {
      //         $this->htmlValidLabel()->addContent(new Html_Element('br'));
         $labels = "(";
         foreach ($this->htmlValidationsLabels as $lab) {
            $labels .= $lab.", ";
         }
         $labels = substr($labels, 0, strlen($labels)-2).")";

         $this->htmlValidLabel()->addContent($labels);
         $this->htmlValidLabel()->addClass('formValidationLabel');
         return $this->htmlValidLabel();
      }
      return null;
   }

   /**
    * Funkce vygeneruje přepínač pro volbu jazyku
    * @return string
    */
   public function labelLangs() {
      if($this->isMultilang() AND count($this->langs) > 1) {
         $langButtons = null;
         foreach ($this->getLangs() as $langKey => $langLabel) {
            $a = new Html_Element('a', $langLabel);
            $a->setAttrib('href', "#");
            $a->addClass("formLinkLang");
            if($this->isDimensional()){
               $a->setAttrib('id', $this->getName()."_".$this->dimensional."_lang_link_".$langKey);
            } else {
               $a->setAttrib('id', $this->getName()."_lang_link_".$langKey);
            }
            $a->addClass("elem_lang_link");
            $a->setAttrib('onclick', "return formElemSwitchLang(this,'".$langKey."');");
            $a->setAttrib('title', $langLabel);
            $a->setAttrib('lang', $langKey);
            $langButtons .= $a;
         }
         return $langButtons.(new Html_Element('br'));
      }
      return null;
   }

   /**
    * Metoda pro generování scriptů. potřebných pro práci s formulářem
    */
   public function scripts() {
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
            $td1 = new Html_Element('th');
            $td1->setAttrib('allign', 'right');
            $td1->setAttrib('width', 100);
            $td1->addContent($this->label());
            $tr->addContent($td1);
            // kontrolní element
            $td2 = new Html_Element('td');
            $td2->addContent($this->labelLangs());
            $td2->addContent($this->controll());
            $td2->addContent($this->scripts());
            // popisky k validátorům
            $td2->addContent($this->labelValidations());
            $tr->addContent($td2);
            $string = $tr;
            break;
      }
      return (string)$string;
   }

   /**
    * Metoda upraví vlastnost prvku u vykreslení
    * @param string $type -- typ parametru, který se má upravit
    * @param mixed $value -- hodnota parametru
    * @todo -- asij nebude třeba
    */
   public function setRender($type, $size = 30) {
      switch ($type) {
         case 'size':
            $this->html()->setAttrib('size', $size);
            break;
         default:
            break;
      }
   }

   public function  __toString() {
      return (string)$this->render();
   }

   /**
    * Metoda vrací objekt Html elementu, vhodné pro úpravu vlastností elementu.
    * Element kontrolního prvku formuláře (např. input)
    * @return Html_Element
    */
   public function html() {
      return $this->htmlElement;
   }

   /**
    * Metoda vrací objekt Html elementu, vhodné pro úpravu vlastností elementu
    * Element popisku formuláře (label)
    * @return Html_Element
    */
   public function htmlLabel() {
      return $this->htmlElementLabel;
   }

   /**
    * Metoda vrací objekt Html elementu, vhodné pro úpravu vlastností elementu
    * Element popisku pro validace
    * @return Html_Element
    */
   public function htmlValidLabel() {
      return $this->htmlElementValidaionLabel;
   }

   /**
    * Metoda vrací objekt Html elementu, vhodné pro úpravu vlastností elementu
    * Element subpopisku
    * @return Html_Element
    */
   public function htmlSubLabel() {
      return $this->htmlElementSubLabel;
   }
}
?>
