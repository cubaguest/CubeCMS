<?php
/**
 * Abstraktní třída pro vytvoření hlavního menu
 * 
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	MainMenu class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: mainmenu.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída pro vytvoření hlavního menu
 */
abstract class MainMenu {
	/**
	 * Název pole s proměnými v šabloně
	 * @var string
	 */
	const TPL_ARRAY_NAME = 'MAIN_MENU';
	
	/**
	 * Objekt s databázovým kontrolerem
	 * @var Db
	 */
	private $dbConnector = null;
	
	/**
	 * Objekt s informacemi o autorizaci
	 * @var Auth
	 */
	private $auth = null;
	
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
	 * Konstruktor
	 *
	 * @param Db -- objekt databázového konektoru
	 */
	function __construct(Db $dbConnector, Auth $auth) {
		$this->dbConnector = $dbConnector;
		$this->auth = $auth;
		$this->template = new Template();
		
		$this->tablesSections = AppCore::sysConfig()->getOptionValue("section_table", "db_tables");
		$this->tablesCategories = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
		$this->tablesItems = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
	}
	
	public function controller() {
		$userNameGroup = $this->auth->getGroupName();

		$menuSelect = $this->dbConnector->select()
						   ->from(array("cat" => $this->tablesCategories), array("urlkey", "clabel" => "IFNULL(cat.label_".Locale::getLang().", cat.label_".Locale::getDefaultLang().")", "id_category", "alt" => "IFNULL(cat.alt_".Locale::getLang().", cat.alt_".Locale::getDefaultLang().")"))
						   ->join(array("s" => $this->tablesSections), "s.id_section = cat.id_section", null, array("id_section", "slabel" => "IFNULL(s.label_".Locale::getLang().", s.label_".Locale::getDefaultLang().")", "salt" => "IFNULL(s.alt_".Locale::getLang().", s.alt_".Locale::getDefaultLang().")"))
						   ->join(array("item" => $this->tablesItems), "cat.id_category = item.id_category", null, null)
						   ->where(Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
						   ->where("cat.active = 1", "and")
						   ->group("cat.id_category")
						   ->order("s.priority", "desc")
						   ->order("slabel")
						   ->order("cat.priority", "desc")
						   ->order("clabel");


		$this->menuArray = $this->dbConnector->fetchAssoc($menuSelect);
		
		if(empty($this->menuArray)){
			new CoreException(_("Nepodařilo se nahrát hlavní menu z databáze"), 2);
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
	
	
}

?>