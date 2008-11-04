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
	 * Odělovač parametrů v pramaterech kategorie
	 * @var string
	 */
	const CAT_PARAMS_SEPARATOR = ';';
	
	/**
	 * Názvy sloupců v db tabulce
	 * @var string
	 */
	const COLUM_CAT_LABEL 	= 'clabel';
	const COLUM_SEC_LABEL 	= 'slabel';
	const COLUM_CAT_ID		= 'id_category';
	const COLUM_SEC_ID		= 'id_section';
	const COLUM_CAT_URLKEY	= 'urlkey';
	const COLUM_CAT_LPANEL	= 'left_panel';
	const COLUM_CAT_RPANEL	= 'right_panel';
	const COLUM_CAT_PARAMS	= 'params';
	
	
	/**
	 * Proměná s konektorem pro připojení db
	 * @var Db
	 */
	private static $_dbConnector = null;

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
	 * Id Sekce
	 * @var int
	 */
	private static $_sectionId = null;
	
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
	 * názvem sekce
	 * @var string
	 */
	private static $_sectionName = null;
	
	/**
	 * Pole s parametry kategorie
	 * @var array
	 */
	private static $_categoryParams = array();
	
	/**
	 * konstruktor načte informace o kategorii
	 *
	 * @param Db object -- konektor k databázi
	 * @param string -- název $_GET proměné s klíčem kategorie
	 */
	public static function factory(DbInterface $dbConnector, Auth $auth, $getCategory = self::GetCategoryName) {
//		nastavení db
		self::$_dbConnector = $dbConnector;
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
		$catTable = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
		$secTable = AppCore::sysConfig()->getOptionValue("section_table", "db_tables");
		$itemsTable = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
//		$userNameGroup = self::$_auth->userdetail->offsetGet(Auth::USER_GROUP_NAME);
		$userNameGroup = self::$_auth->getGroupName();

		$catSelect = self::$_dbConnector->select()->from(array("cat" => $catTable), array(self::COLUM_CAT_URLKEY, "clabel" => "IFNULL(cat.label_".Locale::getLang().", cat.label_".Locale::getDefaultLang().")", "id_category", self::COLUM_CAT_LPANEL, self::COLUM_CAT_RPANEL, self::COLUM_SEC_ID, self::COLUM_CAT_PARAMS))
						   ->join(array("item" => $itemsTable), "cat.id_category = item.id_category", "inner", null)
						   ->join(array("sec" => $secTable), "cat.id_section = sec.id_section", "inner", array("slabel" => "IFNULL(sec.label_".Locale::getLang().", sec.label_".Locale::getDefaultLang().")"))
						   ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
						   ->where("cat.active = 1", "and")
						   ->where("cat.urlkey = '$catKey'", "and")
						   ->order("sec.priority", "desc")
						   ->order("cat.priority", "desc")
						   ->order("clabel")
						   ->limit(0,1);

		$catArray = self::$_dbConnector->fetchObject($catSelect, true);
		
//		Pokud nebyla načtena žádná kategorie
		if(empty($catArray)){
			$link = new Links(true);
			$link->category()->reload();
		}
		
		self::$_categoryLabel = $catArray->{self::COLUM_CAT_LABEL};
		self::$_categoryId = $catArray->{self::COLUM_CAT_ID};
		self::$_sectionId = $catArray->{self::COLUM_SEC_ID};
		self::$_categoryUrlkey = $catArray->{self::COLUM_CAT_URLKEY};
		self::$_categoryLeftPanel = $catArray->{self::COLUM_CAT_LPANEL};
		self::$_categoryRightPanel = $catArray->{self::COLUM_CAT_RPANEL};
		self::$_sectionName = $catArray->{self::COLUM_SEC_LABEL};
		self::parseParams($catArray->{self::COLUM_CAT_PARAMS});
	}

	/**
	 * metoda načte výchozí kategorii z databáze
	 */
	private static function _loadDefaultFromDb() {
		$catTable = AppCore::sysConfig()->getOptionValue("category_table", "db_tables");
		$secTable = AppCore::sysConfig()->getOptionValue("section_table", "db_tables");
		$itemsTable = AppCore::sysConfig()->getOptionValue("items_table", "db_tables");
		$userNameGroup = self::$_auth->getGroupName();


		$catSelect = self::$_dbConnector->select()->from(array("cat" => $catTable), array(self::COLUM_CAT_URLKEY, "clabel" => "IFNULL(cat.label_".Locale::getLang().", cat.label_".Locale::getDefaultLang().")", "id_category", self::COLUM_CAT_LPANEL, self::COLUM_CAT_RPANEL, self::COLUM_SEC_ID, self::COLUM_CAT_PARAMS))
						   ->join(array("item" => $itemsTable), "cat.id_category = item.id_category", "inner", null)
						   ->join(array("sec" => $secTable), "cat.id_section = sec.id_section", "inner", array("slabel" => "IFNULL(sec.label_".Locale::getLang().", sec.label_".Locale::getDefaultLang().")"))
						   ->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
						   ->where("cat.active = 1", "and")
						   ->order("sec.priority", "desc")
						   ->order("cat.priority", "desc")
						   ->order("clabel")
						   ->limit(0,1);

		$catArray = self::$_dbConnector->fetchObject($catSelect,true);
		
//		Pokud nebyla načtena žádná kategorie
		if(empty($catArray)){
			$link = new Links(true);
			$link->category()->reload();
		}
		
		self::$_categoryLabel = $catArray->{self::COLUM_CAT_LABEL};
		self::$_categoryId = $catArray->{self::COLUM_CAT_ID};
		self::$_sectionId = $catArray->{self::COLUM_SEC_ID};
		self::$_categoryUrlkey = $catArray->{self::COLUM_CAT_URLKEY};
		self::$_categoryLeftPanel = $catArray->{self::COLUM_CAT_LPANEL};
		self::$_categoryRightPanel = $catArray->{self::COLUM_CAT_RPANEL};
		self::$_sectionName = $catArray->{self::COLUM_SEC_LABEL};
		self::parseParams($catArray->{self::COLUM_CAT_PARAMS});
	}

	/**
	 * Metoda parsuje parametry kategorie a uloží je do pole
	 *
	 * @param string -- řetězec s paramaetry
	 */
	private static function parseParams($params){
		if ($params != null){
			$arrayValues = array();
			$arrayValues = explode(self::CAT_PARAMS_SEPARATOR, $params);
//			print_r($arrayValues);

			foreach ($arrayValues as $value) {
				$tmpArrayValue = explode("=", $value);
				self::$_categoryParams[$tmpArrayValue[0]]=$tmpArrayValue[1];
			}
		}
//		print_r(self::$_categoryParams);
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
	 * Metoda vrací id sekce kategorie
	 * @return integer -- id sekce kategorie
	 */
	public static function getSectionId() {
		return self::$_sectionId;
	}

	/**
	 * Metoda vrací název sekce kategorie
	 * @return string -- název sekce kategorie
	 */
	public static function getSectionLabel() {
		return self::$_sectionName;
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

	/**
	 * Metoda vrací požedovaný parametr
	 * @param string -- index parametru
	 * @return string -- parametr
	 */
	public static function getParam($param) {
		if(isset(self::$_categoryParams[$param])){
			return self::$_categoryParams[$param];
		} else {
			return null;
		}
	}
	
}

?>