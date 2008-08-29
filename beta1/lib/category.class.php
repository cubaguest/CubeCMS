<?php
/**
 * Třída obsluhuje práci s kategorií
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	Category class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: category.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída pro obsluhu zvolené kategorie
 */
class Category {
	/**
	 * Název $_GET proměné s kategorií
	 * načítá se z konstanty třídy links
	 * @var string
	 */
	const GetCategoryName = Links::GET_CATEGORY;

	/**
	 * Názvy sloupců v db tabulce
	 * @var string
	 */
	const COLUM_CAT_LABEL 	= 'clabel';
	const COLUM_SEC_LABEL 	= 'slabel';
	const COLUM_CAT_ID		= 'id_category';
	const COLUM_CAT_URLKEY	= 'urlkey';
	const COLUM_CAT_LPANEL	= 'left_panel';
	const COLUM_CAT_RPANEL	= 'right_panel';
	
	
	/**
	 * Proměná s konektorem pro připojení db
	 * @var Db
	 */
	private static $_dbConnector = null;

	/**
	 * Proměná configu
	 * @var Config
	 */
	private static $_config = null;
	
	/**
	 * název kategorie
	 * @var string
	 */

	/**
	 * proměná a autorizací přístupu
	 * @var Auth
	 */
	private static $_auth = null;

	/**
	 * Proměné s informacemi o kategorii
	 * @var string
	 */
	private static $_categoryLabel = null;
	
	/**
	 * Id kategorie
	 * @var integer
	 */
	private static $_categoryId = null;
	
	/**
	 * URL klíč kategorie
	 * @var string
	 */
	private static $_categoryUrlkey = null;
	
	/**
	 * Proměné jesli jsou zapnuty panely
	 * @var boolean
	 */
	private static $_categoryLeftPanel = false;
	private static $_categoryRightPanel = false;

	/**
	 * konstruktor načte informace o kategorii
	 *
	 * @param Db object -- konektor k databázi
	 * @param string -- název $_GET proměné s klíčem kategorie
	 */
	public static function factory(DbInterface $dbConnector, Config $config, Auth $auth, $getCategory = self::GetCategoryName) {
//		nastavení db
		self::$_dbConnector = $dbConnector;
		self::$_config = $config;
		self::$_auth = $auth;

//		vbrání kategorie
		if(isset($_GET[$getCategory])){
			self::_loadSelectedFromDb($_GET[$getCategory]);
		} else {
			self::_loadDefaultFromDb();
		}
	}

	/**
	 * metoda načte vybranou kategorii z databáze
	 * @param string -- klíč kategorie
	 */
	private static function _loadSelectedFromDb($catKey) {
		$catTable = self::$_config->getOptionValue("category_table", "db_tables");
		$secTable = self::$_config->getOptionValue("section_table", "db_tables");
		$itemsTable = self::$_config->getOptionValue("items_table", "db_tables");
//		$userNameGroup = self::$_auth->userdetail->offsetGet(Auth::USER_GROUP_NAME);
		$userNameGroup = self::$_auth->getGroupName();

		$catSelect = self::$_dbConnector->select()->from(array("cat" => $catTable), array(self::COLUM_CAT_URLKEY, "clabel" => "IFNULL(label_".Locale::getLang().", label_".Locale::getDefaultLang().")", "id_category", self::COLUM_CAT_LPANEL, self::COLUM_CAT_RPANEL))
						   ->join(array("item" => $itemsTable), "cat.id_category = item.id_category", "inner", null)
						   ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
						   ->where("cat.active = 1", "and")
						   ->where("cat.urlkey = '$catKey'", "and")
						   ->order("cat.priority", "desc")
						   ->order("clabel")
						   ->limit(0,1);

		$catArray = self::$_dbConnector->fetchObject($catSelect, true);
		
		self::$_categoryLabel = $catArray->{self::COLUM_CAT_LABEL};
		self::$_categoryId = $catArray->{self::COLUM_CAT_ID};
		self::$_categoryUrlkey = $catArray->{self::COLUM_CAT_URLKEY};
		self::$_categoryLeftPanel = $catArray->{self::COLUM_CAT_LPANEL};
		self::$_categoryRightPanel = $catArray->{self::COLUM_CAT_RPANEL};
	}

	/**
	 * metoda načte výchozí kategorii z databáze
	 */
	private static function _loadDefaultFromDb() {
		$catTable = self::$_config->getOptionValue("category_table", "db_tables");
		$secTable = self::$_config->getOptionValue("section_table", "db_tables");
		$itemsTable = self::$_config->getOptionValue("items_table", "db_tables");
		$userNameGroup = self::$_auth->getGroupName();


		$catSelect = self::$_dbConnector->select()->from(array("cat" => $catTable), array(self::COLUM_CAT_URLKEY, "clabel" => "IFNULL(label_".Locale::getLang().", label_".Locale::getDefaultLang().")", "id_category", self::COLUM_CAT_LPANEL, self::COLUM_CAT_RPANEL))
						   ->join(array("item" => $itemsTable), "cat.id_category = item.id_category", "inner", null)
						   ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
						   ->where("cat.active = 1", "and")
						   ->order("cat.priority", "desc")
						   ->order("clabel")
						   ->limit(0,1);

		$catArray = self::$_dbConnector->fetchObject($catSelect,true);
		
		self::$_categoryLabel = $catArray->{self::COLUM_CAT_LABEL};
		self::$_categoryId = $catArray->{self::COLUM_CAT_ID};
		self::$_categoryUrlkey = $catArray->{self::COLUM_CAT_URLKEY};
		self::$_categoryLeftPanel = $catArray->{self::COLUM_CAT_LPANEL};
		self::$_categoryRightPanel = $catArray->{self::COLUM_CAT_RPANEL};
	}

	/**
	 * Metoda vrací název kategorie
	 * @return string -- název kategorie
	 */
	public static function getLabel() {
		return self::$_categoryLabel;
	}

	/**
	 * Metoda vrací id kategorie
	 * @return integer -- id kategorie
	 */
	public static function getId() {
		return self::$_categoryId;
	}

	/**
	 * Metoda vrací urlkey kategorie
	 * @return string -- urlkey kategorie
	 */
	public static function getUrlKey() {
		return self::$_categoryUrlkey;
	}
	
	/**
	 * Metoda vrací jesli je zapnut levý panel
	 * @return boolena -- true pokud je panel zapnut
	 */
	public static function isLeftPanel(){
		return self::$_categoryLeftPanel;
	}

	/**
	 * Metoda vrací jesli je zapnut pravý panel
	 * @return boolena -- true pokud je panel zapnut
	 */
	public static function isRightPanel(){
		return self::$_categoryRightPanel;
	}

}

?>