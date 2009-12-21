<?php
/**
 * Třída pro obsluhu formuláře
 * Třída implementující objekt pro obsluhu formuláře. Umožňuje kontrolu
 * odeslání formuláře, správného vyplnění všech prvků. Všechny prvky jsou přístupny
 * přes asociativní pole a lze u nich nastavovat validace, překódování a načítat
 * z nich data.
 *
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id$ VVE 5.1.0 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro obsluhu formulářů
 */
class Form implements ArrayAccess {
   /**
    * Prefix pro všechny prvky ve formuláři
    * @var string
    */
   private $formPrefix = null;

   /**
    * Pole z prvky formuláře
    * @var array
    */
   private $elements = array();

   /**
    * Pole se skupinami elementů
    * @var array
    */
   private $elementsGroups = array();

   /**
    * Proměná obsahuje jestli byl formulář odeslán
    * @var boolean
    */
   private $isSend = false;

   /**
    * Jestli byl formulář naplněn
    * @var boolean
    */
   private $isPopulated = false;

   /**
    * Jestli je formulář validně vyplněn
    * @var boolean
    */
   private $isValid = null;

   /**
    * Pole s portvrzovacími elementy
    * @var array
    */
   private $submitElements = array();

   /**
    * Type metody kterou bude formmulář odeslán
    * @var string
    */
   private $sendMethod = 'post';

   /**
    * Objekt odkazu na akci pro formulář
    * @var Url_Link
    */
   private $formAction = null;

   /**
    * Objekt elementu, do kterého bude formlář vykreslen
    * @var Html_Element
    */
   private $htmlElement = null;

   /**
    * Jestli je formulář vícejazyčný
    * @var boolean
    */
   private $formIsMultilang = false;
   /**
    * Konstruktor vytváří objekt formuláře
    * @param string $prefix -- (option) prefix pro formulářové prvky
    */
   function __construct($prefix = null) {
      $this->formPrefix = $prefix;
      $this->formAction = new Url_Link();
      $this->htmlElement = new Html_Element('form');
      $this->setAction(new Url_Link_Module());
      $this->setSendMethod();
   }

   public function  __toString() {
      return $this->creatString();
   }

   /**
    * Metoda vytvoří řetězec s formulářem, pro použití v šabloně
    * @param Form_Decorator $decorator -- objekt dekorátoru
    * @return string -- formulář jako řetězec
    */
   private function creatString(Form_Decorator $decorator = null) {
      if($decorator == null) {
         $decorator = new Form_Decorator();
      }

      $formContent = null;
      $d = clone $decorator;
      foreach ($this->elementsGroups as $key => $grp) {
         // pokud element není ve skupině
         if(!is_array($grp)) {
            $d->addElement($this->elements[$key]);
         }
         // pokud patří do skupiny
         else {
            $formContent .= $d->render();
            $d = clone $decorator;
            $groupCnt = null;
            foreach ($grp['elements'] as $key2 => $elemName) {
               $d->addElement($this->elements[$key2]);
            }
            $filedset = new Html_Element('fieldset');
            if($grp['label'] != null) {
               $filedset->addContent(new Html_Element('legend', $grp['label']));
            }
            if($grp['text'] != null) {
               $p = new Html_Element('p', $grp['text']);
               $p->addClass('formGroupText');
               $filedset->addContent($p);
            }
            $filedset->addContent($d->render(true));
            $formContent .= $filedset;
            $d = clone $decorator;
         }
      }
      $formContent .= $d->render();
      $this->html()->addContent($formContent);
      $this->html()->addContent($this->scripts());

      return (string)$this->html();
   }

   /**
    * Metoda vykreslí skripty formuláře
    */
   public function scripts() {
      $script = null;
      if($this->formIsMultilang) {
         Template::addJsPlugin(new JsPlugin_JQuery());
         Template::addJS(Url_Request::getBaseWebDir().'jscripts/formswitchlangs.js');
         $script = new Html_Element_Script('formShowOnlyLang(\''.Locale::getLang().'\');');
      }
      return $script;
   }

   /**
    * Magická metoda pro vložení neinicializované proměné do objektu
    * @param string $name -- název proměnné
    * @param mixed $value -- hodnota proměnné
    */
   public function  __set($name, $value) {
      $this->elements[$name] = $value;
   }

   /**
    * Metoda vraci inicializovanou proměnnou, pokud je
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  __get($name) {
      if(!isset($this->elements[$name])) {
         $this->elements[$name] = null;
      }
      return $this->elements[$name];
   }

   /**
    * Metoda kontroluje jestli byla daná proměnná inicializována
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  __isset($name) {
      return isset ($this->elements[$name]);
   }

   /**
    * Metoda maže danou proměnnou z objektu
    * @param string $name -- název proměnné
    */
   public function  __unset($name) {
      if(isset ($this->elements[$name])) {
         unset ($this->elements[$name]);
      }
   }


   /*
    * Metody pro přístup k prvkům formuláře
   */

   /**
    * Metoda přidává nový prvek do pole elemntů
    * @param string $name -- název prvku
    * @param mixed $value -- hodnota prvku
    * @return Form_Element
    */
   function offsetSet($name, $value) {
      $this->elements[$name] = $value;
   }
   /**
    * Metoda pro přístup k prvkům formuláře pře asociativní pole
    * @param string $name -- název prvku
    * @return Form_Element
    */
   function offsetGet($name) {
      if(!isset($this->elements[$name])) {
         $this->elements[$name] = null;
      }
      return $this->elements[$name];
   }

   /**
    * Metoda pro zjištění existence elementu ve struktuře formuláře.
    * @param string $name -- název formulářového elementu
    */
   function offsetExists($name) {
      return isset ($this->elements[$name]);
   }

   /**
    * Metoda je volána při odstranění elementu z formuláře
    * @param string $name -- název elemeentu
    */
   function offsetUnset($name) {
      unset ($this->elements[$name]);
   }

   /*
    * Metody zajišťující kontrolu a odeslání formuláře
   */

   /**
    * Metoda zjišťuje jestli byl formulář odeslán
    * @return boolean -- true pokud byl formulář odeslán
    */
   public function isSend() {
      foreach ($this->elements as $element) {
         if($element instanceof Form_Element_Submit) {
            $element->populate($this->sendMethod);
            //            $element->validate();
            if($element->isValid()) {
               $this->isSend = true;
               break;
            }
         }
      }
      return $this->isSend;
   }

   /**
    * Metoda zjišťuje jestli byl formulář odeslán v pořádku
    * @return booleant -- true pokud je formulář v pořádku
    */
   public function isValid() {
      if($this->isValid === null) {
         if($this->isSend()) {
            $this->isValid = true;
            $this->populate();
            return $this->isValid;
         }
         return false;
      } else {
         return $this->isValid;
      }
   }

   /**
    * Metoda provede naplnění všech prvků ve formuláři a jejich validaci
    */
   public function populate() {
      foreach ($this->elements as $name => $element) {
         $element->populate($this->sendMethod);
         $element->validate();
         if(!$element->isValid()) {
            $this->isValid = false;
         } else {
            $element->filter();
         }
      }
      $this->isPopulated = true;
   }

   /**
    * Metoda vrací jestli byl formůlář již vyplněn
    *
    * @return bool -- true pokud byl formulář již vyplněn
    */
   public function isPopulated() {
      return $this->isPopulated;
   }

   /**
    * Metoda zjišťuje jestli byl formulář potvrzen
    */
   private function isSubmited() {
      $submited = false;
      // projití všech submitů a zjištění jestli nebyl některý spuštěn
      foreach ($this->submitElements as $name => $element) {

      }
      return $submited;
   }

   /*
    * Metody pro práci s formulářem
   */

   /**
    * Metoda nastavuje akci pro formulář, tedy adresu kam se má odkazovat
    * @param string $link -- odkaz pro akci
    * @todo
    */
   public function setAction($link) {
      $this->html()->setAttrib('action', (string)$link);
   }

   /**
    * Metoda vrací akci pro odeslání formuláře
    * @return Links
    * @todo
    */
   public function getAction() {
      return $this->html()->getAttrib('action');
   }

   /**
    * Metoda nastavuje metodu odeslání formuláře (post|get)
    * @param string $method -- typ metody (výchozí je 'post')
    * @todo
    */
   public function setSendMethod($method = "post") {
      $this->html()->setAttrib('method', strtolower($method));
   }

   /**
    * Metoda vrací nastavenou metodu odeslání formuláře
    * @return string (post|get)
    * @todo
    */
   public function getSendMethod() {
      return $this->getAttrib('method');
   }

   /**
    * Metoda přidá element do formuláře
    * @param Form_Element $element -- objetk formulářového elementu
    * @param integer $group -- priorita (vhodné při řazení)
    * @todo dodělat přiřazení priority prvkům kvůli vykreslení v šabloě
    */
   public function addElement(Form_Element $element, $group = null) {
      $name = $element->getName();
      $this->elements[$name] = $element;
      $this->elements[$name]->setPrefix($this->formPrefix);
      if($element->isMultiLang()) {
         $this->formIsMultilang = true;
      }

      if($group == null) {
         $this->elementsGroups[$name] = $this->elements[$name]->getName();
      } else {
         if(!isset ($this->elementsGroups[$group])) {
            $this->addGroup($group);
         }
         $this->elementsGroups[$group]['elements'][$name] = $this->elements[$name]->getName();
      }

      // pokud je soubor přidám do formu že se bude přenášet po částech
      if($element instanceof Form_Element_File) {
         $this->html()->setAttrib("enctype", "multipart/form-data");
      }
   }

   /**
    * Metoda přidá skupinu pro elementy
    * @param string $name -- název skupiny - pro zařazování
    * @param string $label -- název skupiny - její název při renderu
    * @param string $text -- text ke skupině - popisný text ke skupině
    */
   public function addGroup($name, $label = null, $text = null) {
//      $this->elementsGroups[$name] = array();
      $this->elementsGroups[$name]['elements'] = array();
      $this->elementsGroups[$name]['label'] = $label;
      $this->elementsGroups[$name]['text'] = $text;
   }

   /*
    * Metody pro zpracování obsahu formuláře
   */

   /*
     * Metody pro render formuláře
   */

   /**
    * Metoda vyrenderuje formulář podle zadaného typu
    * @param Form_Decorator $decorator -- objekt Form dekorátoru
    */
   public function render(Form_Decorator $decorator = null) {
      print ($this->creatString($decorator));
   }

   /**
    * Metoda vrací objekt html elemntu formuláře, vhodné pro úpravu tříd, přidání
    * vlastností, atd
    * @return Html_Element
    */
   public function html() {
      return $this->htmlElement;
   }

   /*
     * Podpůrné metody
   */
}

?>
