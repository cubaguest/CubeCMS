<?php
/**
 * Třída pro práci se $_SESSIONS.
 * Třída umožňuje základní přístupy k Sessions, jejich vytváření, mazání, aktualizaci atd.
 * 
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro práci se SESSIONS
 * 
 * @todo          dodělat! není skoro implementována
 */

class Sessions {
	/**
	 * Proměná obsahuje jesli byly session inicializovány
	 * @var boolean
	 */
	private static $sessionInitalized = false;
	
	/**
	 * Statická metoda pro nastavení session
	 * @param string $sessionName -- název session do ketré se bude ukládat
	 */
	public static function factory($sessionName) {
      // pokud je id sessison přenesena v jiném parametru než než pře cookie
      if(isset ($_REQUEST['sessionid'])){
         session_id($_REQUEST['sessionid']);
      }

		//Nastaveni session
      if($_SERVER['SERVER_NAME'] != 'localhost' AND preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" .
            "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $_SERVER['SERVER_NAME']) == 0){
         session_set_cookie_params(3600, '/', substr($_SERVER['SERVER_NAME'],strpos($_SERVER['SERVER_NAME'],".")));
         
      }
		session_regenerate_id(); // ochrana před Session Fixation
		// 	Nastaveni limutu pro automaticke odhlaseni
		/* set the cache limiter to 'private' */
		session_cache_limiter('private');
		$cache_limiter = session_cache_limiter();
		/* set the cache expire to 30 minutes */
		session_cache_expire(60);
		$cache_expire = session_cache_expire();
		session_name($sessionName);
		session_start();
		self::$sessionInitalized = true;
	}
	
	/**
	 * Konstruktor vytvoří objekt pro práci se session
	 *
	 * @param string $sessionName -- název session (option)
	 */
	function __construct($sessionName = 'default_session') {
		if(!self::$sessionInitalized){
			self::factory($sessionName);
		}
	}
	
	/**
	 * Metoda uloží proměnou do session
	 * 
	 * @param string $name -- název proměné
	 * @param mixed $value -- hodnota proměné
	 */
	public function add($name, $value) {
		$_SESSION[$name] = $value;
	}
	
	/**
	 * Metoda vrací obsah zadané session
	 * 
	 * @param string $name -- název session
	 * @return mixed -- obsah session
	 */
	public function get($name){
		//TODO dodělat ověřování atd
		if(isset($_SESSION[$name])){
			return $_SESSION[$name];
		} else {
			return null;
		}
	}
	
	/**
	 * Metoda zjišťuje jestli je daná session prázdná
	 * 
	 * @param string $name -- název session
	 */
	public function isEmpty($name) {
		if(isset($_SESSION[$name])){
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * Metoda odstraní zadanou session
	 * 
	 * @param string $name -- název session
	 */
	public function remove($name){
		if(isset($_SESSION[$name])){
			$_SESSION[$name] = null;
			unset($_SESSION[$name]);
		}
	}
	
	/**
	 * Metoda uloží session a znovu ji načte
	 */
	public function commit() {
		session_commit();
		session_start();
	}
}
?>