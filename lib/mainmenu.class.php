<?php
/**
 * Abstraktní třída hlavního menu.
 * Třída slouží pro vytvoření hlavního menu aplikace z uživatelem definované 
 * třídy pro menu, a poskytuje základní přístup k prvkům menu.
 * 
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.2 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vytvoření hlavního menu
 */

abstract class MainMenu {
	/**
	 * Název pole s proměnými v šabloně
	 * @var string
	 */
	const TPL_ARRAY_NAME = 'MAIN_MENU';
	
	/**
	 * Názva sloupců v databázi v tabulce kategorií
	 * @var string
	 */
	const COLUMN_CATEGORY_ID 			= 'id_category';
	const COLUMN_CATEGORY_ID_SECTION 	= 'id_section';
	const COLUMN_CATEGORY_URLKEY 		= 'urlkey';
	const COLUMN_CATEGORY_LABEL_PREFIX 	= 'label_';
	const COLUMN_CATEGORY_LABEL_IMAG 	= 'clabel';
	const COLUMN_CATEGORY_ALT_PREFIX 	= 'alt_';
	const COLUMN_CATEGORY_ALT_IMAG 		= 'calt';
	const COLUMN_CATEGORY_PROTECTED 	= 'protected';
	const COLUMN_CATEGORY_SHOW_IN_MENU 	= 'show_in_menu';
	const COLUMN_CATEGORY_SHOW_WHEN_LOGIN_ONLY 	= 'show_when_login_only';
	const COLUMN_CATEGORY_PRIORITY 		= 'priority';
	const COLUMN_CATEGORY_ACTIVE 		= 'active';
	
	/**
	 * Názva sloupců v databázi v tabulce sekcí
	 * @var string
	 */
	const COLUMN_SECTION_ID 			= 'id_section';
	const COLUMN_SECTION_LABEL_PREFIX 	= 'label_';
	const COLUMN_SECTION_LABEL_IMAG 	= 'slabel';
	const COLUMN_SECTION_ALT_PREFIX 	= 'alt_';
	const COLUMN_SECTION_ALT_IMAG 		= 'calt';
	const COLUMN_SECTION_PRIORITY 		= 'priority';

	/**
	 * Názvy sloupců v tabulce s itemy
	 *
	 */
	const COLUMN_ITEM_ID 			= 'id_item';
	const COLUMN_ITEM_ID_CATEGORY 	= 'id_category';
	const COLUMN_ITEM_ID_MODULE 	= 'id_module';
	const COLUMN_ITEM_LABEL 		= 'label'; //@deprecated
	const COLUMN_ITEM_ALT	 		= 'alt'; //@deprecated
	const COLUMN_ITEM_SCROLL 		= 'calt';
	const COLUMN_ITEM_PRIORITY 		= 'priority';
	
	/**
	 * Objekt s šablonou
	 * @var Template
	 */
	private $template = null;
	
	/**
	 * Pole s objekty menu
	 * @var pole
	 */
	protected $menuArray = array();
	
	/**
	 * Názvy tabulky se sekcemi
	 * @var string
	 */
	private $tablesSections = null;

	/**
	 * Názvy tabulky s kategoriemi
	 * @var string
	 */
	private $tablesCategories = null;
	
	/**
	 * Názvy tabulky s itemy
	 * @var string
	 */
	private $tablesItems = null;
	
	/**
	 * Proměná s názvem skupiny uživatele
	 * @var string
	 */
	private $userGroupName = 'guest';
	
	/**
	 * Konstruktor
	 *
	 * @param Db -- objekt databázového konektoru
	 */
	function __construct() {
		$this->template = new Template();
		
		$this->tablesSections = AppCore::sysConfig()->getOptionValue("section_table", "db_tables");
		$this->tablesCategories = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
		$this->tablesItems = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
		
		$this->userGroupName = $this->auth()->getGroupName();
	}
	
	public function controller() {
      $menuSelect = $this->getDb()->select()
      ->table($this->tablesCategories, 'cat')
      ->colums(array(self::COLUMN_CATEGORY_LABEL_IMAG => "IFNULL(cat."
					.self::COLUMN_CATEGORY_LABEL_PREFIX.Locale::getLang().", cat."
					.self::COLUMN_CATEGORY_LABEL_PREFIX.Locale::getDefaultLang().")",
					self::COLUMN_CATEGORY_ID, self::COLUMN_CATEGORY_ALT_IMAG => "IFNULL(cat."
					.self::COLUMN_CATEGORY_ALT_PREFIX.Locale::getLang().", cat."
					.self::COLUMN_CATEGORY_ALT_PREFIX.Locale::getDefaultLang().")"))

      ->join(array('sec'=>$this->tablesSections),
         array(self::COLUMN_SECTION_ID, 'cat'=>self::COLUMN_CATEGORY_ID_SECTION), null, array(self::COLUMN_SECTION_ID,
				self::COLUMN_SECTION_LABEL_IMAG => "IFNULL(sec.".self::COLUMN_SECTION_LABEL_PREFIX
				.Locale::getLang().", sec.".self::COLUMN_SECTION_LABEL_PREFIX.Locale::getDefaultLang().")",
				self::COLUMN_SECTION_ALT_IMAG => "IFNULL(sec.".self::COLUMN_SECTION_ALT_PREFIX
				.Locale::getLang().", sec.".self::COLUMN_SECTION_ALT_PREFIX.Locale::getDefaultLang().")"))
      ->join(array("item" => $this->tablesItems),
         array('cat' =>self::COLUMN_CATEGORY_ID, self::COLUMN_ITEM_ID_CATEGORY))
			->where("cat.".self::COLUMN_CATEGORY_ACTIVE, (int)true)
			->where("cat.".self::COLUMN_CATEGORY_SHOW_IN_MENU, (int)true)
			->group("cat.".self::COLUMN_CATEGORY_ID)
         ->order("sec.".self::COLUMN_SECTION_PRIORITY, Db::ORDER_DESC)
			->order(self::COLUMN_SECTION_LABEL_IMAG)
			->order("cat.".self::COLUMN_CATEGORY_PRIORITY, Db::ORDER_DESC)
			->order(self::COLUMN_CATEGORY_LABEL_IMAG);

      if(!$this->auth()->isLogin()){
         $menuSelect->where("cat.".self::COLUMN_CATEGORY_SHOW_WHEN_LOGIN_ONLY, (int)false);
      }

		$this->loadMenu($menuSelect);
	}
	
	/**
	 * Metoda provede načtení menu a přiřadí menu do pole menu pro zpracování
	 * @param DbInterface -- objekt s popisem jak se má menu načíst
	 */
	public function loadMenu($sqlSelect){
      //	Přidání výběru jen na zvolenou skupinu
      $sqlSelect = $sqlSelect->where(Rights::RIGHTS_GROUPS_TABLE_PREFIX.$this->getUserGroup(),'r__', Db::OPERATOR_LIKE);

      try {
         $this->menuArray = $this->getDb()->fetchAll($sqlSelect);
         if(empty($this->menuArray)){
            throw new RangeException(_("Nepodařilo se nahrát hlavní menu z databáze"), 2);
         }
      } catch (RangeException $e) {
         new CoreErrors($e);
      }
	}
	
	public function view() {
		$this->createMenu();
	}
	
	/**
	 * Abstraktní třída je volána při zobrazování menu
	 * je rozšířena při dědění
	 */
	abstract function createMenu();
	
	/**
	 * Metoda přiřadí proměnou do šablony
	 * @param string -- název proměnné
	 * @param mixed -- hodnota proměnné
	 */
	protected function addTpl($varName, $varValue) {
		$this->template->addVar($varName, $varValue);
	}
	
	/**
	 * Metoda vrací šablony a proměné menu
	 * @return Template
	 */
	public function getTemplate() {
		return $this->template;
	}
	
	
	/**
	 * Metoda vrací objekt pro práci s odkazy
	 * 
	 * @return Links -- objekt pro prái s odkazy
	 */
	protected function getLink() {
		return new Links(true);
	}
	
	/**
	 * Metoda vrací název tabulky s kategoriema
	 *
	 * @return string -- tabulka s kategoriemi
	 */
	public function getCatTable() {
		return $this->tablesCategories;
	}
	
	/**
	 * Metoda vrací název tabulky se sekcema
	 *
	 * @return string -- tabulka se sekcema
	 */
	public function getSecTable() {
		return $this->tablesSections;
	}
	
	/**
	 * Metoda vrací název tabulky s itemy
	 *
	 * @return string -- tabulka s itemy
	 */
	public function getItemTable() {
		return $this->tablesItems;
	}
	
	/**
	 * Metoda vrací objekt na db konektor
	 * @return DbInterface -- objekt db konektoru
	 */
	public function getDb() {
      return AppCore::getDbConnector();
	}
	
	/**
	 * Metoda vrací název skupiny uživatele
	 *
	 * @return string -- nazev skupiny
	 */
	private function getUserGroup() {
		return $this->userGroupName;
	}
	
   /**
    * Metoda vrací objekt autorizace
    * @return Auth
    */
   private function auth() {
      return AppCore::getAuth();
   }
	
}

?>