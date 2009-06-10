<?php
/**
 * Abstraktní třída pro objektu viewru.
 * Třídá slouží jako základ pro tvorbu Viewrů jednotlivých modulů. Poskytuje základní paramtery a metody k vytvoření pohledu modulu.
 * 
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: controller.class.php 610 2009-05-29 07:14:39Z jakub $ VVE3.9.4 $Revision: 610 $
 * @author        $Author: jakub $ $Date: 2009-05-29 09:14:39 +0200 (Pá, 29 kvě 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-05-29 09:14:39 +0200 (Pá, 29 kvě 2009) $
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
	 * @var ModuleSys
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
	 * @param Model -- použitý model
	 */
	function __construct(Template $template, ModuleSys $moduleSys) {
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
      $this->viewVars[$name] = $value;
   }

   /**
    * Metoda vraci inicializovanou proměnnou, pokud je
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  __get($name) {
      if(isset($this->viewVars[$name])){
         return $this->viewVars[$name];
      } else {
         return null;
      }
   }

   /**
    * Metoda kontroluje jestli byla daná proměnná inicializována
    * @param string $name -- název proměnné
    * @return mixed -- hodnota proměnné
    */
   public function  __isset($name) {
      return isset($this->viewVars[$name]);
   }

   /**
    * Metoda maže danou proměnnou z objektu
    * @param string $name -- název proměnné
    */
   public function  __unset($name) {
      if(isset ($this->viewVars[$name])){
         unset ($this->viewVars[$name]);
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
    * @return ModuleSys
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
    * Metoda přidá danou šablonu
    * @param string $name -- název šablony
    * @param boolean $engine -- jestli je šablona enginu
    * @return View -- vrací objekt sebe
    */
//   final public function addTpl($name, $engine  = false) {
//      $this->template()->addTplFile($name, $engine);
//      return $this;
//   }
	
   /**
    * Metoda přidá proměnnou do šablony
    * @param string $name -- název proměnné
    * @param mixed $value -- hodnota proměnné
    * @param boolean/string $array -- jestli má být proměná zařazena do pole
    * nebo pod název pole
    * @return View -- vrací objekt sebe
    */
//   final public function addVar($name, $value, $array = false) {
//      $this->template()->setVar($name, $value, $array);
//      return $this;
//   }

   /**
    * Metoda přidá do šablony zadaný odkaz
    * @param Links $link -- objekt odkazu
    * @return View -- vrací sám sebe
    */
//   final public function addLink($name, Links $link){
//      $this->template()->setLink($name, $link);
//      return $this;
//   }

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
    * Metoda přeloží zadaný řetězec
    * @param string $message -- řetězec k přeložení
    * @return string -- přeložený řetězec
    */
   final public function _m($message) {
      return $this->sys()->locale()->_m($message);
   }
}
?>