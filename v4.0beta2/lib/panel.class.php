<?php
/**
 * Abstraktní třída pro práci s panely.
 * Základní třída pro tvorbu tříd panelů jednotlivých modulu. Poskytuje prvky
 * základního přístu jak k vlastnostem modelu tak pohledu. Pomocí této třídy 
 * se také generují šablony panelů.
 * 
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Abstraktní třída pro práci s panely
 * @todo				Není implementována práce s chybami
 */

abstract class Panel{
	/**
	 * Objekt pro práci s odkazy
	 * @var Links
	 */
	private $_link = null;
	
	/**
	 * Objekt pro práci s module
	 * @var Module
	 */
	private $_module = null;
	
	/**
	 * Pole s parametry kategorie
	 * @var Array
	 */
	private $_category = null;
	
	/**
	 * Objekt s šablonovacím systémem
	 * @var Template
	 */
	private $_template = null;
	
	/**
	 * Objekt s článkem
	 * @var Article
	 */
	private $_article = null;
	
	/**
	 * Objekt s akcemi
	 * @var Action
	 */
	private $_action = null;
	
	/**
	 * Objekt s cestami
	 * @var Routes
	 */
	private $_routes = null;
	
	/**
	 * Objekt s právy uživatele
	 * @var Rights
	 */
	private $_rights = null;
	
	/**
	 * Konstruktor
	 */
	function __construct($category, Template &$template, Rights $rights) {
		$this->_link = new Links(true);
		$this->_category = $category;
		$this->_template = $template;
		$this->_article = new Article();
		$this->_rights = $rights;
		
		$action = null;
		$actionClassName = ucfirst($this->getModule()->getName()).'Action';
		
//		Pokud ještě nebyla třída načtena
		if(!class_exists($actionClassName,false)){
//			načtení souboru s akcemi modulu
			if(!file_exists('.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $this->getModule()->getName() . DIRECTORY_SEPARATOR . 'action.class.php')){
            throw new BadClassException(sprintf(_('Nepodařilo se nahrát akci modulu '), $module->getName()),1);
         }
         include '.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $this->getModule()->getName() . DIRECTORY_SEPARATOR . 'action.class.php';
		}
		if(class_exists($actionClassName)){
         $this->_action = new $actionClassName($this->getModule(), $this->_article);
		} else {
         $this->_action = new Action($getModule(), $this->_article);
		}
//		Cesty
		$routes = null;
		$routesClassName = ucfirst($this->getModule()->getName()).'Routes';		
//		Pokud ještě nebyla třída načtena
		if(!class_exists($routesClassName, false)){		
//			načtení souboru s cestami (routes) modulu
			if(!file_exists('.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $this->getModule()->getName() . DIRECTORY_SEPARATOR . 'routes.class.php')){
            throw new BadClassException(sprintf(_('Nepodařilo se nahrát akci modulu '), $module->getName()),2);
         }
         include '.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $this->getModule()->getName() . DIRECTORY_SEPARATOR . 'routes.class.php';
		}
		if(class_exists($routesClassName)){
			$this->_routes = new $routesClassName($this->_article);
		} else {
			$this->_routes = new Routes($this->_article);
		}
	}
		
	/**
	 * Metoda controleru panelu
	 */
	abstract function panelController();
	
	/**
	 * Metoda viewru panelu
	 */
	abstract function panelView();
	
	/**
	 * Metoda vrací objekt pro přístup k db
	 * @return DbInterface -- objekt databáze
	 */
	final public function getDb() {
		return AppCore::getDbConnector();
	}
	
	/**
	 * Metoda vrací odkaz na objekt pro práci s odkazy
	 * @return Links -- objekt pro práci s odkazy
	 */
	final public function getLink($clear = true) {
		$link = new Links($clear);
		return $link->category($this->_category[Category::COLUMN_CAT_LABEL],
			$this->_category[Category::COLUMN_CAT_ID]);
	}
	
	/**
	 * Metody vrací objekt modulu
	 * @return Module -- objekt modulu
	 */
	final public function getModule() {
		return AppCore::getSelectedModule();
	}
	
	/**
	 * Metoda vrací objekt na akci
	 * @return ModuleAction -- objekt akce
	 */
	final public function getAction() {
		return $this->_action;
	}

	/**
	 * Metoda vrací objekt na akci
	 * @return ModuleAction -- objekt akce
	 */
	final public function getRoute() {
		return $this->_routes;
	}
	
	/**
	 * Metoda vrací objekt s právy na modul
	 * @return Rights -- objekt práv
	 */
	final public function getRights() {
		return $this->_rights;
	}

	/**
	 * Metoda vrací objekt s článkem
	 * @return Article -- objekt článku
	 */
	final public function getArticle() {
		return $this->_article;
	}

	/**
	 * Metoda vrací objekt s informačními zprávami
	 * @return Messages -- objekt zpráv
	 */
	final public function infoMsg() {
      return AppCore::getInfoMessages();
	}

	/**
	 * Metoda vrací objekt s chybovými zprávami
	 * @return Messages -- objekt zpráv
	 */
	final public function errMsg() {
      return AppCore::getUserErrors();
	}
	
	/**
	 * Metoda vrací objekt šablony, přes kerý se přiřazují proměnné do šablony
	 * @return Template -- objekt šablony
	 */
	final public function template(){
		return $this->_template;
	}
}
?>