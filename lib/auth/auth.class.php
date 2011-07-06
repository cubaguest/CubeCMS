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

class Auth extends TrObject {
	/**
	 * Konstanty označující informace o uživateli
	 * @var string
	 */
	const USER_NAME			= 'username';
	const USER_MAIL			= 'mail';
	const USER_ID			   = 'id_user';
	const USER_ID_GROUP		= 'id_group';
	const USER_GROUP_NAME	= 'group_name';
	const USER_LOGIN_TIME	= 'logintime';
	const USER_IS_LOGIN		= 'login';
	const USER_LOGIN_ADDRESS= 'ip_address';
	const USER_ADMIN_GROUP  = 'admin_grp';
	const USER_ADMIN        = 'admin';
	const USER_SITES        = 'sites';

   const PERMANENT_COOKIE_EXPIRE = 2678400; // 31*24*60*60

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
	 * Uživatel je Admin pro aktuální web
	 * @var string
	 */
	private static $userIsAdmin = false;

	/**
	 * Uživatel je Admin skupina
	 * @var string
	 */
	private static $userIsAdminGroup = false;

	/**
	 * Pole s podweby uživatele
	 * @var string
	 */
	private static $userSites = array();

	/**
	 * Konstruktor, provádí autorizaci
	 */
   public static function authenticate() {
		//Zakladni proměne
		self::$login = false;
   	//Jestli uzivatel prihlasen, je zvolena skupina uzivatele, v opacnem pripade vychozi skupina
		if(!self::userIslogIn()){
			if(!self::permanentLogin() AND !self::logInNow()){
				//přihlášení výchozího uživatele
				self::setDefaultUserParams();
			}
		} else {
			//Uživatel se odhlásil
			if(self::logOutNow()){
				self::setDefaultUserParams();
			} else {
				self::setUserDetailFromSession();
			}
		}
	}

	/**
	 * Metoda zjistí jesli je uživatel již přihlášen
	 * @return boolean -- true pokud je uživatel přihlášen
    * @todo přidání kontroly IP adresy proti zneužití
	 */
	private static function userIslogIn() {
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
	private static function setUserDetailFromSession() {
		$_SESSION[self::USER_LOGIN_TIME] = time();
		self::$userName = $_SESSION[self::USER_NAME];
		self::$userMail = $_SESSION[self::USER_MAIL];
		self::$userId = $_SESSION[self::USER_ID];
		self::$userGroupId = $_SESSION[self::USER_ID_GROUP];
		self::$userGroupName = $_SESSION[self::USER_GROUP_NAME];
		self::$userIsAdminGroup = $_SESSION[self::USER_ADMIN_GROUP];
		self::$userSites = $_SESSION[self::USER_SITES];
		self::$userIsAdmin = false;
      if(self::$userIsAdminGroup AND (empty(self::$userSites) OR isset(self::$userSites[VVE_SUB_SITE_DOMAIN])
         OR (VVE_SUB_SITE_DOMAIN == null AND isset(self::$userSites['www']) ) )){
         self::$userIsAdmin = true;
      }

      if(isset ($_COOKIE[VVE_SESSION_NAME.'_pl'])){
         setcookie(VVE_SESSION_NAME.'_pl', $_COOKIE[VVE_SESSION_NAME.'_pl'], time()+self::PERMANENT_COOKIE_EXPIRE,'/', Url_Request::getDomain());
      }
	}

	/**
	 * metoda nastvuje výchozí prametry pro nepřihlášeného uživatele
	 */
	private static function setDefaultUserParams() {
		self::$userGroupId = VVE_DEFAULT_ID_GROUP;
		self::$userGroupName = VVE_DEFAULT_GROUP_NAME;
		self::$userName = VVE_DEFAULT_USER_NAME;
		self::$userIsAdmin = false;
		self::$userIsAdminGroup = false;
		self::$userSites = array();
	}

	/**
	 * metoda ukládá parametry uživatele do session
	 */
	private static function saveUserDetailToSession() {
		$_SESSION[self::USER_NAME] = self::$userName;
		$_SESSION[self::USER_MAIL] = self::$userMail;
		$_SESSION[self::USER_ID]= self::$userId;
		$_SESSION[self::USER_ID_GROUP] = self::$userGroupId;
		$_SESSION[self::USER_GROUP_NAME] = self::$userGroupName;
		$_SESSION[self::USER_LOGIN_ADDRESS] = $_SERVER['REMOTE_ADDR'];
		$_SESSION[self::USER_LOGIN_TIME] = time();
		$_SESSION[self::USER_IS_LOGIN] = true;
		$_SESSION[self::USER_ADMIN] = self::$userIsAdmin;
		$_SESSION[self::USER_ADMIN_GROUP] = self::$userIsAdminGroup;
		$_SESSION[self::USER_SITES] = self::$userSites;
	}

	/**
	 * Metoda ověří přihlašovací údaje a přihlásí uživatele do aplikace
	 * @return boolean -- true pokud se uživatele podařilo přihlásit
	 */
	private static function logInNow() {
		$return = false;
		if (isset($_POST["login_submit"]) OR isset ($_POST['login_submit_x'])){
         $tr = new Translator();
			if (($_POST["login_username"] == "") and ($_POST["login_passwd"] == "")){
				AppCore::getUserErrors()->addMessage($tr->tr("Byly zadány prázdné údaje"));
			} else {
            $user = self::getUser(htmlentities($_POST["login_username"],ENT_QUOTES));
				if (!$user){
					AppCore::getUserErrors()->addMessage($tr->tr("Nepodařilo se přihlásit. Zřejmě váš účet neexistuje."));
				} else {
					if (Auth::cryptPassword(htmlentities($_POST["login_passwd"],ENT_QUOTES)) == $user->{Model_Users::COLUMN_PASSWORD}
               OR ($user->{Model_Users::COLUMN_PASSWORD_RESTORE} != null
                  AND Auth::cryptPassword(htmlentities($_POST["login_passwd"],ENT_QUOTES)) == $user->{Model_Users::COLUMN_PASSWORD_RESTORE})){
						//	Uspesne prihlaseni do systemu
						self::$login = true;
						self::$userName = $user->{Model_Users::COLUMN_USERNAME};
						self::$userGroupId = $user->{Model_Users::COLUMN_ID_GROUP};
						self::$userGroupName = $user->gname;
						self::$userId = $user->{Model_Users::COLUMN_ID};
						self::$userMail = $user->{Model_Users::COLUMN_MAIL};
						self::$userIsAdmin = false;
						self::$userIsAdminGroup = (bool)$user->{Model_Groups::COLUMN_IS_ADMIN};

                  // mačteme všechny podweby, kde má uživatel přístup
                  $modelSites = new Model_SitesGroups();
                  $sites = $modelSites->joinFK(Model_SitesGroups::COLUMN_ID_SITE)
                     ->where(Model_SitesGroups::COLUMN_ID_GROUP.' = :idg', array('idg' => $user->{Model_Users::COLUMN_ID_GROUP}))
                     ->records();

                  if($sites != false){
                     foreach($sites as $site) {
                        self::$userSites[$site->{Model_Sites::COLUMN_DOMAIN}] = $site->{Model_Sites::COLUMN_ID};
                     }
                  }
                  unset($sites);
                  unset($modelSites);
                  // Je admin                     nemá omezení                     je omezen na doménu
//                   if(self::$userIsAdminGroup AND (empty(self::$userSites) OR isset(self::$userSites[VVE_SUB_SITE_DOMAIN])) ){
//                      self::$userIsAdmin = true;
//                   }

						if($user->{Model_Users::COLUMN_FOTO_FILE} != null){
							//TODO není dodělána práce s fotkou
//							$_SESSION["foto_file"]=$user_details["foto_file"]=USER_AVANT_FOTO.$user["foto_file"];
						}
                  // pokud je použito obnovné heslo uožíme jej
                  if(Auth::cryptPassword(htmlentities($_POST["login_passwd"],ENT_QUOTES)) == $user->{Model_Users::COLUMN_PASSWORD_RESTORE}){
                     $user->{Model_Users::COLUMN_PASSWORD} = $user->{Model_Users::COLUMN_PASSWORD_RESTORE};
                     $user->{Model_Users::COLUMN_PASSWORD_RESTORE} = null;
                     $model = new Model_Users();
                     $model->save($user);
                     unset ($model);
                     AppCore::getInfoMessages()->addMessage($tr->tr("Nové heslo bylo nastaveno."));
                     Log::msg($tr->tr('Uživateli bylo obnoveno nové heslo'), null, self::$userName);
                  }
                  Log::msg($tr->tr('Uživatel byl přihlášen'), null, self::$userName);
                  // permanent login
                  if(isset ($_POST['login_permanent']) AND $_POST['login_permanent'] == 'on'){
                     setcookie(VVE_SESSION_NAME.'_pl', self::$userName.'|'.self::getBrowserIdent(), time()+self::PERMANENT_COOKIE_EXPIRE,'/', '.'.Url_Request::getDomain());
                  }
                  self::saveUserDetailToSession();
                  $link = new Url_Link();
                  $link->reload('http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
                  die(); // not needn't
                  return true;
					} else {
                  AppCore::getUserErrors()->addMessage($tr->tr("Bylo zadáno špatné heslo."));
					}
				}
			}
		}
		return false;
	}

	/**
	 * Metoda provede odhlášení z aplikace
	 * @return boolean -- true pokud se uživatel odhlásil
	 */
	private static function logOutNow() {
		$return = false;
		if(isset($_POST["logout_submit"]) OR isset ($_POST['logout_submit_x'])){
         $tr = new Translator();
			$_SESSION[self::USER_IS_LOGIN] = false;
			self::$login = false;
			session_destroy();
			$return = true;
			Log::msg($tr->tr('Uživatel byl odhlášen'), null, self::$userName);
         AppCore::getInfoMessages()->addMessage($tr->tr('Byl jste úspěšně odhlášen'));
         setcookie(VVE_SESSION_NAME.'_pl', '', time()-60*5,'/', '.'.Url_Request::getDomain()); // remove permament cookie
			$link = new Url_Link();
         $link->reload();
		}
		return $return;
	}

   /**
    * Metoda provade trvalé přihlášení do systému
    * @return bool
    */
   private static function permanentLogin() {
      if(isset ($_COOKIE[VVE_SESSION_NAME.'_pl'])){
         $data = explode('|', $_COOKIE[VVE_SESSION_NAME.'_pl']);
         $user = self::getUser($data[0]);
         if($user != false AND $data[1] == self::getBrowserIdent()){
            //	Uspesne prihlaseni do systemu
         	self::$login = true;
            self::$userName = $user->{Model_Users::COLUMN_USERNAME};
   			self::$userGroupId = $user->{Model_Users::COLUMN_ID_GROUP};
      		self::$userGroupName = $user->{Model_Users::COLUMN_GROUP_NAME};
         	self::$userId = $user->{Model_Users::COLUMN_ID};
            self::$userMail = $user->{Model_Users::COLUMN_MAIL};
            self::$userIsAdminGroup = (bool)$user->{Model_Groups::COLUMN_IS_ADMIN};
            self::$userIsAdmin = false;

            // mačteme všechny podweby, kde má uživatel přístup
            $modelSites = new Model_SitesGroups();
            $sites = $modelSites->joinFK(Model_SitesGroups::COLUMN_ID_SITE)
               ->where(Model_SitesGroups::COLUMN_ID_GROUP.' = :idg', array('idg' => $user->{Model_Users::COLUMN_ID_GROUP}))
               ->records();

            if(!empty($sites)){
               foreach($sites as $site) {
                  self::$userSites[$site->{Model_Sites::COLUMN_ID}] = $site->{Model_Sites::COLUMN_DOMAIN};
               }
            }
            unset($sites);
            unset($modelSites);

            if(self::$userIsAdminGroup AND (empty(self::$userSites) OR isset(self::$userSites[VVE_SUB_SITE_DOMAIN])
               OR (VVE_SUB_SITE_DOMAIN == null AND isset(self::$userSites['www']) ) )){
                  self::$userIsAdmin = true;
            }

            self::saveUserDetailToSession();
            return true;
         }
         setcookie(VVE_SESSION_NAME.'_pl', '', time()-60*5, '/', '.'.Url_Request::getDomain()); // remove permament cookie
         $tr = new Translator();
         Log::msg(sprintf($tr->tr('Pokus o ukradení cookie s trvalým přihlášením. IP: %s'), $_SERVER['REMOTE_ADDR']), 'Auth', $data[0]);
      }
      return false;
   }

   /**
    * Metoda vrací identifikátor prohlížeče, pro částečnou autentizaci trvalého přihlášení uživatele
    * @return string
    */
   private static function getBrowserIdent() {
      return sha1($_SERVER['HTTP_USER_AGENT'].$_SERVER['HTTP_ACCEPT_CHARSET'].$_SERVER['HTTP_ACCEPT_LANGUAGE']);
   }


   private static function getUser($username) {
      $model = new Model_Users();
      $rec = $model->joinFK(Model_Users::COLUMN_GROUP_ID, array('gname' => Model_Groups::COLUMN_NAME, Model_Groups::COLUMN_IS_ADMIN))
         ->where(Model_Users::COLUMN_USERNAME.' = :username', array('username' => $username))->record();
      return $rec;
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
	 * Metoda vrací jestli je uživatele administrátor
	 * @return bool -- true pokud je administrator
	 */
	public static function isAdmin() {
		return self::$userIsAdmin;
	}

	/**
	 * Metoda vrací pole s weby, kde je uživatel platný
	 * @return array -- pole s doménami
	 */
	public static function getUserSites() {
		return self::$userSites;
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