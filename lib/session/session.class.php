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

   /**
    * Model se session
    * @var Model_ORM_Record
    */
   private static $sessionRecord = null;

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
      if(SERVER_PLATFORM == 'UNIX'){
         session_set_save_handler(array('Session', 'open'),
                         array('Session', 'close'),
                         array('Session', 'read'),
                         array('Session', 'write'),
                         array('Session', 'destroy'),
                         array('Session', 'gc'));
      }

      ini_set('session.cookie_lifetime',  VVE_LOGIN_TIME);
      ini_set('session.gc_maxlifetime',  VVE_LOGIN_TIME);
      ini_set('session.gc_probability', 1);
      if(VVE_DEBUG_LEVEL > 0){
         ini_set('session.gc_divisor', 100);
      } else {
         ini_set('session.gc_divisor', 1000);
      }
      // pokud je id sessison přenesena v jiném parametru než než pře cookie
      if (isset($_REQUEST['sessionid'])) {
         session_id($_REQUEST['sessionid']);
      } else if (isset($_REQUEST[VVE_SESSION_NAME])) {
         session_id($_REQUEST[VVE_SESSION_NAME]);
      }

      //Nastaveni session
      if (Url_Request::getDomain() != 'localhost'){
         session_set_cookie_params(VVE_LOGIN_TIME, '/', '.'.Url_Request::getDomain());
      } else {
         session_set_cookie_params(VVE_LOGIN_TIME, '/');
      }
      session_name(VVE_SESSION_NAME);
      session_start();
      // cookie params
      $cookieParams = session_get_cookie_params();
      if(isset ($_COOKIE[VVE_SESSION_NAME])){
         setcookie(VVE_SESSION_NAME, session_id(), time()+$cookieParams['lifetime'], $cookieParams['path'], '.'.Url_Request::getDomain());
      }
   }

   /**
     *  Regenerates the session id.
     *  <b>Call this method whenever you do a privilege change!</b>
     *  @return void
     */
    public static function regenerateId() {
        // saves the old session's id
        $oldSessionID = session_id();

        // regenerates the id
        // this function will create a new session, with a new id and containing the data from the old session
        // but will not delete the old session
        session_regenerate_id();

        // because the session_regenerate_id() function does not delete the old session,
        // we have to delete it manually
        self::destroy($oldSessionID);
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
      self::$sessionRecord = $model->where(Model_Session::COLUMN_KEY.' = :key ',array('key' => $id))->record();
      if(self::$sessionRecord != false){
         return (string)self::$sessionRecord->{Model_Session::COLUMN_VALUE};
      }
      self::$sessionRecord = $model->newRecord();
      return (string)null;
   }

   public static function write($id, $sess_data) {
      $model = new Model_Session();
      self::$sessionRecord->{Model_Session::COLUMN_KEY} = $id;
      self::$sessionRecord->{Model_Session::COLUMN_VALUE} = $sess_data;
      // pokud je nový zázname
      if (self::$sessionRecord->isNew()) {
         self::$sessionRecord->{Model_Session::COLUMN_IP} = $_SERVER['REMOTE_ADDR'];
         self::$sessionRecord->{Model_Session::COLUMN_CREATED} = new DateTime();
      }
      self::$sessionRecord->{Model_Session::COLUMN_UPDATED} = new DateTime();
      $model->save(self::$sessionRecord);
      return true;
   }

   public static function destroy($id) {
      $model = new Model_Session();
      if($model->delete($id) == false) return false;
      return true;
   }

   public static function gc($maxlifetime) {
      $model = new Model_Session();
      $deleted = $model->where(' TIMESTAMPADD(SECOND, :lftime, '.Model_Session::COLUMN_UPDATED.') <= NOW()', array('lftime' => $maxlifetime))->delete();
      file_put_contents(AppCore::getAppCacheDir().'session.log', $maxlifetime.' > '.self::$sessionRecord->{Model_Session::COLUMN_KEY}.' del: '.$deleted);
      return true;
   }

}
?>