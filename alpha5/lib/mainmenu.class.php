<?php
/**
 * Abstraktní třída pro vytvoření hlavního menu
 */
abstract class MainMenu {
	/**
	 * Název pole s proměnými v šabloně
	 * @var string
	 */
	const TPL_ARRAY_NAME = 'MAIN_MENU';
	
	/**
	 * Objekt s konfiguračními volbami enginu
	 * @var Config
	 */
	private $config = null;
	
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
	 * Konstruktor
	 *
	 * @param Config -- objekt s konfiguracemi
	 * @param Db -- objekt databázového konektoru
	 */
	function __construct(Config $config, Db $dbConnector, Auth $auth) {
		$this->config = $config;
		$this->dbConnector = $dbConnector;
		$this->auth = $auth;
		$this->template = new Template();
	}
	
	public function controller() {
		$catTable = $this->config->getOptionValue("category_table", "db_tables");
		$secTable = $this->config->getOptionValue("section_table", "db_tables");
		$itemsTable = $this->config->getOptionValue("items_table", "db_tables");
//		$userNameGroup = $this->auth->userdetail->offsetGet(Auth::USER_GROUP_NAME);
		$userNameGroup = $this->auth->getGroupName();

		$menuSelect = $this->dbConnector->select()
						   ->from(array("cat" => $catTable), array("urlkey", "clabel" => "IFNULL(cat.label_".Locale::getLang().", cat.label_".Locale::getDefaultLang().")", "id_category", "alt" => "IFNULL(cat.alt_".Locale::getLang().", cat.alt_".Locale::getDefaultLang().")"))
						   ->join(array("s" => $secTable), "s.id_section = cat.id_section", null, array("id_section", "slabel" => "IFNULL(s.label_".Locale::getLang().", s.label_".Locale::getDefaultLang().")", "salt" => "IFNULL(s.alt_".Locale::getLang().", s.alt_".Locale::getDefaultLang().")"))
						   ->join(array("item" => $itemsTable), "cat.id_category = item.id_category", null, null)
						   ->where(Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
						   ->where("cat.active = 1", "and")
						   ->group("cat.id_category")
						   ->order("s.priority", "desc")
						   ->order("slabel")
						   ->order("cat.priority", "desc")
						   ->order("clabel");


		$this->menuArray = $this->dbConnector->fetchAssoc($menuSelect);
		
		if(empty($this->menuArray)){
			new CoreException("Nepodařilo se nahrát hlavní menu z databáze", 2);
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
//		Template::addMainVar($varName, $varValue, self::TPL_ARRAY_NAME);
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
	
}

?>