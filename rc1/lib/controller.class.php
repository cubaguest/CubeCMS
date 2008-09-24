<?php
/**
 * Abstraktní třída pro obsluhu kontroleru modulu
 * 
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	Action class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: controller.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Abstraktní třída kontroleru modulu
 */

//třída pro práci s modelem
//require_once ('.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. 'model.class.php');

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
	 * @param unknown_type $aplicationMainDir //TODO
	 */
	function __construct(Module $module, DbInterface $db, ModuleAction $action, Rights $rights, Messages &$messages, Messages &$errors, Article $article) {
		
		//TODO
		$this->module = $module;
		$this->db = $db;
		$this->model = new Models();
//		$this->link = new Links();
		$this->action = $action;
		$this->auth = $rights->getAuth();
		$this->rights = $rights;
		$this->infomsg = $messages;
		$this->errmsg = $errors;
		$this->article = $article;
//		Příprava a nastavení použití překladu
	}
	
	/**
	 * Metoda vrací objekt systémové konfigurace
	 * @return Config -- objekt systémové konfigurace
	 */
	final public function getSysConfig(){
		return AppCore::sysConfig();
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
		//$modelFileName = strtolower($modelName);
		$modelFileName = $modelName;

		$modelPath = $this->getModule()->getDir()->getModelsDir().$modelFileName.'.php';

		if(!file_exists($modelPath)){
			$return = null;
			new CoreException(_("Nemohu načíst model ") . $modelFileName, 1);
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
	 * @return Eplugin -- objekt EPluginu
	 */
	final public function eplugin(){
	//		První písmeno velké
//		$className = ucfirst($pluginName);
//			if(class_exists($className)){
//				return new $className($this->getLink(), $this->getDb(), $this->getModule());
//			}
		return new Eplugin($this->getDb(),$this->getModule(), $this->getRights(), $this->errMsg(), $this->infoMsg());
		
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
	
	/**
	 * Metoda kontroluje práva pro čtení modulu. v opačném případě vyvolá přesměrování
	 */
	final public function checkReadableRights() {
		if(!$this->getRights()->isReadable()){
			$this->errMsg()->addMessage(_("Nemáte dostatčná práva pro přístup ke kategorii nebo jste byl(a) odhlášen(a)"));
			$this->getLink(true)->reload();
		}
	}
	/**
	 * Metoda kontroluje práva pro zápis do modulu. v opačném případě vyvolá přesměrování
	 */
	final public function checkWritebleRights() {
		if(!$this->getRights()->isWritable()){
			$this->errMsg()->addMessage(_("Nemáte dostatčná práva pro přístup ke kategorii nebo jste byl(a) odhlášen(a)"));
			$this->getLink(true)->reload();
		}
	}
	/**
	 * Metoda kontroluje práva pro plný přístup k modulu. v opačném případě vyvolá přesměrování
	 */
	final public function checkControllRights() {
		if(!$this->getRights()->isControll()){
			$this->errMsg()->addMessage(_("Nemáte dostatčná práva pro přístup ke kategorii nebo jste byl(a) odhlášen(a)"));
			$this->getLink(true)->reload();
		}
	}
}

?>