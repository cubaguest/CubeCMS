<?php
/**
 * Abstraktní třída pro Engine Pluginy
 * (např. scroll, comments, atd.)
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	Eplugin class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: eplugin.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Abstraktní třída pro vytvoření Epluginu
 */
class Eplugin {
	/**
	 * Výchozí cesta s šablonama
	 * @var string
	 */
	const EPLUGINS_DEFAULT_TEMPALTES_DIR = AppCore::TEMPLATES_DIR;

	/**
	 * Proměná s názvem šablony
	 * @var string 
	 */
	protected $templateFile = null;
	
	/**
	 * Proměná s polem hodnot předávaných do šablony
	 * @var array
	 */
	private $_tplVarsArray = array();
	
	/**
	 * Proměná s polem
	 */
	private $_tplJSPluginsArray = array();
	
	/**
	 * Objekt s linky
	 * @var Links
	 */
	private $links = null;
	
	/**
	 * Objekt s db konektorem
	 * @var Db
	 */
	private $dbConnector = null;
	
	/**
	 * Objekt s informacemi o modulu
	 * @var Module
	 */
	private $module = null;

	/**
	 * Objekt s informacemi o právach uživatele
	 * @var Rights
	 */
	private $rights = null;
	
	/**
	 * Objekt s konfiguračními volbami enginu
	 * @var Config
	 */
	private $sysConfig = null;
	
	/**
	 * Konstruktor třídy, spouští metodu init();
	 *
	 */
	function __construct(Db $dbConnector, Module $module, Rights $rights, Config $sysConfig)
	{
		$this->module = $module;
		$this->dbConnector = $dbConnector;
		$this->rights = $rights;
		$this->sysConfig = $sysConfig;

		$this->init();
	}
	
	protected function init(){}
	
	/**
	 * Metoda vrací objekt k tvorbě odkazů
	 *
	 * @return Links -- objekt odkazů
	 */
	protected function getLinks()
	{
		return new Links();
	}
	
	/**
	 * Metoda vrací objekt modulu
	 *
	 * @return Module -- objekt modulu
	 */
	protected function getModule()
	{
		return $this->module;
	}
	
	
	/**
	 * Metoda vrací objekt k připojení k db
	 *
	 * @return DbInterface -- objekt Db
	 */
	protected function getDb()
	{
		return $this->dbConnector;
	}

	/**
	 * Metoda vrací objekt ke konfiguraci enginu
	 *
	 * @return Config -- objekt Config
	 */
	protected function getSysConfig()
	{
		return $this->sysConfig;
	}

	/**
	 * Metoda vrací objekt autorizace a právům k modulům
	 *
	 * @return Rights -- objekt Rights
	 */
	protected function getRights()
	{
		return $this->rights;
	}
	
	
	/**
	 * Abstraktní metoda pro inicializaci epluginu
	 *
	 */
//	abstract function init();
	
	/**
	 * Metoda vrací název šablony
	 * @return string -- název šablony
	 */
	final public function getTpl()
	{
//		echo $this->templateFile;
		return $this->templateFile;
	}
	
	/**
	 * Metoda vrací objekt pluginu scroll
	 * @return Scroll -- objekt scrolleru
	 */
	public function scroll(){
		return new Scroll($this->getDb(), $this->getModule(), $this->rights, $this->sysConfig);
	}
	
	/**
	 * Metoda vrací objekt epluginu changes
	 * @return Changes -- objekt epluginu changes
	 */
	public function changes() {
		return new Changes($this->getDb(), $this->getModule(), $this->rights, $this->sysConfig);
	}
	
	/**
	 * Metoda zařadí proměné epluginu do šablony
	 * Je volána z viewru
	 *
	 * @param Template -- objekt šablony
	 */
	public function assignToTpl(Template $template)	{
//		Přiřazení proměnných do pole
		$this->assignTpl();

//		Vložení proměných do šablony
		foreach ($this->_tplVarsArray as $var => $value) {
			$template->addVar($var, $value);
		}
		
//		Vložení JSPluginů
		foreach ($this->_tplJSPluginsArray as $jsPlugin) {
			$template->addJsPlugin($jsPlugin);
		}
		
	}
	
	/**
	 * Metoda obstarává přiřazení proměných do šablony
	 *
	 */
	protected function assignTpl(){}
	
	/**
	 * Metoda pro asociování proměných do šablony
	 *
	 * @param string -- název proměnné
	 * @param mixed -- hodnota proměnné
	 */
	final protected function toTpl($tplValueName, $value)
	{
		$this->_tplVarsArray[$tplValueName] = $value;
	}
	
	/**
	 * Metoda přidává zadaný objekt JSpluginu do šablony
	 * @param JSPlugin -- objekt JSPluginu
	 */
	final protected function toTplJSPlugin($jsPlugin) {
		array_push($this->_tplJSPluginsArray, $jsPlugin);
	}
	
	
	
}
?>