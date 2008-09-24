<?php
/**
 * Abstraktní třída pro práci s panely
 * 
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	Panel class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: panel.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Abstraktní třída pro práci s panely
 */
abstract class Panel{

	/**
	 * Objekt pro práci s odkazy
	 * @var Links
	 */
	private $_link = null;
	
	/**
	 * Objekt pro práci s Db
	 * @var DbInterface
	 */
	private $_db = null;
	
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
	 * Objekt s informačními hláškami
	 * @var Messages
	 */
	private $_infoMsg = null;

	/**
	 * Objekt s chbovými hláškami
	 * @var Messages
	 */
	private $_errMsg = null;
	
	/**
	 * Konstruktor
	 */
	function __construct(Module $module, DbInterface $db, $category, Messages &$messages, Messages &$errors, Template &$template, Rights $rights) {
		$this->_link = new Links(true);
		$this->_db = $db;
		$this->_category = $category;
		$this->_module = $module;
		$this->_template = $template;
		$this->_article = new Article();
		$this->_rights = $rights;
		
//		Načtení souboru s akcemi modulu
		if(class_exists("ModuleAction")){
			$this->_action = new ModuleAction($this->getModule(), $this->_article);
		}
		
//		Načtení souboru s cestami (routes) modulu
		if(class_exists("ModuleRoutes")){
			$artcle = $this->getArticle();
			$this->_routes = new ModuleRoutes($artcle);
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
		return $this->_db;
	}
	
	/**
	 * Metoda vrací odkaz na objekt pro práci s odkazy
	 * @return Links -- objekt pro práci s odkazy
	 */
	final public function getLink($clear = true) {
		$link = new Links($clear);
		return $link->category($this->_category[Category::COLUM_CAT_URLKEY]);
	}
	
	/**
	 * Metody vrací objekt modulu
	 * @return Module -- objekt modulu
	 */
	final public function getModule() {
		return $this->_module;
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
		return $this->_infoMsg;
	}

	/**
	 * Metoda vrací objekt s chybovými zprávami
	 * @return Messages -- objekt zpráv
	 */
	final public function errMsg() {
		return $this->_errMsg;
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