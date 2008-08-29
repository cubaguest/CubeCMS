<?php
/**
 * Třída pro obsluhu autorizace
 * určuje jestli je uživatel přihlášen
 * a provádí operace s přihlášením
 */

class Auth {
	/**
	 * Konstanta s parametrem s názvem tabulky uživatelů
	 * @var string
	 */
	const CONFIG_USERS_TABLE_NAME = 'users_table';
	const CONFIG_GROUPS_TABLE_NAME = 'groups_table';
	
	/**
	 * Konstanty označující informace o uživateli
	 * @var string
	 */
	const USER_NAME			= 'username';
	const USER_MAIL			= 'mail';
	const USER_ID			= 'id_user';
	const USER_ID_GROUP		= 'id_group';
	const USER_GROUP_NAME	= 'group_name';
	const USER_LOGIN_TIME	= 'logintime';
	const USER_IS_LOGIN		= 'login';
	const USER_LOGIN_ADDRESS= 'ip_address';

	/**
	 * Proměná - název session pro ukládání auth údajů
	 * @var string
	 * @deprecated 
	 */
	private $_sessionName = "LOGIN_SESSION";

	/**
	 * Je-li uživatel přihlášen
	 * @var boolean
	 */
	private $login = false;

	/**
	 * Skupina uživatele
	 * @var string
	 */
	private $userGroupName = null;

	/**
	 * Skupina uživatele
	 * @var integer
	 */
	private $userGroupId = null;

	/**
	 * Id uživatele
	 * @var integer
	 */
	private $userId = null;

	/**
	 * Uživatelské jméno uživatele
	 * @var string
	 */
	private $userName = null;

	/**
	 * Mail uživatele
	 * @var string
	 */
	private $userMail = null;

	/**
	 * Objekt s chybami uživatele
	 * @var Messages
	 */
	private $userErrors = null;
	
	
	/**
	 * Konektor na databázi
	 * @var DbConnector
	 */
	private $dbConnector = null;

	/**
	 * Pole s konfiguracemi
	 * @var Config
	 */
	private $config = null;

	/**
	 * Konstruktor, provádí autorizaci
	 */
	function __construct($dbConnector, Config $config, Messages &$errors) {
		//Zakladni proměne
		$this->login = false;

		//konfigurační soubor
		$this->config = $config;
//		chbové zprávy
		$this->userErrors = $errors;
//		inicializace konektoru k db
		$this->dbConnector = $dbConnector;
		
		$this->_sessionName = $this->config->getOptionValue("session_name");
		
//		Inicializace session
		$this->_initSession();
		

	//Jestli uzivatel prihlasen, je zvolena skupina uzivatele, v opacnem pripade vychozi skupina
		if(!$this->_userIslogIn()){
						
			if(!$this->_logInNow()){
				//přihlášení se nezdařilo
				$this->_setDefaultUserParams();
			} else {
				//Zdařilé přihlášení, uložení detaileg o uživateli do session
				$this->_saveUserDetailToSession();
			}
		} else {
//			načtení detailů
			$this->_setUserDetailFromSession();
			
			//Uživatel se odhlásil
			if($this->_logOutNow()){
				$this->_setDefaultUserParams();
			} else {
				$this->_setUserDetailFromSession();
			}
		}
	}

	/**
	 * Metoda inicializuje session pro auth
	 */
	private function _initSession() {
		
	}

	/**
	 * Metoda zjistí jesli je uživatel již přihlášen
	 * @return boolean -- true pokud je uživatel přihlášen
	 */
	private function _userIslogIn() {
		if((!empty($_SESSION[self::USER_IS_LOGIN])) AND ($_SESSION[self::USER_IS_LOGIN] == true)){
			$this->login = true;;
		} else {
			$this->login = false;
		}

		return $this->login;
	}

	/**
	 * metoda nastavuje parametry pro přihlášeného uživatele
	 */
	private function _setUserDetailFromSession() {
		$_SESSION[self::USER_LOGIN_TIME] = time();
		$this->userName = $_SESSION[self::USER_NAME];
		$this->userMail = $_SESSION[self::USER_MAIL];
		$this->userId = $_SESSION[self::USER_ID];
		$this->userGroupId = $_SESSION[self::USER_ID_GROUP];
		$this->userGroupName = $_SESSION[self::USER_GROUP_NAME];
	}
	
	/**
	 * metoda nastvuje výchozí prametry pro nepřihlášeného uživatele
	 */
	private function _setDefaultUserParams() {
		$this->userGroupId = $this->config->getOptionValue("default_id_group", "users");
		$this->userGroupName = $this->config->getOptionValue("default_group_name", "users");
		$this->userName = $this->config->getOptionValue("default_user_name", "users");
	}
	
	/**
	 * metoda ukládá parametry uživatele do session
	 */
	private function _saveUserDetailToSession() {
		$_SESSION[self::USER_NAME] = $this->userName;
		$_SESSION[self::USER_MAIL] = $this->userMail;
		$_SESSION[self::USER_ID] = $this->userId;
		$_SESSION[self::USER_ID_GROUP] = $this->userGroupId;
		$_SESSION[self::USER_GROUP_NAME] = $this->userGroupName;
		$_SESSION[self::USER_LOGIN_ADDRESS] = $_SERVER['REMOTE_ADDR'];
		$_SESSION[self::USER_LOGIN_TIME] = time();
		$_SESSION[self::USER_IS_LOGIN] = true;
	}
	
	
	
	/**
	 * Metoda ověří přihlašovací údaje a přihlásí uživatele do aplikace
	 * //TODO není dodělána
	 * @return boolean -- true pokud se uživatele podařilo přihlásit
	 */
	private function _logInNow() {
		$return = false;
		
		if (isset($_POST["login_submit"])){
			if (($_POST["login_username"] == "") and ($_POST["login_passwd"] == "")){
				$this->userErrors->addMessage(_("Byly zadány prázdné údaje"));
			} else {
//				if (!($userResult=$loginMysql->sqlQuery("SELECT u.*, g.* FROM ".MYSQL_TABEL_PREFIX."users u 
//					LEFT JOIN ".MYSQL_TABEL_PREFIX."groups g ON g.id_group = u.id_group 
//					WHERE u.username ='".$_POST["login_username"]."'", true))){
//					$error->addError(1001, "MYSQL ERROR: ".$loginMysql->getError());
//				} 
				
//				$userSql = $this->dbConnector->select()->from(array("u" =>$this->config->getOptionValue("users_table", "db_tables")))
				$userSql = $this->dbConnector->select()->from(array("u" =>$this->config->getOptionValue(self::CONFIG_USERS_TABLE_NAME, Config::SECTION_DB_TABLES)))
						->join(array("g"=>$this->config->getOptionValue(self::CONFIG_GROUPS_TABLE_NAME, Config::SECTION_DB_TABLES)), "g.id_group = u.id_group" , "left", array("*", "gname" => "name"))
						->where("u.username = '".htmlentities($_POST["login_username"],ENT_QUOTES)."'");

				$userResult = $this->dbConnector->fetchObject($userSql);
						
				if (!($userResult)){
					$this->userErrors->addMessage(_("Nepodařilo se přihlásit. Zřejmně váš účet neexistuje."));
				} 
				else {
					if (md5(htmlentities($_POST["login_passwd"],ENT_QUOTES)) == $userResult->password){
						//	Uspesne prihlaseni do systemu
						$this->login = true;
						$this->userName = $userResult->username;
						$this->userGroupId = $userResult->id_group;
						$this->userGroupName = $userResult->gname;
						$this->userId = $userResult->id_user;
						$this->userMail = $userResult->mail;
						
						if($userResult->foto_file != null){
							//TODO není dodělána práce s fotkou
//							$_SESSION["foto_file"]=$user_details["foto_file"]=USER_AVANT_FOTO.$user["foto_file"];
						}
						
						$return = true;
						
					} else {
						$this->userErrors->addMessage(_("Bylo zadáno špatné heslo."));
					}
				}
				unset($loginMysql);

			}
		}
		
		return $return;
	}

	/**
	 * Metoda provede odhlášení z aplikace
	 * @return boolean -- true pokud se uživatel odhlásil
	 */
	private function _logOutNow() {
		
		$return = false;
		
		if(isset($_POST["logout_submit"])){
			$_SESSION[self::USER_IS_LOGIN] = false;
			$this->login = false;
			session_destroy();
			$return = true;
			
					
			$link = new Links(true);
			$link->category()->action()->article()->params()->reload();
			
		}
		
		return $return;

	}

	/**
	 * Metoda vrací je-li uživatel přihlášen
	 *
	 * @return boolean -- je li uživatel přihlášen
	 */
	function isLogin() {
		return $this->login;
	}
	
	/**
	 * Metoda vrací název skupiny ve které je uživatel
	 * @return string -- název skupiny
	 */
	public function getGroupName() {
		return $this->userGroupName;
	}
	
	/**
	 * Metoda vrací id skupiny ve které je uživatel
	 * @return integer -- id skupiny
	 */
	public function getGroupId() {
		return $this->userGroupId;
	}
	
	/**
	 * Metoda vrací id uživatele
	 * @return integer -- id uživatele
	 */
	public function getUserId() {
		return $this->userId;
	}
	
	/**
	 * Metoda vrací název uživatele
	 * @return string -- název uživatele
	 */
	public function getUserName() {
		return $this->userName;
	}
	
	/**
	 * Metoda vrací mail uživatele
	 * @return string -- mail uživatele
	 */
	public function getUserMail() {
		return $this->userMail;
	}
	
}

?>