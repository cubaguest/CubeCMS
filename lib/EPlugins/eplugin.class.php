<?php
/**
 * Abstraktní třída pro Engine Pluginy - EPlugins.
 * Třída obsahuje základní metody pro vytváření EPluginu a práci s nimi
 * (např. scroll, comments, atd.).
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: eplugin.class.php 3.1.8 13.11.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Abstraktní třída pro vytvoření Epluginu
 * @todo 			implementovat generování názvu souborů pro zvolený eplugin
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
	 * Obejt s autorizačními informacemi
	 * @var Auth
	 */
	private $auth = null;
	
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
	function __construct(Messages $errors = null, Messages $info = null, Rights $rights = null)
	{
		$this->module = AppCore::getSelectedModule();
		$this->dbConnector = AppCore::getDbConnector();
		
		if($rights instanceof Rights){
			$this->rights = $rights;
			$this->auth = $rights->getAuth();
		}
		if($errors instanceof Messages){
			$this->errMsg = $errors;
		} else {
			$this->errMsg = new Messages();
		}
		if($info instanceof Messages){
			$this->infoMsg = $info;
		} else {
			$this->infoMsg = new Messages();
		}
		$this->init();
	}
	
	/**
	 * Metoda zjišťuje jestli byl nastaven index na eplugin
	 * 
	 * @return boolean -- true pokud se má zpracovávat eplugin
	 */
	public static function isEplugin() {
		if(isset($_GET[Links::GET_EPLUGIN_NAME])){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Metoda vrací název zvoleného epluginu
	 *
	 * @return string -- název epluginu
	 */
	public static function getSelEpluginName() {
		return rawurldecode($_GET[Links::GET_EPLUGIN_NAME]);
	}
		
	/**
	 * Metoda nastavuje knihovnu epluginu
	 * @param Auth -- objekt autorizace
	 */
	public function setAuthParam(Auth $auth) {
		$this->auth = $auth;
	}

	/**
	 * Metoda nastavuje knihovnu epluginu je použita v epluginech
	 */
	public function setParams() {
	}

	/**
	 * Metoda se využívá pro načtení proměných do stránky, 
	 * je volána při volání parametru stránky pro EPlugin
	 *
	 */
	public function runOnlyEplugin(){}
	
	/**
	 * Inicializační metoda EPluginu
	 * 
	 */
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
	 * Metoda vrací název epluginu
	 * @return string -- název epluginu
	 */
	public final function getEpluginName() {
		return strtolower(get_class($this));
	}
	
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
	
	/**
	 * Metoda vrací odkaz na soubor epluginu
	 * //TODO možná implementovat vracení odkazu na EPlugin file (./epluginuserimages.js)
	 */
//	public function getFileLink() {
//		;
//	}
	
	
}
?>