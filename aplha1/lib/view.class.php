<?php
require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'template.class.php');


abstract class View {

	/**
	 * Objekt modelu aplikace
	 */
	private $model = null;

	/**
	 * Objekt s konfigurací
	 * @var Config
	 */
	private $config = null;

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
	 * Konstruktor Viewu
	 *
	 * @param Model -- použitý model
	 * @param Config -- konfigurační volby
	 */
	function __construct(Models $model, Module $module, Config $config) {
		$this->model = $model;
		$this->config = $config;
		$this->module = $module;
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
		if($this->template == null){
			return $this->template = new Template($this->getModule());
		} else {
			return $this->template;
		}
	}
	
	
	
}

?>