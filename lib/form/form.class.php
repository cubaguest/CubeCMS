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
class Form extends Html_Element implements ArrayAccess {
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
   private $isValid = true;

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
    * Konstruktor vytváří objekt formuláře
    * @param string $prefix -- (option) prefix pro formulářové prvky
    */
   function __construct($prefix = null) {
      $this->formPrefix = $prefix;
      parent::__construct('form');
      $this->setAttrib('action', new Links());
      $this->setAttrib('method', 'post');
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
         if($element instanceof Form_Element_Submit){
            $element->populate();
            if($element->isValid()){
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
      if($this->isSend()){
         $this->populate();
      }
      return $this->isValid;
   }

   /**
    * Metoda provede naplnění všech prvků ve formuláři a jejich validaci
    */
   public function populate() {
      foreach ($this->elements as $name => $element) {
         $element->populate($this->sendMethod);
         if(!$element->isValid()){
            $this->isValid = false;
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
      $element->populate();
         $element->validate();
         if($element->isValid()){
            $submited = true;
         }
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
    */
   public function setAction($link) {
      $this->setAttrib('action', (string)$link);
   }

   /**
    * Metoda vrací akci pro odeslání formuláře
    * @return Links
    */
   public function getAction() {
      return $this->getAttrib('action');
   }

   /**
    * Metoda nastavuje metodu odeslání formuláře (post|get)
    * @param string $method -- typ metody (výchozí je 'post')
    */
   public function setSendMethod($method = "post") {
      $this->setAttrib('method', strtolower($method));
   }

   /**
    * Metoda vrací nastavenou metodu odeslání formuláře
    * @return string (post|get)
    */
   public function getSendMethod() {
      return $this->getAttrib('method');
   }

   /**
    * Metoda přidá element do formuláře
    * @param Form_Element $element -- objetk formulářového elementu
    * @param integer $priority -- priorita (vhodné při řazení)
    * @todo dodělat přiřazení priority prvkům kvůli vykreslení v šabloě
    */
   public function addElement(Form_Element $element, $priority = null) {
//      $element->setRequestType($this->getRequest());
      $element->setPrefix($this->formPrefix);
      $this->elements[$element->getName()] = $element;
//      if($element instanceof Form_Element_Submit) {
//         $this->submitElements[$element->getName()] = & $this->elements[$element->getName()];
//      }
   }

   /*
    * Metody pro zpracování obsahu formuláře
    */

    /*
     * Metody pro render formuláře
     */

   /**
    * Metoda vyrenderuje formulář
    */
   public function render() {
      ;
   }

   /**
    * Metoda přidá css třídu do dormuláře
    * @param string $class -- název třídy
    */
   public function addCssClass($class) {
      ;
   }

   /**
    * Metoda odebere css třídu z formuláře
    * @param string $name -- název třídy
    */
   public function removeCssClass($name) {
      ;
   }

    /*
     * Podpůrné metody
     */

     /**
      * Metoda vrací typ zvoleného requestu
      * @retur 
      */
     private function getRequest() {
        if($this->sendMethod == 'post'){
           return $_POST;
        } else if($this->sendMethod == 'get'){
           return $_GET;
        } else {
           throw new InvalidArgumentException(_('Nepodporovaný typ požadavku'), 1);
        }
     }

   /**
    * Metoda vrací objekt s chybovými zprávami
    * @return Messages -- objekt zpráv
    */
   final private function errMsg() {
      return AppCore::getUserErrors();
   }
}

?>
