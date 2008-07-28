<?php
//třída pro práci s modelem
require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'model.class.php');

abstract class Controller {
	/**
	 * Objekt obsahuje model modulu
	 * @var Model
	 */
	private $model = null;

	/**
	 * Objekty se základními vlastnostmi modulu
	 * @var Module
	 */
	private $module = null;

	/**
	 * Objekt obsahující připojení k databázi a práci s ní
	 * @var DbInterface
	 */
	private $db = null;
	
	/**
	 * Objekt se systémem autorizace přístupu
	 * @var Auth
	 */
	private $auth = null;
	
	/**
	 * Objekt se sytémovou konfigurací
	 * @var Config
	 */
	private $sysConfig = null;
	
	/**
	 * Název nového actionViewru
	 * @var string
	 */
	private $actionViewer = null;
	
	/**
	 * Objekt pto práci s akcemi
	 * @var ModuleAction
	 */
	private $action = null;
	
	/**
	 * Objekt s právy k modulu
	 * @var Rights
	 */
	private $rights = null;

	/**
	 * Objekt s informačními zprávami modulu
	 * @var Messages
	 */
	private $infomsg = null;

	/**
	 * Objekt s chybovými zprávami modulu (NE VYJÍMKY)
	 * @var Messages
	 */
	private $errmsg = null;
	
	/**
	 * Pole s objekty e-pluginu
	 * @var array(objects)
	 */
	private $eplugins = array();
	
	/**
	 * Objekt s článkem
	 * @var Article
	 */
	private $article = null;
	
	/**
	 * Konstruktor třídy vytvoří a naplní základní vlastnosti pro modul
	 *
	 * @param Module -- objekt modulu
	 * @param Config -- objekt s konfigem
	 * @param unknown_type $aplicationMainDir //TODO
	 */
	function __construct(Module $module, Config $config, DbInterface $db, ModuleAction $action, Rights $rights, Messages &$messages, Messages &$errors, Article $article) {
		
		//TODO
		$this->module = $module;
		$this->db = $db;
		$this->model = new Models();
		$this->sysConfig = $config;
//		$this->link = new Links();
		$this->action = $action;
		$this->auth = $rights->getAuth();
		$this->rights = $rights;
		$this->infomsg = $messages;
		$this->errmsg = $errors;
		$this->article = $article;
		
		
		
	}


	
	/**
	 * Metoda vrací objekt systémové konfigurace
	 * @return Config -- objekt systémové konfigurace
	 */
	final public function getSysConfih(){
		return $this->sysConfig;
	}
	
	/**
	 * Metoda vrací objekt pro přístup k db
	 * @return DbInterface -- objekt databáze
	 */
	final public function getDb() {
		return $this->db;
	}
	
	/**
	 * Metoda vrací odkaz na objekt pro práci s odkazy
	 * @return Links -- objekt pro práci s odkazy
	 */
	final public function getLink($clear = false) {
		return new Links($clear);
	}
	
	/**
	 * Metoda vrací model
	 *
	 * @return Models -- objekt modelu
	 */
	final public function getModel(){
		return $this->model;
	}
	
	/**
	 * Metody vrací objekt modulu
	 * @return Module -- objekt modulu
	 */
	final public function getModule() {
		return $this->module;
	}
	
	/**
	 * Metody vrací objekt autorizace
	 * @return Auth -- objekt autorizace
	 */
	final public function getAuth() {
		return $this->auth;
	}
	
	
	/**
	 * Metoda vrací objekt na akci
	 * @return ModuleAction -- objekt akce
	 */
	final public function getAction() {
		return $this->action;
	}
	
	/**
	 * Metoda vrací objekt s právy na modul
	 * @return Rights -- objekt práv
	 */
	final public function getRights() {
		return $this->rights;
	}

	/**
	 * Metoda vrací objekt s článkem
	 * @return Article -- objekt článku
	 */
	final public function getArticle() {
		return $this->article;
	}

	/**
	 * Metoda vrací objekt s informačními zprávami
	 * @return Messages -- objekt zpráv
	 */
	final public function infoMsg() {
		return $this->infomsg;
	}

	/**
	 * Metoda vrací objekt s chybovými zprávami
	 * @return Messages -- objekt zpráv
	 */
	final public function errMsg() {
		return $this->errmsg;
	}
	
	
	/**
	 * Vrací objekt modulu
	 * @return Module -- objekt modulu
	 */
	function __destruct() {
		unset ($this->db);
	}

	/**
	 * Hlavní metoda třídy kontroleru, provádí se pokud není žádná akce
	 */
	abstract function mainController();

	/**
	 * Metoda načte požadovaný model a nastaví na něj objek $model
	 *
	 * @param string -- název modelu
	 */
	final public function createModel($modelName)
	{
		//TODO
		$modelFileName = strtolower($modelName);

		$modelPath = $this->getModule()->getDir()->getModelsDir().$modelFileName.'.php';

		if(!file_exists($modelPath)){
			$return = null;
			new CoreException(_("Cannot load model ") . $modelFileName, 1);
		} else {
			require_once $modelPath;
			$this->model = new $modelName();
			$return = $this->model;
		}
		
		return $return;
	}
	
	/**
	 * Metoda vytvoří objekt engine pluginu
	 *
	 * @param string -- název e-pluginu
	 * 
	 * @return Objekt -- vytvořený objekt epluginu 
	 */
	final public function addEPlugin($pluginName){
//		První písmeno velké
		$className = ucfirst($pluginName);
			if(class_exists($className)){
				return new $className($this->getLink(), $this->getDb());
			}
	}
	
	/**
	 * Meotda vrací objekt EPluginu
	 */
	final public function eplugin(){
	//		První písmeno velké
//		$className = ucfirst($pluginName);
//			if(class_exists($className)){
//				return new $className($this->getLink(), $this->getDb(), $this->getModule());
//			}
		return new Eplugin($this->getDb(),$this->getModule(), $this->getRights());
		
	}
	
	/**
	 * Metoda změní výchozí actionViewer na zadaný
	 * @param string -- název actionViewru
	 */
	final public function changeActionView($newActionView) {
		$this->actionViewer = $newActionView;
	}
	
	/**
	 * Metoda vrací zvolený actionViewer nebo false pokud je nulový
	 * @return string -- název nového actionViewru
	 */
	final public function getActionView() {
		return $this->actionViewer;
	}
	
	
	
//	final public function &addEPlugin($pluginName, $objectName){
////		První písmeno velké
//		$className = ucfirst($pluginName);
//		if(!array_key_exists($objectName, $this->eplugins)){
//			if(class_exists($className)){
//				$this->eplugins[$objectName] = new $className();
//				//			$this->eplugins[$objectName] = new Scroll();
//				$return = $this->getEPlugin($pluginName);
//			}
//		} else {
//			new CoreException(_("Název toho objektu již existuje"), 3);
//		}
//		
//		echo "<pre>in ";
//		print_r($this->eplugins[$objectName]);
//		echo "</pre>";
//		
//		return $return;
//	}
	
	/**
	 * Metoda vrací objekt zadaného epluginu
	 *
	 * @param string -- název epluginu
	 * @return object -- vrací odkaz na objekt
	 */
//	final public function &getEPlugin($epluginName) {
//		if(array_key_exists($epluginName, $this->eplugins)){
//			return $this->eplugins[$epluginName];
//		} else {
//			new CoreException(_("Objekt zadaného epluginu neexistuje"), 4);
//			return false;
//		}
//		
////		return $this->eplugins[$epluginName];
//	}
	
}

?>