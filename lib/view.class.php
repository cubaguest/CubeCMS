<?php
/**
 * Abstraktní třída pro objek viewru
 * 
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	View class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: view.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída pro obsluhu pohledu
 */

abstract class View {
	/**
	 * Adresář s JavaScript Pluginy
	 * @var string
	 */
	const JSPLUGINS_DIR = 'JsPlugins';
	
	/**
	 * Objekt modelu aplikace
	 */
	private $model = null;

	/**
	 * Objekt s informacemi o modulu
	 * @var Module
	 */
	private $module = null;

	/**
	 * Objekt pro práci s šablonovacím systémem
	 * @var 
	 */
	private $template = null;
	
	/**
	 * Objekt pro zjištění práv uživatele
	 * @var Rights
	 */
	private $rights = null;
	
	/**
	 * Objekt kontaineru ve který obsahuje data z kontroleru
	 * @var Container
	 */
	private $container = null;
	
	/**
	 * Konstruktor Viewu
	 *
	 * @param Model -- použitý model
	 */
	function __construct(Module $module, Rights $rights, Template &$template, Container $container) {
		$this->rights = $rights;
		$this->module = $module;
		$this->template = $template;
		$this->container = $container;
		
//		inicializace viewru
		$this->init();
		
		$this->saveContainerToTpl();
	}

	/**
	 * Metoda vloži data s containeru do šablony
	 *
	 */
	private function saveContainerToTpl() {
//		Uložení dat containeru do šablony
		foreach ($this->container()->getAllData() as $key => $var) {
			$this->template()->addVar($key, $var);
		}

//		Uložení odkazů do šablony
		foreach ($this->container()->getAllLinks() as $key => $var) {
			$this->template()->addVar($key, $var);
		}
	}
	
	
	/**
	 * Metoda, která se provede vždy
	 */
	public function init() {
		
	}
	
	
	/**
	 * Hlavní abstraktní třída pro vytvoření pohledu
	 */
	abstract function mainView();

	final public function getModule() {
		return 	$this->module;
	}
	
	
	/**
	 * Funkce vrací datový adresář modulu
	 * @return string -- datový adresář modulu
	 */
	final public function getDataDir() {
		return $this->getModule()->getDir()->getDataDir();
	}
	
	/**
	 * Funkce vrací objekt modelu
	 * @return Models -- objekt modelu
	 */
	final public function getModel() {
		return $this->model;
	}
	
	/**
	 * Metoda vrací objekt šablony, přes kerý se přiřazují proměnné do šablony
	 * @return Template -- objekt šablony
	 */
	final public function template(){
		//TODO zbytečné
		if($this->template == null){
			return $this->template = new Template();
		} else {
			return $this->template;
		}
	}
	
	/**
	 * Metoda vrací objekt k právům uživatele
	 * @return Rights -- objekt práv
	 */
	final public function getRights() {
		return $this->rights;
	}
	
	/**
	 * Metoda vrací objekt kontaineru. který slouží pro přenos dat z kontroleru do viewru
	 * 
	 * @return Container -- objekt s daty
	 */
	final public function container() {
		return $this->container;
	}
	
}

?>