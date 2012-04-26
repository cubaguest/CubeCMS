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
class Form_Element extends TrObject implements Form_Element_Interface {
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
    * Jestli byl prvek validován
    * @var boolean
    */
   protected $isValidated = false;

   /**
    * Jestli byl prvek filtrován
    * @var boolean
    */
   protected $isFiltered = false;

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
   protected $htmlValidationsLabelsAdded = false;

   protected static $elementFocused = false;
   
   /**
    * Id renderované část -- použito při vytváření id
    * @var integer
    */
   protected $renderedId = 1;

   /**
    * Název css tříd, která se přidávají k elementům
    * @var array
    */
   public static $cssClasses = array('error' => 'form-error',
                                     'validations' => 'form-box-validations',
                                     'langLinkContainer' => 'form-link-lang-container',
                                     'langLink' => 'form-link-lang',
                                     'langLinkSel' => 'form-link-lang-sel',
                                     'elemContainer' => 'form-elem-container');

   /**
    * Konstruktor elemntu
    * @param string/Form_Element $name -- název elemntu
    * @param string $label -- popis elemntu
    */
   public function  __construct($name, $label = null, $prefix = null) {
      if($name instanceof Form_Element){
         $this->formElementName = str_replace($name->formElementPrefix, '', $name->getName());
         $this->formElementLabel = $name->getLabel();
         $this->formElementPrefix = $name->formElementPrefix;
      } else {
         $this->formElementName = $name;
         $this->formElementLabel = $label;
         $this->formElementPrefix = $prefix;
      }
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
      $this->isPopulated = false;
   }

   /**
    * Metoda odebere elemntu pravidlo pro validace
    * @param string $validator -- název validátoru
    */
   final public function removeValidation($validator) {
      unset ($this->validators[$validator]);
      $this->isPopulated = false;
   }

   /**
    * Metoda přidá elemntu filtr, který upraví výstup elementu
    * @param Form_Filter_Interface $filter -- typ filtru
    */
   final public function addFilter(Form_Filter_Interface $filter) {
      $this->filters[get_class($filter)] = $filter;
      // doplnění popisků k validaci
      $filter->addHtmlElementParams($this);
      $this->isPopulated = false;
   }

   /**
    * Metoda nastaví hodnoty do prvku
    * @param mixed $values -- hodnoty
    * @param string $key -- klíč pokud se element chová jako pole
    * @param bool $unfiltered -- jestli se mají hodnoty ukládat do nefiltrovaných (vhodné pro filtry)
    * @return Form_Element
    */
   public function setValues($values, $key = null) {
      $this->isFiltered = false;
      $this->isPopulated = false;
      if($key === null) {
         $this->unfilteredValues = $values;
      } else {
         $this->unfilteredValues[$key] = $values;
      }
      return $this;
   }

   /**
    * Metoda nastaví nefiltrované hodnoty do prvku
    * @param mixed $values -- hodnoty
    * @return Form_Element
    */
   public function setFilteredValues($values) {
      $this->values = $values;
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
         $langs = Locales::getAppLangsNames();
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
    * Metoda vrcí subpopisek prvku
    * @return string
    */
   public function getSubLabel()
   {
      return $this->formElementSubLabel;
   }

   /**
    * Metofda vrací jestli se jedná o vícerozměrný element
    * @param bool $multiple -- true pro nasatvení vícerozměrného elementu
    * @return bool -- true pokud je element vicerozměrný
    */
   public function isDimensional() {
      if($this->dimensional === false) {
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
      if(!$this->isValidated){
         $this->validate();
      } 
      if($this->isValid()){
         $this->filter();
         if($key !== null AND isset($this->values[$key])) {
            return $this->values[$key];
         }
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

   /**
    * Metoda nastavní počáteční id renderu
    * @param int $id
    * @return Form_Element 
    */
   final public function setRenderID($id)
   {
      $this->renderedId = $id;
      return $this;
   }


   /*
    * Metody pro vykreslení
   */

   /**
    * Metoda vrací jestli je element validní
    */
   public function isValid($valid = null) {
      if($valid !== null){
         $this->isValid = $valid;
         $this->isValidated = true;
      } else if(!$this->isValidated){
         $this->validate();
      }
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
      return $this->checkEmpty($this->getUnfilteredValues());
   }

   public function setError($msg) {
      $this->isValid = false;
      $this->errMsg()->addMessage($msg);
   }

   /**
    * Metoda vrací jestli byl element vůbec odeslán
    * @return bool
    */
   public function isSend() {
      return isset ($_REQUEST[$this->getName()]);
   }

   /*
    * Interní metody
   */

   /**
    * metoda zkorntroluje jestli je prvek prázdný
    * @param array/string $array -- pole nebo řetězec
    * @return boolean -- true pro prázdný prvek
    * @todo Je to k něčemu???
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

   protected function createValidationLabels(){
      if(!$this->htmlValidationsLabelsAdded) {
         $this->htmlValidationsLabelsAdded = true;
         // doplnění popisků k validaci
         foreach ($this->validators as $validator) {
            $validator->addHtmlElementParams($this);
         }
      }
   }

   /**
    * Metody pro naplěnní a validaci
    */
   
   /**
    * Metoda naplní element
    */
   public function populate() {
      if(isset ($_POST[$this->getName()]) && $_POST[$this->getName()] != "") {
         $this->values = $_POST[$this->getName()];
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
      $this->isValidated = true;
   }

   /**
    * Metodda provede přefiltrování obsahu elementu
    * @param boolean $newFilter -- (option true) jestli se má znovu přefiltrovat
    */
   public function filter($newFilter = false) {
      if($this->isValidated == true && ($this->isFiltered == false OR $newFilter == true) ) {
         $this->values = $this->unfilteredValues;
         foreach ($this->filters as $filter) {
            $filter->filter($this, $this->values);
         }
         $this->isFiltered = true;
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
      $this->createValidationLabels();
      $elem = clone $this->htmlLabel();
      $elem->clearContent();
      if(!$this->isValid AND $this->isPopulated) {
         $elem->addClass(self::$cssClasses['error']);
      }
      if($this->formElementLabel !== null) {
         $elem->addContent($this->formElementLabel.":");
      }
      if($this->isDimensional()) {
         $elem->addClass($this->getName()."_".$this->dimensional."_label_class");
      } else {
         $elem->addClass($this->getName()."_label_class");
      }
      if($this->isMultilang()) {
         $cnt = $langButtons = null;
         foreach ($this->getLangs() as $langKey => $langLabel) {
            if($this->isDimensional()) {
               $elem->setAttrib('for', $this->getName().'_'.$this->renderedId."_".$this->dimensional.'_'.$langKey);
               $elem->setAttrib('id', $this->getName()."_".$this->dimensional.'_label_'.$langKey);
               $elem->setAttrib('lang', $langKey);
            } else {
               $elem->setAttrib('for', $this->getName().'_'.$this->renderedId.'_'.$langKey);
               $elem->setAttrib('id', $this->getName().'_label_'.$langKey);
               $elem->setAttrib('lang', $langKey);
            }
            $cnt .= $elem;
         }
         return $cnt;
      } else {
         if(!$this->isDimensional()) {
            $elem->setAttrib('for', $this->getName().'_'.$this->renderedId);
         } else {
            $elem->setAttrib('for', $this->getName().'_'.$this->renderedId."_".$this->dimensional);
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
      $this->createValidationLabels();
      $this->html()->clearContent();
      if(!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass(self::$cssClasses['error']);
         if(!self::$elementFocused){ $this->html()->setAttrib('autofocus','autofocus'); self::$elementFocused = true;}
      }
      $values = $this->getUnfilteredValues();
      $this->html()->addClass($this->getName()."_class");
      if($this->isMultiLang()) {
         $cnt = null;
         $first = true;
         foreach ($this->getLangs() as $langKey => $langLabel) {
            $container = new Html_Element('p', $this->html());
            if($this->isDimensional()) {
               $this->html()->setAttrib('name', $this->getName().'['.$this->dimensional.']['.$langKey.']');
               $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId."_".$this->dimensional.'_'.$langKey);
               $this->html()->setAttrib('value', htmlspecialchars($values[$this->dimensional][$langKey]));
               $container->setAttrib('id', $this->getName().'_'.$this->renderedId."_".$this->dimensional.'_container_'.$langKey);
            } else {
               $this->html()->setAttrib('name', $this->getName().'['.$langKey.']');
               $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId.'_'.$langKey);
               $this->html()->setAttrib('value', htmlspecialchars($values[$langKey]));
               $container->setAttrib('id', $this->getName().'_container_'.$langKey);
            }
            $this->html()->setAttrib('lang', $langKey);
            $container->addClass(self::$cssClasses['elemContainer']);
            $container->setAttrib('lang', $langKey);
            $cnt .= $container;
         }
         return $cnt;
      } else {
         if($this->isDimensional()) {
            $this->html()->setAttrib('name', $this->getName()."[".$this->dimensional."]");
            $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId."_".$this->dimensional);
            if(is_array($values) AND isset($values[$this->dimensional])) {
               $this->html()->setAttrib('value', htmlspecialchars((string)$values[$this->dimensional]));
            } else {
               $this->html()->setAttrib('value', htmlspecialchars((string)$values));
            }
         } else {
            $this->html()->setAttrib('name', $this->getName());
            $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId);
            $this->html()->setAttrib('value', htmlspecialchars((string)$values));
         }
         $this->renderedId++;
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
      $this->createValidationLabels();
      if(!empty($this->htmlValidationsLabels)) {
         $labels = "(";
         foreach ($this->htmlValidationsLabels as $lab) {
            $labels .= $lab.", ";
         }
         $labels = substr($labels, 0, strlen($labels)-2).")";
         $this->htmlValidLabel()->addContent($labels);
         $this->htmlValidLabel()->addClass(self::$cssClasses['validations']);
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
//            $a = new Html_Element('a', new Html_Element('span', $langLabel));
            $a = new Html_Element('a', $langLabel);
            $a->setAttrib('href', "#");
            $a->addClass(self::$cssClasses['langLink']);
            if($this->isDimensional()) {
               $a->setAttrib('id', $this->getName()."_".$this->dimensional."_lang_link_".$langKey);
            } else {
               $a->setAttrib('id', $this->getName()."_lang_link_".$langKey);
            }
            $a->setAttrib('lang', $langKey);
//            $a->setAttrib('onclick', "return formElemSwitchLang(this,'".$langKey."');");
            $a->setAttrib('title', $langLabel);
            $a->setAttrib('lang', $langKey);
            $langButtons .= $a;
         }
         $container = new Html_Element('p', $langButtons);
         return $container->addClass(self::$cssClasses['langLinkContainer']);
      }
      return null;
   }

   /**
    * Metoda pro generování scriptů. potřebných pro práci s formulářem
    */
   public function scripts() {
      return null;
   }

   public function  __toString() {
      return (string)$this->controll();
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
