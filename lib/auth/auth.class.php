<?php
/**
 * Třída pro autorizaci.
 * Třída obsluhuje přihlášení/odhlášení uživatele a práci s vlastnostmi (jméno, email,
 * id, skupinu, atd.) přihlášeného uživatele.
 *
 * @copyright     Copyright (c) 2008-2009 Jakub Matas
 * @version       $Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro obsluhu autorizace uživatele
 * @todo          Dodělat načítání z modelu a převést taknázvy sloupců do modelu
 */

class Auth extends TrObject {
   /**
    * Konstanty označující informace o uživateli
    * @var string
    */
   const SESSION_USER      = 'userlogin';
   
   const PERMANENT_COOKIE_EXPIRE = 2678400; // 31*24*60*60
   
   const FORM_PERMANENT_LOGIN = 'login_permanent';
   const FORM_LOGOUT = 'logout_submit';

   /**
    * @var Auth_User
    */
   protected static $user;

   /**
    * @var Auth_Interface[]
    */
   protected static $authenticators = array();

   /**
    * Je-li uživatel přihlášen
    * @var boolean
    */
   private static $login = false;

   /**
    * provede přihlášení a autentizaci uživatele
    */
   public static function authenticate() {
      $baseAuth = new Auth_Provider_Internal();
      self::addAuthenticator($baseAuth);
      self::$user = new Auth_User($baseAuth);
      
      // načti aktuální přihlášení
      self::checkUserIslogIn();
      
      if(!self::isLogin()){
         foreach (self::$authenticators as $authenticator) {
            if($authenticator->isCalled()){
               $authenticatedUser = $authenticator->authenticate();
               if($authenticatedUser){
                  self::$user = $authenticatedUser;
                  self::$login = true;
                  break;
               }
            }
         }
         
         // pokud dojde k přihlášení
         if(self::isLogin()){
            self::saveUserDetailToSession();
            self::checkEnablePermanentLogin();
            // redirect to link
            $link = new Url(isset($_GET['redirect']) ? urldecode($_GET['redirect']) : null);
            $link->rmParam()->redirect();
         }
      } else {
         // kontrola odhlášení
         self::checkLogOut();
         self::saveUserDetailToSession();
      }
      
   }
   
   public static function loginUser(Model_ORM_Record $user)
   {
      $baseAuth = self::getAuthenticator('internal');
      self::$user = new Auth_User($baseAuth, $user);
      self::$login = true;
      self::saveUserDetailToSession();
   }

   public static function addAuthenticator(Auth_Provider_Interface $auth)
   {
      self::$authenticators[strtolower(str_replace('Auth_Provider_', '', get_class($auth)))] = $auth;
   }
   
   /**
    * Zjišťuje jestli se daný autentizátor používá
    * @param string $name
    * @return boolean
    */
   public static function isAuthenticator($name)
   {
      return isset(self::$authenticators[$name]);
   }
   
   /**
    * 
    * @param type $name
    * @return Auth_Provider_Interface|boolean
    */
   public static function getAuthenticator($name = null)
   {
      $class = 'Auth_Provider_'.ucfirst($name);
      return isset(self::$authenticators[$name]) ? self::$authenticators[$name] : new $class();
   }
   
   /**
    * Varcí informace o uživateli
    * @return Auth_User
    */
   public static function getUser()
   {
      return self::$user;
   }
   

   /**
    * Metoda zjistí jesli je uživatel již přihlášen
    * @return boolean -- true pokud je uživatel přihlášen
    * @todo přidání kontroly IP adresy proti zneužití
    */
   private static function checkUserIslogIn() {
      if(isset($_SESSION[self::SESSION_USER])){
         self::$login = true;
         self::$user = $_SESSION[self::SESSION_USER];
      } else {
         self::$login = false;
         self::checkIsPermanentLogin();
      }
      return self::$login;
   }
   
   private static function checkEnablePermanentLogin()
   {
      if(self::getUser()->getAuthenticator()->isPermanentLogin()){
         self::createPermanentLogin();
      }
   }
   
   private static function checkIsPermanentLogin()
   {
      if(isset($_COOKIE[VVE_SESSION_NAME.'_pl'])){
         $cookieParts = explode('|', $_COOKIE[VVE_SESSION_NAME.'_pl']);
         if(!isset($cookieParts[1]) || $cookieParts[1] != self::getBrowserIdent()){
            return;
         }
         $user = Model_Users::getUserByID($cookieParts[0]);
         if(!$user){
            return;
         }
         
         self::$user = new Auth_User(self::getAuthenticator($user->{Model_Users::COLUMN_AUTHENTICATOR}), $user);
         self::$login = true;
         self::createPermanentLogin();
      }
   }
   
   private static function createPermanentLogin()
   {
      setcookie(VVE_SESSION_NAME.'_pl', self::$user->getUserId().'|'.self::getBrowserIdent(), time()+self::PERMANENT_COOKIE_EXPIRE,'/', '.'.Url_Request::getDomain());
   }
   
   private static function destroyPemanentLogin()
   {
      if(isset($_COOKIE[VVE_SESSION_NAME.'_pl'])){
         setcookie(VVE_SESSION_NAME.'_pl', '', time()-60*5,'/', '.'.Url_Request::getDomain());
      }
   }

   /**
    * metoda ukládá parametry uživatele do session
    */
   private static function saveUserDetailToSession() {
      $_SESSION[self::SESSION_USER] = self::$user;
   }

   private static function checkLogOut()
   {
      if(isset($_POST["logout_submit"]) OR isset ($_POST['logout_submit_x'])){
         self::logOutNow();
      }
   }

   /**
    * Metoda provede odhlášení z aplikace
    * @return boolean -- true pokud se uživatel odhlásil
    */
   private static function logOutNow() {
      $tr = new Translator();
      $_SESSION[self::SESSION_USER] = false;
      unset($_SESSION[self::SESSION_USER]);
      self::$login = false;
      session_destroy();
      Log::msg($tr->tr('Uživatel byl odhlášen'), null, self::getUser()->getUserName());
      AppCore::getInfoMessages()->addMessage($tr->tr('Byl jste úspěšně odhlášen'));
      self::destroyPemanentLogin();
      $link = new Url_Link();
      $link->redirect(isset($_GET['redirect']) ? $_GET['redirect'] : null);
      return true;
   }

   /**
    * Metoda vrací identifikátor prohlížeče, pro částečnou autentizaci trvalého přihlášení uživatele
    * @return string
    */
   private static function getBrowserIdent() {
      return sha1($_SERVER['HTTP_USER_AGENT'].$_SERVER['HTTP_ACCEPT_LANGUAGE']);
   }

   /**
    * Metoda vrací je-li uživatel přihlášen
    *
    * @return boolean -- je li uživatel přihlášen
    */
   public static function isLogin() {
      return self::getUser()->getGroupId() != CUBE_CMS_DEFAULT_ID_GROUP;
   }

   /**
    * Metoda vrací jestli je uživatel přihlášen
    * @return boolean -- true pokud je uživatel přihlášen
    * @deprecated
    */
   public static function isLoginStatic() {
      return self::isLogin();
   }

   /**
    * Metoda vrací název skupiny ve které je uživatel
    * @return string -- název skupiny
    */
   public static function getGroupName() {
      return self::getUser()->getGroupName();
   }

   /**
    * Metoda vrací id skupiny ve které je uživatel
    * @return integer -- id skupiny
    */
   public static function getGroupId() {
      return self::getUser()->getGroupId();
   }

   /**
    * Metoda vrací id uživatele
    * @return integer -- id uživatele
    */
   public static function getUserId() {
      return self::getUser()->getUserId();
   }

   /**
    * Metoda vrací název uživatele
    * @return string -- název uživatele
    */
   public static function getUserName() {
      return self::getUser()->getUserName();
   }

   /**
    * Metoda vrací mail uživatele
    * @return string -- mail uživatele
    */
   public static function getUserMail() {
      return self::getUser()->getUserMail();
   }

   /**
    * Metoda vrací jestli je uživatele administrátor pro dané stránky
    * @return bool -- true pokud je administrator
    */
   public static function isAdmin() {
      return self::getUser()->isAdmin();
   }

   /**
    * Metoda vrací jestli je uživatele administrátor pro některé stránky z domény
    * @return bool -- true pokud je administrator
    */
   public static function isAdminGroup() {
      return self::getUser()->isAdminGroup();
   }

   /**
    * Metoda vrací pole s weby, kde je uživatel platný
    * @return array -- pole s doménami
    */
   public static function getUserSites() {
      return self::getUser()->getUserSites();
   }

   /**
    * Metoda provede zašifrování hesla
    * @param string $pass -- heslo raw
    * @return string -- šifrované heslo
    */
   public static function cryptPassword($pass) {
      return sha1($pass);
   }

   /**
    * Metoda vygeneruje náhodné heslo
    * @param int $len - délka hesla
    * @return string - heslo
    */
   public static function generatePassword($len = 8)
   {
      $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
      return substr( str_shuffle( $chars ), 0, $len );
   }

   /**
    * Metoda pro generování nového hesla
    * @param $userName
    */
   public static function sendRestorePassword($userName)
   {
      $tr = new Translator();
      
      /**
       * @todo přepsat do Auth_Provider včetně generování emailu
       */
      $modelUsr = new Model_Users();
      $user = $modelUsr->where(Model_Users::COLUMN_USERNAME.' = :uname OR '.Model_Users::COLUMN_MAIL." = :mail",
         array('uname' => $userName, 'mail' => $userName))->record();

      $mail = explode(';', $user->{Model_Users::COLUMN_MAIL});

      $email = new Email(true);
      $email->addAddress($mail[0], $user->{Model_Users::COLUMN_NAME}.' '.$user->{Model_Users::COLUMN_SURNAME});

      $email->setSubject(VVE_WEB_NAME.': '.$tr->tr('obnova hesla'));

      $cnt = $tr->tr("<p>Vážený uživateli,</p><p>zasíláme Vám vyžádanou změnu hesla.</p><p>Pokud jste tento email nevygeneroval Vy, jedná se nejspíše o omyl jiného uživatele a můžete tento e-mail ignorovat.Vašeho aktuálního hesla se změna samozřejmně nedotkne.</p>");

      $newPass = self::generatePassword();

      $cnt .= '<table><tr>';
      $cnt .= "<th>".  $tr->tr('Heslo').':</th><td>'.$newPass."</td>";
      $cnt .= '</tr></table>';

      $email->setContent(Email::getBaseHtmlMail($cnt));
      $email->send();

      if(defined('Model_Users::COLUMN_PASSWORD_RESTORE')){// need release 6.4 r4 or higer
         $user->{Model_Users::COLUMN_PASSWORD_RESTORE} = Auth::cryptPassword($newPass);
      } else {
         $user->{Model_Users::COLUMN_PASSWORD} = Auth::cryptPassword($newPass);
      }
      $modelUsr->save($user);
   }
}
