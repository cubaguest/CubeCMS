<?php
/**
 * Třída pro obsluhu práv přihlášeného uživatele a jednotlivých kategorií
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	Rights class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: rights.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída pro zjišťování práv uživatele
 */
class Rights {

	/**
	 * Prefix sloupců s právy skupin
	 * @var string
	 */
	const RIGHTS_GROUPS_TABLE_PREFIX = 'group_';
	
	/**
	 * Pole s právy ve všech skupinách
	 * @var array
	 */
	private $groupRights = array();
	
	/**
	 * Proměná s objektem autorizace
	 * @var Auth
	 */
	private $auth = null;
	
	/**
	 * Právo čtení
	 * @var boolean
	 */
	private $read = false;
	
	/**
	 * Právo zápisu
	 * @var boolean
	 */
	private $write = false;
	
	/**
	 * Právo kontroly
	 * @var boolean
	 */
	private $controll = false;
	
	/**
	 * Konstruktor
	 *
	 * @param Auth -- objekt s autorizací
	 * @param string -- pole s práva všech skupin
	 */
	function __construct(Auth $auth, $rights) {
		$this->auth = $auth;
		$this->groupRights = $rights;
		
		$this->setRights();
	}
	
	/**
	 * Metoda vrací přístup k objektu autorizace
	 * @return Auth -- objekt autorizace
	 */
	public function getAuth() {
		return $this->auth;
	}
	
	/**
	 * Metoda vrací objekt Modulu
	 * @return Module -- objekt modulu
	 */
//	private function getModule() {
//		return $this->module;
//	}
//	
	
	
	/**
	 * Metoda nastaví práva
	 */
	private function setRights() {
//		echo "<pre>";
//		print_r($this->groupRights);
//		echo "</pre>";
		
		if (ereg("^r[cw-]{2}$", $this->groupRights[$this->getAuth()->getGroupName()])){
			$this->read = true;
		}
		if (ereg("^[r-]w[c-]$", $this->groupRights[$this->getAuth()->getGroupName()])){
			$this->write = true;
		}
		if (ereg("^[rw-]{2}c$", $this->groupRights[$this->getAuth()->getGroupName()])){
			$this->controll = true;
		};
	}
	
	
	
	/**
	 * Metoda vrací true pokud má uživatel právo číst
	 * @return boolean -- právo ke čtení
	 */
	final public function isReadable() {
		return $this->read;
	}
	
	/**
	 * Metoda vrací true pokud má uživatel právo zapisovat
	 * @return boolean -- právo k zápisu
	 */
	final public function isWritable() {
		return $this->write;
	}
	
	/**
	 * Metoda vrací true pokud má uživatel plné právo
	 * @return boolean -- plné právo
	 */
	final public function isControll() {
		return $this->controll;
	}
	
	
	
	
}

?>