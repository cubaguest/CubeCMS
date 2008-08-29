<?php
//require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'template.class.php');


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
	 * Objekt pro zjištění práv uživatele
	 * @var Rights
	 */
	private $rights = null;
	
	/**
	 * Konstruktor Viewu
	 *
	 * @param Model -- použitý model
	 * @param Config -- konfigurační volby
	 */
	function __construct(Models $model, Module $module, Config $config, Rights $rights, Template &$template) {
		$this->rights = $rights;
		$this->model = $model;
		$this->config = $config;
		$this->module = $module;
		$this->template = $template;
		
//		inicializace viewru
		$this->init();
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
}

?>