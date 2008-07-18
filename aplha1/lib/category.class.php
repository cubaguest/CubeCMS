<?php
/**
 * Třída obsluhuje práci s kategorií
 *
 */
class Category {
	/**
	 * Název $_GET proměné s kategorií
	 * načítá se z konstanty třídy links
	 * @var string
	 */
//	const GetCategoryName = 'category';
	const GetCategoryName = Links::GET_CATEGORY;

	/**
	 * Proměná s konektorem pro připojení db
	 * @var Db
	 */
	private $_dbConnector = null;

	/**
	 * Proměná configu
	 * @var Config
	 */
	private $_config = null;

	/**
	 * proměná a autorizací přístupu
	 * @var Auth
	 */
	private $_auth = null;

	/**
	 * Proměné s informacemi o kategorii
	 * @var string
	 */
	private $_categoryLabel = null;
	private $_categoryId = null;
	private $_categoryUrlkey = null;

	/**
	 * konstruktor načte informace o kategorii
	 *
	 * @param Db object -- konektor k databázi
	 * @param string -- název $_GET proměné s klíčem kategorie
	 */
	function __construct(Db $dbConnector, Config $config, Auth $auth, $getCategory = self::GetCategoryName) {
//		nastavení db
		$this->_dbConnector = $dbConnector;
		$this->_config = $config;
		$this->_auth = $auth;

//		vbrání kategorie
		if(isset($_GET[$getCategory])){
			$this->_loadSelectedFromDb($_GET[$getCategory]);
		} else {
			$this->_loadDefaultFromDb();
		}
	}

	/**
	 * metoda načte vybranou kategorii z databáze
	 * @param string -- klíč kategorie
	 */
	private function _loadSelectedFromDb($catKey) {
		$catTable = $this->_config->getOptionValue("category_table", "db_tables");
		$secTable = $this->_config->getOptionValue("section_table", "db_tables");
		$itemsTable = $this->_config->getOptionValue("items_table", "db_tables");
//		$userNameGroup = $this->_auth->userdetail->offsetGet(Auth::USER_GROUP_NAME);
		$userNameGroup = $this->_auth->getGroupName();

		$catSelect = $this->_dbConnector->select()->from(array("cat" => $catTable), array("urlkey", "clabel" => "label", "id_category"))
						   ->join(array("item" => $itemsTable), "cat.id_category = item.id_category", "inner", null)
						   ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
						   ->where("cat.active = 1", "and")
						   ->where("cat.urlkey = '$catKey'", "and")
						   ->order("cat.priority", "desc")
						   ->order("clabel")
						   ->limit(0,1);

		$catArray = $this->_dbConnector->fetchAssoc($catSelect);
		$catArray = $catArray[0];
		$this->_categoryLabel = $catArray["clabel"];
		$this->_categoryId = $catArray["id_category"];
		$this->_categoryUrlkey = $catArray["urlkey"];
	}

	/**
	 * metoda načte výchozí kategorii z databáze
	 */
	private function _loadDefaultFromDb() {
		$catTable = $this->_config->getOptionValue("category_table", "db_tables");
		$secTable = $this->_config->getOptionValue("section_table", "db_tables");
		$itemsTable = $this->_config->getOptionValue("items_table", "db_tables");
		$userNameGroup = $this->_auth->getGroupName();


		$catSelect = $this->_dbConnector->select()->from(array("cat" => $catTable), array("urlkey", "clabel" => "label", "id_category"))
						   ->join(array("item" => $itemsTable), "cat.id_category = item.id_category", "inner", null)
						   ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
						   ->where("cat.active = 1", "and")
						   ->order("cat.priority", "desc")
						   ->order("clabel")
						   ->limit(0,1);

		$catArray = $this->_dbConnector->fetchAssoc($catSelect);
		$catArray = $catArray[0];
		$this->_categoryLabel = $catArray["clabel"];
		$this->_categoryId = $catArray["id_category"];
		$this->_categoryUrlkey = $catArray["urlkey"];

	}

	/**
	 * Metoda vrací název kategorie
	 * @return string -- název kategorie
	 */
	public function getLabel() {
		return $this->_categoryLabel;
	}

	/**
	 * Metoda vrací id kategorie
	 * @return integer -- id kategorie
	 */
	public function getId() {
		return $this->_categoryId;
	}

	/**
	 * Metoda vrací urlkey kategorie
	 * @return string -- urlkey kategorie
	 */
	public function getUrlKey() {
		return $this->_categoryUrlkey;
	}


}

?>