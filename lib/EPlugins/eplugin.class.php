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
    * Parametr pro přenos souboru js pluginu
    */
   const PARAMS_EPLUGIN_FILE_PREFIX = 'eplugin';

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
	 * Proměná obsahuje jestli se spouští pouze eplugin nebo celý web
	 * @var boolean
	 */
	private static $runOnly = false;

	/**
	 * Konstruktor třídy, spouští metodu init();
	 *
	 */
	function __construct(Rights $rights = null)
	{
		$this->module = AppCore::getSelectedModule();
		$this->dbConnector = AppCore::getDbConnector();
		
		if($rights instanceof Rights){
			$this->rights = $rights;
			$this->auth = $rights->getAuth();
		}
        /**
         * @todo dodělat, tak ať se to nčítá třeba přímo z modulu nebo kategoorie, nebo jádra
         */
//		else if(AppCore::getSelectedModule() instanceof Module){
//            $this->rights =
//		}

		if(AppCore::getModuleErrors() instanceof Messages){
			$this->errMsg = AppCore::getModuleErrors();
		} else {
			$this->errMsg = new Messages();
		}
		if(AppCore::getModuleMessages() instanceof Messages){
			$this->infoMsg = AppCore::getModuleMessages();
		} else {
			$this->infoMsg = new Messages();
		}
		$this->init();
	}
	
	/**
	 * Metoda nastavuje knihovnu epluginu
	 * @param Auth -- objekt autorizace
	 */
	public function setAuthParam(Auth $auth) {
		$this->auth = $auth;
	}

	/**
	 * Metoda nastavuje že je eplugin spouštěn samostatně nebo ne
	 * @param boolean $value -- true pokud je eplugin spoštěn samostatně
	 */
//	public static function setRunOnly($value){
//		self::$runOnly = $value;
//	}

	/**
	 * Metoda vrací jestli je eplugin spouštěn samostatně nebo jako
	 * součást stránky
	 * @return boolean -- true pokud je spuštěn samostatně
	 */
//	public static function isRunOnly() {
//		return self::$runOnly;
//	}

	/**
	 * Metoda nastavuje knihovnu epluginu je použita v epluginech
	 */
	public function setParams() {}

   /**
    * Metoda inicializuje spuštění epluginu pouze jako sjednoho souboru
    */
   public function initRunOnlyEplugin() {
      $this->runOnlyEplugin(UrlRequest::getSupportedServicesFile(),
         UrlRequest::getSupportedServicesParams());
   }

	/**
	 * Metoda se využívá pro načtení proměných do stránky, 
	 * je volána při volání parametru stránky pro EPlugin
	 *
	 */
	public function runOnlyEplugin($fileName, $fileParams = null){}
	
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
	protected function getLinks($clear = false, $onlyWebRoot = false)
	{
        $link = new Links($clear, $onlyWebRoot);
        $cat = AppCore::getSellectedCategory();
        if($cat != false){
            $link->category($cat[Category::COLUMN_CAT_LABEL], $cat[Category::COLUMN_CAT_ID]);
        }
        return $link;
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
		$name = strtolower(get_class($this));
      return str_ireplace(self::PARAMS_EPLUGIN_FILE_PREFIX, '', $name);
	}
	
	/**
	 * Metoda vrací název šablony
	 * @return string -- název šablony
	 */
	final public function getTpl()
	{
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
	public function getFileLink($file = null, $params = null) {
      if($file == null) {
         $file = '.'.URL_SEPARATOR.self::PARAMS_EPLUGIN_FILE_PREFIX.$this->getEpluginName()
            .URL_SEPARATOR.$this->getEpluginName().'js';
         if($params != null AND is_array($params)) {
            $param = http_build_query($params);
            $file.='?'.$param;
         }
      } else if(is_string($file)) {
         $file = '.'.URL_SEPARATOR.self::PARAMS_EPLUGIN_FILE_PREFIX.$this->getEpluginName().URL_SEPARATOR.$file;
         if($params != null AND is_array($params)) {
            $param = http_build_query($params);
            $file.='?'.$param;
         }
      } else if($file instanceof JsFile){
         $file = '.'.URL_SEPARATOR.self::PARAMS_EPLUGIN_FILE_PREFIX.$this->getEpluginName().URL_SEPARATOR.$file;
      }

      
      return $file;
	}
	
}
?>