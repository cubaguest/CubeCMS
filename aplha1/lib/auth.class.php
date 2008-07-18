<?php
/**
 * Třída pro obsluhu autorizace
 * určuje jestli je uživatel přihlášen
 * a provádí operace s přihlášením
 */

class Auth {

	/**
	 * Konstanty označující informace o uživateli
	 * @var string
	 */
	const USER_NAME			= 'username';
	const USER_MAIL			= 'mail';
	const USER_ID			= 'id_user';
	const USER_ID_GROUP		= 'id_group';
	const USER_GROUP_NAME	= 'group_name';

	/**
	 * Proměná - název session pro ukládání auth údajů
	 * @var string
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
	function __construct($dbConnector, Config $config) {
		//Zakladni proměne
		$this->login = false;

		$this->config = $config;

//		inicializace konektoru k db
//		$this->dbConnector = new MySQLDb($dsad,$dsds,$dsds,$dsds,$dsaas);
		$this->dbConnector = $dbConnector;

		//konfigurační soubor

//$this->dbConnector->select()->from(array("t" => "test"))->where()->sqlQuery();
//echo $this->dbConnector->select()->from(array("te" => "test"), array("us" => "users", "ps" => "password"))
//								 ->join(array("jt" => "jointable"), "jt.cond = te.id", array("im" => "image", "file"))
//								 ->join("jointable", "jt2.cond = te.id", "*", "RIGHT")
//								 ->where("us = im")
//								 ->where("us = file", "OR");

//		Inicializace session
		$this->_initSession();



	//Jestli uzivatel prihlasen, je zvolena skupina uzivatele, v opacnem pripade vychozi skupina
		if(!$this->_userIslogIn()){
			$this->_logInNow();
		}

	}

	/**
	 * Metoda inicializuje session pro auth
	 */
	private function _initSession() {
		//Nastaveni session
		session_regenerate_id(); // ochrana před Session Fixation
		// 	Nastaveni limutu pro automaticke odhlaseni
		/* set the cache limiter to 'private' */

		session_cache_limiter('private');
		$cache_limiter = session_cache_limiter();

		/* set the cache expire to 30 minutes */
		session_cache_expire(30);
		$cache_expire = session_cache_expire();

		//session_set_cookie_params(1800);
		session_name($this->_sessionName);
		session_start();
	}

	/**
	 * Metoda zjistí jesli je uživatel již přihlášen
	 */
	private function _userIslogIn() {
		if((!empty($_SESSION['login'])) and ($_SESSION['login'] == true))
		{
			$this->login = true;

			$_SESSION["time"] = time();
			$this->userName = $_SESSION[self::USER_NAME];
			$this->userMail = $_SESSION[self::USER_MAIL];
			$this->userId = $_SESSION[self::USER_ID];
			$this->userGroupId = $_SESSION[self::USER_ID_GROUP];
			$this->userGroupName = $_SESSION[self::USER_GROUP_NAME];
		} else {
			$this->userGroupId = $this->config->getOptionValue("default_id_group", "users");
			$this->userGroupName = $this->config->getOptionValue("default_group_name", "users");
			$this->userName = $this->config->getOptionValue("default_user_name", "users");
		}

		return $this->login;

	}

	/**
	 * Metoda ověří přihlašovací údaje a přihlásí uživatele do aplikace
	 * //TODO není dodělána
	 *
	 */
	private function _logInNow() {
		if (isset($_POST["login_submit"])){
			if (($_POST["login_username"] == "") and ($_POST["login_passwd"] == "")){
//				$error->addError(105, $errMsg[105]);
				new CoreException("Please provide both a user name, and a password.", 150);
			} else {

				$select = $this->dbConnector->select()->from(array("u" => "users"))
													  ->join(array("g" => "groups"), "g.id_group = u.id_group", "left")
													  ->where("u.username ='".$_POST["login_username"]."'");

//				$loginMysql =  new Mysql(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWD, MYSQL_DBNAME);


				if (!($userResult=$loginMysql->sqlQuery("SELECT u.*, g.* FROM ".MYSQL_TABEL_PREFIX."users u LEFT JOIN ".MYSQL_TABEL_PREFIX."groups g ON g.id_group = u.id_group WHERE u.username ='".$_POST["login_username"]."'", true))){
					$error->addError(1001, "MYSQL ERROR: ".$loginMysql->getError());
				} else {
					if (mysql_num_rows($userResult)){
						$user=mysql_fetch_array($userResult);
						if ($_POST["login_passwd"] == $user["password"]){
							//	Uspesne prihlaseni do systemu
							$login=true;
							$_SESSION["login"] = true;
							$this->getUserName() != null ? $_SESSION["username"]=$this->getUserName() : $_SESSION["username"]=$user["username"];
							if($user["foto_file"] != null){
								//TODO není dodělána práce s fotkou
//								$_SESSION["foto_file"]=$user_details["foto_file"]=USER_AVANT_FOTO.$user["foto_file"];
							}
							$this->getUserId() != null ? $_SESSION["id_user"]=$this->getUserId() : $_SESSION["id_user"]=$user["id_user"];
							$_SESSION["group_id"]=$this->getGroupId();
							$this->getUserMail() != null ? $_SESSION["mail"]=$this->getUserMail() : $_SESSION["mail"]=$user["mail"];
							$_SESSION["group_name"]=$this->getGroupName();
							$_SESSION["time"] = time();
							$_SESSION["ip_address"] = $REMOTE_ADDR;
						} else {
							$error->addError(107, $errMsg[107]);
						}
					} else {
						$error->addError(106, $errMsg[106]);
					}
				}
				unset($loginMysql);

			}
		};
	}

	/**
	 * Metoda provede odhlášení z aplikace
	 */
	private function _logOut() {
		$_SESSION["login"] = false;
		session_destroy();
		//TODO znovunačtení stránky tak aby se nedala obnovit
//		$links->getSendHeader(null,true,true,true,true);;
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