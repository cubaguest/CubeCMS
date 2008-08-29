<?php
/**
 * Třída pro práci se SESSIONS
 * 
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	Sessions class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: sessions.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída pro práci se SESSIONS
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