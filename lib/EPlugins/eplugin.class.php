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
	 * Objekt pro ukládání chybových hlášek
	 * @var Messages
	 */
	private $errMsg = null;
	
	/**
	 * Objekt pro ukládání informačních hlášek
	 * @var Messages
	 */
	private $infoMsg = null;
	
	/**
	 * Konstruktor třídy, spouští metodu init();
	 *
	 */
	function __construct(Module $module, Rights $rights, Messages $errors, Messages $info)
	{
		$this->module = $module;
//		$this->dbConnector = $dbConnector;
		$this->dbConnector = AppCore::getDbConnector();
		$this->rights = $rights;
		
		$this->errMsg = $errors;
		$this->infoMsg = $info;

		$this->init();
	}
	
	protected function init(){}
	
	/**
	 * Metoda vrací objekt k tvorbě odkazů
	 *
	 * @return Links -- objekt odkazů
	 */
	protected function getLinks($clear = false)
	{
		return new Links($clear);
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
		return AppCore::sysConfig();
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
	 * Metoda vrací objekt chbových zpráv
	 *
	 * @return Messages -- objekt chybových zpráv
	 */
	protected function errMsg()
	{
		return $this->errMsg;
	}

	/**
	 * Metoda vrací objekt informačních zpráv
	 *
	 * @return Messages -- objekt informačních zpráv
	 */
	protected function infoMsg()
	{
		return $this->infoMsg;
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
		return new Scroll($this->getModule(), $this->rights, $this->errMsg, $this->infoMsg);
	}
	
	/**
	 * Metoda vrací objekt epluginu changes
	 * @return Changes -- objekt epluginu changes
	 */
	public function changes() {
		return new Changes($this->getModule(), $this->rights, $this->errMsg, $this->infoMsg);
	}
	
	/**
	 * Metoda vrací objekt epluginu userfiles
	 * @return UserFiles -- objekt epluginu userfiles
	 */
	public function userfiles() {
		return new UserFiles($this->getModule(), $this->rights, $this->errMsg, $this->infoMsg);
	}
	
	/**
	 * Metoda vrací objekt epluginu userimages
	 * @return UserImages -- objekt epluginu userimages
	 */
	public function userimages() {
		return new UserImages($this->getModule(), $this->rights, $this->errMsg, $this->infoMsg);
	}
	
	/**
	 * Metoda vrací objekt epluginu progressbar
	 * @return ProgressBar -- objekt epluginu progressbar
	 */
	public function progressbar() {
		return new ProgressBar($this->getModule(), $this->rights, $this->errMsg, $this->infoMsg);
	}

	/**
	 * Metoda vrací objekt epluginu sendmail
	 * @return SendMail -- objekt epluginu sendmail
	 */
	public function sendmail() {
		return new SendMail($this->getModule(), $this->rights, $this->errMsg, $this->infoMsg);
	}

	/**
	 * Metoda vrací objekt epluginu csvdata
	 * @return CsvData -- objekt epluginu csvdata
	 */
	public function csvdata() {
		return new CsvData($this->getModule(), $this->rights, $this->errMsg, $this->infoMsg);
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