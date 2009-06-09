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
	 * Konstruktor Viewu
	 *
	 * @param Model -- použitý model
	 */
	function __construct(Template $template, ModuleSys $moduleSys) {
		$this->template = $template;
      $this->moduleSys = $moduleSys;
		
//		inicializace viewru
		$this->init();
		//$this->saveContainerToTpl();
	}

	/**
	 * Metoda vloži data s containeru do šablony
	 *
	 */
//	private function saveContainerToTpl() {
////		Uložení dat containeru do šablony
//		foreach ($this->container()->getAllData() as $key => $var) {
//			$this->template()->addVar($key, $var);
//		}
////		Uložení odkazů do šablony
//		foreach ($this->container()->getAllLinks() as $key => $var) {
//			$this->template()->addVar($key, $var);
//		}
//	}
	
	/**
	 * Metoda, která se provede vždy
	 */
	public function init() {
		
	}
	
	/**
	 * Hlavní abstraktní třída pro vytvoření pohledu
	 */
	abstract function mainView();

	/**
	 * Metoda vrací objekt modulu
	 *
	 * @return Module -- objekt modulu
	 */
//	final public function getModule() {
//      return Module::getCurrentModule();
////      return AppCore::getSelectedModule();
//	}
	
	/**
	 * Funkce vrací datový adresář modulu
	 * @return string -- datový adresář modulu
	 */
//	final public function getDataDir() {
//		return $this->getModule()->getDir()->getDataDir();
//	}
	
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
}
?>