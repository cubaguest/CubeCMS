<?php

/**
 * Třída pro práci se $_SESSIONS.
 * Třída umožňuje základní přístupy k Sessions, jejich vytváření, mazání, aktualizaci atd.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: session_base.class.php -1   $ VVE3.9.4 $Revision: -1 $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro práci se SESSIONS
 *
 * @todo          dodělat! není skoro implementována
 */
class Session {
   /**
    * Název session
    * @var string
    */
   private $name = 'default';

   protected static $started = false;

   /**
    * Model se session
    * @var Model_ORM_Record
    */
//   private static $sessionRecord = null;

   /**
    * Konstruktor vytvoří objekt pro práci se session
    *
    * @param string $sessionName -- název session (option)
    */
   function __construct($name = 'default') {
      $this->name = $name;
   }

   /**
    * Metoda uloží proměnou do session
    *
    * @param string $name -- název proměné
    * @param mixed $value -- hodnota proměné
    */
   public function set($value) {
      $_SESSION[$this->name] = $value;
   }

   /**
    * Metoda vrací obsah zadané session
    *
    * @param string $name -- název session
    * @return mixed -- obsah session
    */
   public function get() {
      if (isset($_SESSION[$this->name])) {
         return $_SESSION[$this->name];
      } else {
         return null;
      }
   }

   /**
    * Metoda uloží session a znovu ji načte
    */
   public function commit() {
      session_write_close();
      session_start();
   }

   /**
    * Statická metoda pro nastavení session
    * @param string $sessionName -- název session do ketré se bude ukládat
    */
   public static function factory() {
      if(self::$started){
         return;
      }
      if(( SERVER_PLATFORM == 'UNIX' && !defined('CUBE_CMS_SESSION_SAVE_HANDLER'))
          || ( SERVER_PLATFORM == 'UNIX' && defined('CUBE_CMS_SESSION_SAVE_HANDLER') && CUBE_CMS_SESSION_SAVE_HANDLER == 'db' )){
         session_set_save_handler(array('Session', 'open'),
                         array('Session', 'close'),
                         array('Session', 'read'),
                         array('Session', 'write'),
                         array('Session', 'destroy'),
                         array('Session', 'gc'));
      } 

      ini_set('session.cookie_lifetime',  CUBE_CMS_LOGIN_TIME);
      ini_set('session.gc_maxlifetime',  CUBE_CMS_LOGIN_TIME);
      ini_set('session.gc_probability', 1);
      if(CUBE_CMS_DEBUG_LEVEL > 0){
         ini_set('session.gc_divisor', 100);
      } else {
         ini_set('session.gc_divisor', 1000);
      }
      // pokud je id sessison přenesena v jiném parametru než než přez cookie
      if (isset($_REQUEST['sessionid'])) {
         session_id($_REQUEST['sessionid']);
      } else if (isset($_REQUEST[CUBE_CMS_SESSION_NAME])) {
         session_id($_REQUEST[CUBE_CMS_SESSION_NAME]);
      }

      //Nastaveni session
      if (Url_Request::getDomain() != 'localhost'){
         session_set_cookie_params(CUBE_CMS_LOGIN_TIME, '/', '.'.Url_Request::getDomain(), false, true);
      } else {
         session_set_cookie_params(CUBE_CMS_LOGIN_TIME, '/', 'localhost', false, true);
      }
      session_name(CUBE_CMS_SESSION_NAME);
      session_start();
      if(!isset($_SESSION['_expire'])){
         $_SESSION['_expire'] = time() + CUBE_CMS_LOGIN_TIME/2;
      }
      if($_SESSION['_expire'] < time()){
         unset($_SESSION['_expire']);
         session_regenerate_id(true);
      }
      
      // cookie params
      if(isset ($_COOKIE[CUBE_CMS_SESSION_NAME])){
         $cookieParams = session_get_cookie_params();
         setcookie(CUBE_CMS_SESSION_NAME, session_id(), time()+$cookieParams['lifetime'], 
             $cookieParams['path'], $cookieParams['domain'], $cookieParams['secure'], $cookieParams['httponly']);
      }
      self::$started = true;
   }

    public static function regenerateId() {
    }

   /**
    * Metoda vrací id session s přihlašovacími údaji
    * @return string
    */
   public static function getSessionId() {
      return session_id();
   }

   /**
    * Metoda pro ukládání session do db
    * @global <type> $sess_save_path
    * @param <type> $save_path
    * @param <type> $session_name
    * @return <type>
    */
   public static function open($save_path, $session_name) {
      return true;
   }

   public static function close() {
      return true;
   }

   public static function read($id) {
      $model = new Model_Session();
      $sess = $model->where(Model_Session::COLUMN_KEY.' = :key ',array('key' => $id))->record();
      if($sess != false){
         return (string)$sess->{Model_Session::COLUMN_VALUE};
      }
//      self::$sessionRecord = $model->newRecord();
      return (string)null;
   }

   public static function write($id, $sess_data) {
      $model = new Model_Session();
      $session = $model->where(Model_Session::COLUMN_KEY." = :key", array('key' => $id))->record();
      if(!$session || $session->isNew()){
         $session = $model->newRecord();
         $session->{Model_Session::COLUMN_IP} = $_SERVER['REMOTE_ADDR'];
         $session->{Model_Session::COLUMN_CREATED} = new DateTime();
      }
      $session->{Model_Session::COLUMN_KEY} = $id;
      $session->{Model_Session::COLUMN_VALUE} = $sess_data;
      $session->save();
      
      return true;
      
//      self::$sessionRecord->{Model_Session::COLUMN_KEY} = $id;
//      self::$sessionRecord->{Model_Session::COLUMN_VALUE} = $sess_data;
//      // pokud je nový zázname
//      if (self::$sessionRecord->isNew()) {
//         self::$sessionRecord->{Model_Session::COLUMN_IP} = $_SERVER['REMOTE_ADDR'];
//         self::$sessionRecord->{Model_Session::COLUMN_CREATED} = new DateTime();
//      }
//      self::$sessionRecord->{Model_Session::COLUMN_UPDATED} = new DateTime();
//      $model->save(self::$sessionRecord);
//      return true;
   }

   public static function destroy($id) {
      $model = new Model_Session();
      if($model->delete($id) == false) return false;
      return true;
   }

   public static function gc($maxlifetime) {
      $model = new Model_Session();
      //DELETE FROM vezeni_sessions WHERE ADDTIME(`updated`, SEC_TO_TIME(3600)) < NOW() 
      $deleted = $model->where(' ADDTIME('.Model_Session::COLUMN_UPDATED.', SEC_TO_TIME(:lftime)) <= NOW()', array('lftime' => $maxlifetime))->delete();
//      file_put_contents(AppCore::getAppCacheDir().'session.log', $maxlifetime.' > '.self::$sessionRecord->{Model_Session::COLUMN_KEY}.' del: '.$deleted);
      return true;
   }

}