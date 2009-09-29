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
   protected $isDimensional = false;

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
    * Metoda nastaví hodnoty do prvku
    * @param mixed $values -- hodnoty
    */
   public function setValues($values) {
      $this->values = $values;
   }

   /**
    * Metoda nastaví jestli je prvek vícerozměrný
    * @param array $names -- (option) názvy jednotlivých prvků, jinak jsou
    * použity čísla
    */
   public function setDimensional($names = null) {
      ;
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
    * Metofda vrací jestli se jedná o vícerozměrný element
    * @param bool $multiple -- true pro nasatvení vícerozměrného elementu
    * @return bool -- true pokud je element vicerozměrný
    */
   public function isDimensional() {
      return $this->isDimensional;
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
    * Metoda vrací popis prvku
    * @return string -- popis prvku, je zadáván při vytvoření
    */
   final public function getLabel() {
      return $this->formElementLabel;
   }

   /**
    * Metoda vrací hodnotu prvku
    * @retrun mixed -- hodnota prvku
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
   public function populate($method = 'post') {
      switch ($method) {
         case 'get':
            if(isset ($_GET[$this->getName()])) {
               $this->values = $_GET[$this->getName()];
            } else {
               $this->values = null;
            }
            break;
         default:
            if(isset ($_POST[$this->getName()])) {
               $this->values = $_POST[$this->getName()];
            } else {
               $this->values = null;
            }
            break;
      }
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

      if(!$this->isValid AND $this->isPopulated) {
         $this->htmlLabel()->addClass('formErrorLabel');
      }

      $this->htmlLabel()->addContent($this->formElementLabel);
      if($this->isMultilang()) {
//         Template::addJS('./jscripts/formswitchlangs.js');

         $cnt = $langButtons = null;
         foreach ($this->getLangs() as $langKey => $langLabel) {
            $this->htmlLabel()->setAttrib('for', $this->getName().'['.$langKey.']');
            $this->htmlLabel()->setAttrib('id', "label_".$this->getName().'['.$langKey.']');

//            $a = new Html_Element('a', $langLabel);
//            $a->setAttrib('href', "#");
//            $a->setAttrib('title', $langLabel);
//            $a->setAttrib('onclick', "return formSwitchLang('".$this->getName()."[".$langKey."]');");
//            $langButtons .= $a.(new Html_Element('br'));

            $cnt .= $this->htmlLabel();
         }
         return $cnt;
      } else {
         $this->htmlLabel()->setAttrib('for', $this->getName());
      }

      return (string)$this->htmlLabel();
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      if(!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass('formError');
      }

      if($this->isMultiLang()) {
         $cnt = null;
         foreach ($this->getLangs() as $langKey => $langLabel) {
            $this->html()->setAttrib('name', $this->getName().'['.$langKey.']');
            $this->html()->setAttrib('id', $this->getName().'['.$langKey.']');
            $cnt .= $this->html();
         }

         // script pro vybrání jazyka -- TODO předělat
         $script = new Html_Element('script');
         $script->setAttrib('type', "text/javascript");
         $script->addContent('formSwitchLang("'.$this->getName()."[".Locale::getLang().']");');


         return $cnt.$script;
      } else {
      // tady bude if při multilang
         $this->html()->setAttrib('name', $this->getName());
         //         $this->html()->setAttrib('type', 'text');
         $this->html()->setAttrib('id', $this->getName());
         $this->html()->setAttrib('value', $this->getValues());
      }
      return $this->html();
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
            // popisky k validátorům
            $td2->addContent($this->labelValidations());
            $tr->addContent($td2);
            $string = $tr;
            break;
      }
      return (string)$string;
   }

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
    * @return <type>
    */
   public function labelLangs() {
      if($this->isMultilang() AND count($this->langs) > 1) {
         Template::addJS('./jscripts/formswitchlangs.js');

         $langButtons = null;
         foreach ($this->getLangs() as $langKey => $langLabel) {
            $a = new Html_Element('a', $langLabel);
            $a->setAttrib('href', "#");
            $a->setAttrib('title', $langLabel);
            $a->setAttrib('onclick', "return formSwitchLang('".$this->getName()."[".$langKey."]');");
//            $langButtons .= $a.(new Html_Element('br'));
            $langButtons .= $a;
         }
         return $langButtons.(new Html_Element('br'));
      }
      return null;
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
}
?>
