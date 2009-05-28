<?php
/**
 * Třída pro autorizaci.
 * Třída obsluhuje přihlášení/odhlášení uživatele a práci s vlastnostmi (jméno, email, 
 * id, skupinu, atd.) přihlášeného uživatele. 
 * 
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu autorizace uživatele
 * @todo          Dodělat načítání z modelu a převést taknázvy sloupců do modelu
 */

class Auth {
	/**
	 * Konstanta s názvem tabulky uživatelů
	 */
	const CONFIG_USERS_TABLE_NAME = 'users_table';

   /**
	 * Konstanta s názvem tabulky se skupinami
	 */
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
    * Názvy některých sloupců
    */
   const COLUMN_USERNAME      = 'username';
   const COLUMN_ID_GROUP      = 'id_group';
   const COLUMN_GROUP_NAME    = 'gname';
   const COLUMN_ID_USER       = 'id_user';
   const COLUMN_USER_MAIL     = 'mail';
   const COLUMN_USER_PHOTO    = 'foto_file';
   
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
	 * Objekt pro práci se sessions
	 * @var Sessions
	 */
	private $session;
	
	/**
	 * Konektor na databázi
	 * @var DbConnector
	 */
	private $dbConnector = null;

	/**
	 * Proměná obsahuje jestli je uživatel přihlášen
	 * @var boolean
	 */
	private static $isLogIn = false;
	
	/**
	 * Konstruktor, provádí autorizaci
	 */
	function __construct($dbConnector) {
		//Zakladni proměne
		$this->login = false;
//		inicializace konektoru k db
		$this->dbConnector = $dbConnector;
//		Inicializace session
		$this->session = new Sessions();

	//Jestli uzivatel prihlasen, je zvolena skupina uzivatele, v opacnem pripade vychozi skupina
		if(!$this->_userIslogIn()){
						
			if(!$this->_logInNow()){
				//přihlášení se nezdařilo
				$this->_setDefaultUserParams();
			} else {
				//Zdařilé přihlášení, uložení detaileg o uživateli do session
				$this->_saveUserDetailToSession();
            $link = new Links();
            $link->category()->action()->article()->rmParam()->reload();
			}
		} else {
         //	načtení detailů
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
	 * Metoda zjistí jesli je uživatel již přihlášen
	 * @return boolean -- true pokud je uživatel přihlášen
	 */
	private function _userIslogIn() {
		if((!empty($_SESSION[self::USER_IS_LOGIN])) AND ($_SESSION[self::USER_IS_LOGIN] == true)){
			$this->login = true;
			self::$isLogIn = true;
		} else {
			$this->login = false;
			self::$isLogIn = false;
		}
		return $this->login;
	}

	/**
	 * metoda nastavuje parametry pro přihlášeného uživatele
	 */
	private function _setUserDetailFromSession() {
		$this->session->add(self::USER_LOGIN_TIME, time());	
		$this->userName = $this->session->get(self::USER_NAME);
		$this->userMail = $this->session->get(self::USER_MAIL);
		$this->userId = $this->session->get(self::USER_ID);
		$this->userGroupId = $this->session->get(self::USER_ID_GROUP);
		$this->userGroupName = $this->session->get(self::USER_GROUP_NAME);
	}
	
	/**
	 * metoda nastvuje výchozí prametry pro nepřihlášeného uživatele
	 */
	private function _setDefaultUserParams() {
		$this->userGroupId = AppCore::sysConfig()->getOptionValue("default_id_group", "users");
		$this->userGroupName = AppCore::sysConfig()->getOptionValue("default_group_name", "users");
		$this->userName = AppCore::sysConfig()->getOptionValue("default_user_name", "users");
	}
	
	/**
	 * metoda ukládá parametry uživatele do session
	 */
	private function _saveUserDetailToSession() {
		$this->session->add(self::USER_NAME, $this->userName);
		$this->session->add(self::USER_MAIL, $this->userMail);
		$this->session->add(self::USER_ID, $this->userId);
		$this->session->add(self::USER_ID_GROUP, $this->userGroupId);
		$this->session->add(self::USER_GROUP_NAME, $this->userGroupName);
		$this->session->add(self::USER_LOGIN_ADDRESS, $_SERVER['REMOTE_ADDR']);
		$this->session->add(self::USER_LOGIN_TIME, time());
		$this->session->add(self::USER_IS_LOGIN, true);
	}
	
	/**
	 * Metoda ověří přihlašovací údaje a přihlásí uživatele do aplikace
	 * @return boolean -- true pokud se uživatele podařilo přihlásit
	 */
	private function _logInNow() {
		$return = false;
		
		if (isset($_POST["login_submit"])){
			if (($_POST["login_username"] == "") and ($_POST["login_passwd"] == "")){
				$this->getError()->addMessage(_("Byly zadány prázdné údaje"));
			} else {
            $userSql = $this->getDb()->select()->table(AppCore::sysConfig()
               ->getOptionValue(self::CONFIG_USERS_TABLE_NAME, Config::SECTION_DB_TABLES),'user')
               ->colums(Db::COLUMN_ALL)
               ->join(array("g"=>AppCore::sysConfig()->getOptionValue(self::CONFIG_GROUPS_TABLE_NAME, 
                        Config::SECTION_DB_TABLES)),
                  array('g'=>'id_group', 'user'=> 'id_group') , Db::JOIN_LEFT,
                  array("gname" => "name", Db::COLUMN_ALL))
					->where("user.".self::COLUMN_USERNAME, htmlentities($_POST["login_username"],ENT_QUOTES));

				$userResult = $this->dbConnector->fetchObject($userSql);
						
				if (!($userResult)){
					$this->getError()->addMessage(_("Nepodařilo se přihlásit. Zřejmně váš účet neexistuje."));
				} 
				else {
					if (md5(htmlentities($_POST["login_passwd"],ENT_QUOTES)) == $userResult->password){
						//	Uspesne prihlaseni do systemu
						$this->login = true;
						self::$isLogIn = true;
						$this->userName = $userResult->{self::COLUMN_USERNAME};
						$this->userGroupId = $userResult->{self::COLUMN_ID_GROUP};
						$this->userGroupName = $userResult->{self::COLUMN_GROUP_NAME};
						$this->userId = $userResult->{self::COLUMN_ID_USER};
						$this->userMail = $userResult->{self::COLUMN_USER_MAIL};
						
						if($userResult->{self::COLUMN_USER_PHOTO} != null){
							//TODO není dodělána práce s fotkou
//							$_SESSION["foto_file"]=$user_details["foto_file"]=USER_AVANT_FOTO.$user["foto_file"];
						}
						$return = true;
					} else {
                  $this->getError()->addMessage(_("Bylo zadáno špatné heslo."));
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
			$this->session->add(self::USER_IS_LOGIN, false);
			$this->login = false;
			self::$isLogIn = false;
			session_destroy();
			$return = true;
					
			$link = new Links(true);
         $link->category()->action()->article()->rmParam()->reload();
		}
		return $return;
	}

   /**
    * Metoda vrací objekt pro přístup k DB
    * @return DbInterface
    */
   private function getDb() {
      return AppCore::getDbConnector();
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
	 * Metoda vrací jestli je uživatel přihlášen
	 * @return boolean -- true pokud je uživatel přihlášen
	 */
	public static function isLoginStatic() {
		return self::$isLogIn;
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

   /**
    * Metoda vrací objekt s chybovými zprávami
    * @return Messages -- objekt zpráv
    */
   public function getError() {
      return AppCore::getUserErrors();
   }
}
?>