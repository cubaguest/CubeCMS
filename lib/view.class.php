<?php
/**
 * Abstraktní třída pro objektu viewru.
 * Třídá slouží jako základ pro tvorbu Viewrů jednotlivých modulů. Poskytuje základní paramtery a metody k vytvoření pohledu modulu.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Abstraktní třída kontroleru modulu
 */

abstract class View {
   /**
    * Objekt pro práci s šablonovacím systémem
    * @var Template
    */
   private $template = null;

   /**
    * Objekt s pro lokalizaci
    * @var Locale
    */
   private $locale = null;

   /**
    * Objekt s odkazem
    * @var Url_Link
    */
   private $link = null;

   /**
    * Objekt c kategorií
    * @var Category
    */
   private $category = null;

   /**
    * Konstruktor Viewu
    *
    * @param Template $template -- objekt šablony
    * @param Module_Sys $moduleSys --  systémový objekt modulu
    */
   function __construct(Url_Link_Module $link, Category $category) {
      $this->template = new Template_Module($link, $category);
      $this->link = $link;
      $this->category = $category;
      $this->locale = new Locale($category->getModule()->getName());
      //		inicializace viewru
      $this->init();
   }

   /**
    * Destruktor při vyčištění viewru převede všechny interní proměnné do šablony
    */
   public function  __destruct() {
   }

   /**
    * Magická metoda pro vložení neinicializované proměné do objektu
    * @param string $name -- název proměnné
    * @param mixed $value -- hodnota proměnné
    */
   public function  __set($name, $value) {
//      $this->viewVars[$name] = $value;
      $this->template()->{$name} = $value;
   }

   /**
    * Metoda vraci inicializovanou proměnnou, pokud je
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  __get($name) {
      if(!isset($this->template()->{$name})){
         $this->template()->$name = null;
      }
      return $this->template()->$name;
   }

   /**
    * Metoda kontroluje jestli byla daná proměnná inicializována
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  __isset($name) {
      //return isset($this->viewVars[$name]);
      return isset ($this->template()->{$name});
   }

   /**
    * Metoda maže danou proměnnou z objektu
    * @param string $name -- název proměnné
    */
   public function  __unset($name) {
      //      if(isset ($this->viewVars[$name])){
      //         unset ($this->viewVars[$name]);
      //      }
      if(isset ($this->template()->{$name})){
         unset ($this->template()->{$name});
      }
   }

   /**
    * Metoda, která se provede vždy
    */
   public function init() {}

   /**
    * Hlavní abstraktní třída pro vytvoření pohledu
    */
   abstract function mainView();

   /**
    * Metoda vrací objekt šablony, přes kerý se přiřazují proměnné do šablony
    * @return Template -- objekt šablony
    */
   public function template(){
      return $this->template;
   }

   /**
    * Metoda vrací objekt s kategorií
    * @return Category
    */
   final public function category() {
      return $this->category;
   }

   /**
    * Metoda vrací název modulu
    * @return string
    */
   final public function module() {
      return $this->category()->getModule()->getName();
   }

   /**
    * Metoda vrací objekt k právům uživatele
    * @return Rights -- objekt práv
    */
   final public function rights() {
      return $this->category()->getRights();
   }

   /**
    * Metoda vrací objekt odkazu na  danou stránku (alias pro metodu l())
    * @return Url_link_Module -- objek odkazů
    */
   final public function link() {
      return clone $this->l();
   }

   /**
    * Metoda vrací objekt odkazu na  danou stránku
    * @return Url_link_Module -- objek odkazů
    */
   final public function l() {
      return clone $this->link;
   }

   /**
    * Metoda vrací objekt Locale pro překlady
    * @return Locale -- objek lokalizace
    */
   final public function locale() {
      return $this->locale;
   }

   /**
    * Metoda přeloží zadaný řetězec alias pro funkci _()
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    */
   final public function _m($message) {
      return $this->_($message);
   }

   /**
    * Metoda přeloží zadaný řetězec
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    */
   final public function _($message) {
      return $this->locale()->_m($message);
   }
}
?>