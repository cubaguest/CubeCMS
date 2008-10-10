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
	 * Objekty se základními vlastnostmi modulu
	 * @var Module
	 */
	private $module = null;

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
	 * Objekt s šablonou
	 * @var Template
	 */
	private $viewTemplate = null;
	
	/**
	 * Objek kontejneru pro přenos dat do viewru
	 * @var Container
	 */
	private $container = null;
	
	/**
	 * Konstruktor třídy vytvoří a naplní základní vlastnosti pro modul
	 *
	 * @param Module -- objekt modulu
	 * @param unknown_type $aplicationMainDir //TODO
	 */
	function __construct(Module $module, ModuleAction $action, Rights $rights, Messages &$messages, Messages &$errors, Article $article) {
		
		//TODO
		$this->module = $module;
		$this->action = $action;
		$this->auth = $rights->getAuth();
		$this->rights = $rights;
		$this->infomsg = $messages;
		$this->errmsg = $errors;
		$this->article = $article;
		$this->container = new Container();
	}
	
	/**
	 * Metoda vrací objekt systémové konfigurace
	 * @return Config -- objekt systémové konfigurace
	 */
	final public function getSysConfig(){
		return AppCore::sysConfig();
	}
	
	/**
	 * Metoda vrací odkaz na objekt pro práci s odkazy
	 * @return Links -- objekt pro práci s odkazy
	 */
	final public function getLink($clear = false) {
		return new Links($clear);
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
	 * Metoda vrací objekt kontaineru pro přenos dat mezi controlerem a viewrem
	 * 
	 * @return Container -- objekt kontaineru
	 */
	final public function container() {
		return $this->container;
	}
	
	
	/**
	 * Vrací objekt modulu
	 * @return Module -- objekt modulu
	 */
	function __destruct() {
	}

	/**
	 * Hlavní metoda třídy kontroleru, provádí se pokud není žádná akce
	 */
	abstract function mainController();

	/**
	 * Metoda vytvoří objekt engine pluginu
	 *
	 * @param string -- název e-pluginu
	 * 
	 * @return Objekt -- vytvořený objekt epluginu 
	 */
//	final public function addEPlugin($pluginName){
////		První písmeno velké
//		$className = ucfirst($pluginName);
//			if(class_exists($className)){
//				return new $className($this->getLink());
//			}
//	}
	
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
		return new Eplugin($this->getModule(), $this->getRights(), $this->errMsg(), $this->infoMsg());
		
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
			$this->errMsg()->addMessage(_("Nemáte dostatčná práva pro přístup ke kategorii nebo jste byl(a) odhlášen(a)"), true);
			$this->getLink(true)->reload();
		}
	}
	/**
	 * Metoda kontroluje práva pro zápis do modulu. v opačném případě vyvolá přesměrování
	 */
	final public function checkWritebleRights() {
		if(!$this->getRights()->isWritable()){
			$this->errMsg()->addMessage(_("Nemáte dostatčná práva pro přístup ke kategorii nebo jste byl(a) odhlášen(a)"), true);
			$this->getLink(true)->reload();
		}
	}
	/**
	 * Metoda kontroluje práva pro plný přístup k modulu. v opačném případě vyvolá přesměrování
	 */
	final public function checkControllRights() {
		if(!$this->getRights()->isControll()){
			$this->errMsg()->addMessage(_("Nemáte dostatčná práva pro přístup ke kategorii nebo jste byl(a) odhlášen(a)"), true);
			$this->getLink(true)->reload();
		}
	}
	
	/**
	 * metoda nastaví název použitého viewru
	 * 
	 * @param string -- název viewru
	 */
	final public function setView($view) {
		$this->actionViewer = $view;
	}
	
		
	/**
	 * Metoda spustí viewer modulu
	 * 
	 * @param Template -- objetk šablony (odkaz)
	 */
	final public function runView(&$template) {
		$this->viewTemplate = $template;


		$viewClassName = ucfirst($this->getModule()->getName()).'View';
		if(class_exists($viewClassName)){

//			//				Volba pohledu
//			$this->actionViewer.= AppCore::MODULE_VIEWER_SUFIX;
//			if($changedView == null){
//				$viewAction = $routes->getRoute().$actionCtrl.AppCore::MODULE_VIEWER_SUFIX;
//			} else {
//				//TODO dořešit při zapnuté cestě
//				$viewAction = $routes->getRoute().$changedView.AppCore::MODULE_VIEWER_SUFIX;
//			}

//			Doplnění sufixu View
			if (strpos($this->actionViewer, AppCore::MODULE_VIEWER_SUFIX) === false) {
				$this->actionViewer .= AppCore::MODULE_VIEWER_SUFIX;
			}

			//					Vytvoření objektu pohledu
			$view = new $viewClassName($this->getModule(), $this->getRights(), $this->viewTemplate, $this->container());

			//	Zvolení překladu na modul
//			textdomain($module->getName());

			if(($this->getAction()->haveAction() OR $this->actionViewer != null) AND method_exists($view, $this->actionViewer)){//TODO doladit jesli se správně dělají akce
			//					if(method_exists($controller, $controllerAction)){
				$view->{$this->actionViewer}();
			}
			else if(!$this->getAction()->isAction() AND $this->getArticle()->isArticle() AND method_exists($view, $this->actionViewer)){
				$view->{$this->actionViewer}();
			}
			else {
				$view->mainView();
				if(!method_exists($view, $this->actionViewer)){
					//				přepnutí překladu na engine
					textdomain(AppCore::GETTEXT_DEFAULT_DOMAIN);
					new CoreException(_("Action Viewer ").$this->actionViewer._(" v modulu ") . $this->getModule()->getName(). _(" nebyl nalezen"), 11);
				}
			}
//			 else {
//				if(!method_exists($view, $viewAction)){
//					//				přepnutí překladu na engine
//					textdomain(AppCore::GETTEXT_DEFAULT_DOMAIN);
//					new CoreException(_("Action Viewer ").$viewAction._(" v modulu ") . $module->getName(). _(" nebyl nalezen"), 13);
//				}
//			}

		} else {
			new CoreException(_("Nepodařilo se vytvořit objekt view modulu ") . $this->getModule()->getName(), 8);
		};
	}
}

?>