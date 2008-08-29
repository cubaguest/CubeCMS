<?php
/**
 * Třída pro práci se SESSIONS
 * 
 * @author Jakub Matas
 * @copyright 2008
 * @version 0.0.1
 * @package vve -- veprove vypecky engine v3.0
 * 
 * //TODO dodělat! není skoro implementována
 */
class Sessions {
	function __construct() {
		;
	}
	
	/**
	 * Metoda uloží proměnou do session
	 * 
	 * @param string -- název proměné
	 * @param mixed -- hodnota proměné
	 */
	public function add($name, $value) {
		$_SESSION[$name] = $value;
	}
	
	/**
	 * Metoda vrací obsah zadané session
	 * 
	 * @param string -- název session
	 * @return mixed -- obsah session
	 */
	public function get($name){
		//TODO dodělat ověřování atd
		
		return $_SESSION[$name];
	}
	
	
	/**
	 * Metoda zjišťuje jestli je daná session prázdná
	 * 
	 * @param string -- název session
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
	 * @param string -- název session
	 */
	public function remove($name){
		if(isset($_SESSION[$name])){
			$_SESSION[$name] = null;
			unset($_SESSION[$name]);
		}
	}
	
	
	
}

?>