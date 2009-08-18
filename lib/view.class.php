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
    * Objekt se systémovými parametry modulu (práva, ...)
    * @var Module_Sys
    */
   private $moduleSys = null;

   /**
    * Pole s proměnými z kontroleru, na konci každé metody jsou přeneseny do šablony
    * @var array
    */
   protected $viewVars = array();

   /**
    * Konstruktor Viewu
    *
    * @param Template $template -- objekt šablony
    * @param Module_Sys $moduleSys --  systémový objekt modulu
    */
   function __construct(Template $template, Module_Sys $moduleSys) {
      $this->template = $template;
      $this->moduleSys = $moduleSys;
      //		inicializace viewru
      $this->init();
   }

   /**
    * Destruktor při vyčištění viewru převede všechny interní proměnné do šablony
    */
   public function  __destruct() {
      foreach ($this->viewVars as $varName => $var) {
         $this->template()->{$varName} = $var;
      }
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
    * Metoda převede proměnné viewru do šablony
    */
   //   public function _assignVarsToTemplate() {
   //      foreach ($this->viewVars as $varName => $var) {
   //         $this->template()->{$varName} = $var;
   //      }
   //   }

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
      //TODO zbytečné
      if($this->template == null){
         $this->template = new Template();
         return $this->template;
      } else {
         return $this->template;
      }
   }

   /**
    * Metoda vrací objekt se systémovým nasatvením modulu
    * @return Module_Sys
    */
   final public function sys() {
      return $this->moduleSys;
   }

   /**
    * Metoda vrací objekt s modulem
    * @return Module
    */
   final public function module() {
      return $this->sys()->module();
   }

   /**
    * Metoda vrací objekt k právům uživatele
    * @return Rights -- objekt práv
    */
   final public function rights() {
      return $this->sys()->rights();
   }

   /**
    * Metoda vrací objekt odkazu na  danou stránku
    * @return Links -- objek odkazů
    */
   final public function link() {
      return clone $this->sys()->link();
   }

   /**
    * Metoda vytvoří objekt modelu
    * @param string $name --  název modelu
    * @return Objekt modelu
    */
   final public function createModel($name) {
      return new $name($this->sys());
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
      return $this->sys()->locale()->_m($message);
   }
}
?>