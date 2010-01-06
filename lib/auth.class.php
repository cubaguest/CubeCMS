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
	 * Je-li uživatel přihlášen
	 * @var boolean
	 */
	private static $login = false;

	/**
	 * Skupina uživatele
	 * @var string
	 */
	private static $userGroupName = null;

	/**
	 * Skupina uživatele
	 * @var integer
	 */
	private static $userGroupId = null;

	/**
	 * Id uživatele
	 * @var integer
	 */
	private static $userId = null;

	/**
	 * Uživatelské jméno uživatele
	 * @var string
	 */
	private static $userName = null;

	/**
	 * Mail uživatele
	 * @var string
	 */
	private static $userMail = null;

	/**
	 * Objekt pro práci se sessions
	 * @var Sessions
	 */
	private $session;
	
	/**
	 * Proměná obsahuje jestli je uživatel přihlášen
	 * @var boolean
	 */
	private static $isLogIn = false;
	
	/**
	 * Konstruktor, provádí autorizaci
	 */
	function __construct() {
		//Zakladni proměne
		self::$login = false;
//		Inicializace session
		$this->session = new Sessions();

	//Jestli uzivatel prihlasen, je zvolena skupina uzivatele, v opacnem pripade vychozi skupina
		if(!$this->_userIslogIn()){
						
			if(!$this->_logInNow()){
				//přihlášení se nezdařilo
				$this->_setDefaultUserParams();
			} else {
				//Zdařilé přihlášení, uložení detailu o uživateli do session
				$this->_saveUserDetailToSession();
            $link = new Url_Link();
            $link->reload();
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
    * @todo přidání kontroly IP adresy proti zneužití
	 */
	private function _userIslogIn() {
		if((!empty($_SESSION[self::USER_IS_LOGIN])) AND ($_SESSION[self::USER_IS_LOGIN] == true)){
			self::$login = true;
		} else {
			self::$login = false;
		}
		return self::$login;
	}

	/**
	 * metoda nastavuje parametry pro přihlášeného uživatele
	 */
	private function _setUserDetailFromSession() {
		$this->session->add(self::USER_LOGIN_TIME, time());	
		self::$userName = $this->session->get(self::USER_NAME);
		self::$userMail = $this->session->get(self::USER_MAIL);
		self::$userId = $this->session->get(self::USER_ID);
		self::$userGroupId = $this->session->get(self::USER_ID_GROUP);
		self::$userGroupName = $this->session->get(self::USER_GROUP_NAME);
	}
	
	/**
	 * metoda nastvuje výchozí prametry pro nepřihlášeného uživatele
	 */
	private function _setDefaultUserParams() {
		self::$userGroupId = VVE_DEFAULT_ID_GROUP;
		self::$userGroupName = VVE_DEFAULT_GROUP_NAME;
		self::$userName = VVE_DEFAULT_USER_NAME;
	}
	
	/**
	 * metoda ukládá parametry uživatele do session
	 */
	private function _saveUserDetailToSession() {
		$this->session->add(self::USER_NAME, self::$userName);
		$this->session->add(self::USER_MAIL, self::$userMail);
		$this->session->add(self::USER_ID, self::$userId);
		$this->session->add(self::USER_ID_GROUP, self::$userGroupId);
		$this->session->add(self::USER_GROUP_NAME, self::$userGroupName);
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
				AppCore::getUserErrors()->addMessage(_("Byly zadány prázdné údaje"));
			} else {
            $model = new Model_Users();
            $userResult = $model->getUser(htmlentities($_POST["login_username"],ENT_QUOTES));
				if (!($userResult)){
					AppCore::getUserErrors()->addMessage(_("Nepodařilo se přihlásit. Zřejmně váš účet neexistuje."));
				} else {
					if (Auth::cryptPassword(htmlentities($_POST["login_passwd"],ENT_QUOTES)) == $userResult->{Model_Users::COLUMN_PASSWORD}){
						//	Uspesne prihlaseni do systemu
						self::$login = true;
						self::$userName = $userResult->{Model_Users::COLUMN_USERNAME};
						self::$userGroupId = $userResult->{Model_Users::COLUMN_ID_GROUP};
						self::$userGroupName = $userResult->{Model_Users::COLUMN_GROUP_NAME};
						self::$userId = $userResult->{Model_Users::COLUMN_ID};
						self::$userMail = $userResult->{Model_Users::COLUMN_MAIL};
						
						if($userResult->{Model_Users::COLUMN_FOTO_FILE} != null){
							//TODO není dodělána práce s fotkou
//							$_SESSION["foto_file"]=$user_details["foto_file"]=USER_AVANT_FOTO.$user["foto_file"];
						}
						$return = true;
					} else {
                  AppCore::getUserErrors()->addMessage(_("Bylo zadáno špatné heslo."));
					}
				}
				unset ($loginMysql);
            unset ($model);
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
			self::$login = false;
			session_destroy();
			$return = true;
					
			$link = new Url_Link();
         $link->reload();
		}
		return $return;
	}

	/**
	 * Metoda vrací je-li uživatel přihlášen
	 *
	 * @return boolean -- je li uživatel přihlášen
	 */
	public static function isLogin() {
		return self::$login;
	}
	
	/**
	 * Metoda vrací jestli je uživatel přihlášen
	 * @return boolean -- true pokud je uživatel přihlášen
    * @deprecated
	 */
	public static function isLoginStatic() {
		return self::$login;
	}
	
	/**
	 * Metoda vrací název skupiny ve které je uživatel
	 * @return string -- název skupiny
	 */
	public static function getGroupName() {
		return self::$userGroupName;
	}
	
	/**
	 * Metoda vrací id skupiny ve které je uživatel
	 * @return integer -- id skupiny
	 */
	public static function getGroupId() {
		return self::$userGroupId;
	}
	
	/**
	 * Metoda vrací id uživatele
	 * @return integer -- id uživatele
	 */
	public static function getUserId() {
		return self::$userId;
	}
	
	/**
	 * Metoda vrací název uživatele
	 * @return string -- název uživatele
	 */
	public static function getUserName() {
		return self::$userName;
	}
	
	/**
	 * Metoda vrací mail uživatele
	 * @return string -- mail uživatele
	 */
	public static function getUserMail() {
		return self::$userMail;
	}

   /**
    * Metoda provede zašifrování hesla
    * @param string $pass -- heslo raw
    * @return string -- šifrované heslo
    */
   public static function cryptPassword($pass) {
      return sha1($pass);
   }
}
?>