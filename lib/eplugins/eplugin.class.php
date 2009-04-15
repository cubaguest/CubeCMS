<?php
/**
 * Abstraktní třída pro Engine Pluginy - EPlugins.
 * Třída obsahuje základní metody pro vytváření EPluginu a práci s nimi
 * (např. scroll, comments, atd.).
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
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
	 * Objekt s informacemi o právach uživatele
	 * @var Rights
	 */
	private $rights = null;

   /**
    * Id šablony
    * @var integer
    */
   protected $idTpl = 1;

   /**
    * podnázev šablony pluginu
    * @var string
    */
   protected static $tplSubName = array();

	/**
	 * Konstruktor třídy, spouští metodu init();
	 */
	function __construct(Rights $rights = null){
		if($rights instanceof Rights){
			$this->rights = $rights;
			$this->auth = $rights->getAuth();
		}
        /**
         * @todo dodělat, tak ať se to nčítá třeba přímo z modulu nebo kategoorie, nebo jádra
         */
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
	protected function getLinks($clear = false, $onlyWebRoot = false){
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
	protected function getModule(){
      return AppCore::getSelectedModule();
	}
	
	
	/**
	 * Metoda vrací objekt k připojení k db
	 *
	 * @return DbInterface -- objekt Db
	 */
	protected function getDb(){
      return AppCore::getDbConnector();
	}

	/**
	 * Metoda vrací objekt ke konfiguraci enginu
	 *
	 * @return Config -- objekt Config
	 */
	protected function getSysConfig(){
		return AppCore::sysConfig();
	}

	/**
	 * Metoda vrací objekt autorizace a právům k modulům
	 *
	 * @return Rights -- objekt Rights
	 */
	protected function getRights(){
		return $this->rights;
	}

	/**
	 * Metoda vrací objekt chbových zpráv
	 *
	 * @return Messages -- objekt chybových zpráv
	 */
	protected function errMsg(){
      return AppCore::getUserErrors();
	}

	/**
	 * Metoda vrací objekt informačních zpráv
	 *
	 * @return Messages -- objekt informačních zpráv
	 */
	protected function infoMsg(){
      return AppCore::getInfoMessages();
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
	final public function getTpl(){
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
	 */
	protected function assignTpl(){}
	
	/**
	 * Metoda pro asociování proměných do šablony
	 *
	 * @param string -- název proměnné
	 * @param mixed -- hodnota proměnné
	 */
	final protected function toTpl($tplValueName, $value){
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

   /**
	 * Metoda nastaví id šablony pro výpis
	 * @param integer -- id šablony (jakékoliv)
	 */
	public function setIdTpl($id) {
		$this->idTpl = $id;
	}

   /**
    * Metoda vrací id šablony
    * @return integer
    */
   protected function getIdTpl() {
      return $this->idTpl;
   }

   /**
	 * Metoda nastaví podnázev šablony pro výpis
	 * @param string -- podnázev šablony (jakékoliv)
	 */
	public function setTplSubName($name) {
      self::$tplSubName[$this->getIdTpl()] = $name;
	}

   /**
    * Metoda vrací podnázev šablony
    * @return string
    */
   protected function getTplSubName() {
      if(isset(self::$tplSubName[$this->getIdTpl()])){
         return self::$tplSubName[$this->getIdTpl()];
      } else {
         return null;
      }
   }
}
?>