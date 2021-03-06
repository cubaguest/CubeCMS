<?php
/**
 * Třída práv uživatele a jednotlivých kategorií.
 * Třída slouží pro práci s právy uživatele na zvolené kategorii. Umožňuje zjištění,
 * jestli je kategorie přístuppná pro čtení, zápis nebo kontrolu.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro zjišťování práv uživatele
 */

class Rights {
	/**
	 * Pole s právy
	 * @var array
	 */
	private $rights = array();
	
	/**
	 * Konstruktor
	 *
	 * @param Auth -- objekt s autorizací
	 * @param string -- pole s práva všech skupin
	 */
	function __construct() {
	}
	
	/**
	 * Metoda nastaví práva
	 */
	private function parseRights($r) {
      $rigths = array('read'=> false,
                      'write' => false,
                      'controll' => false);
      $matches = array();
      if(preg_match('/^([r-]{1})([w-]{1})([c-]{1})$/', $r, $matches) != 1){
         throw new UnexpectedValueException(_("Špatně předaný parametr práv ke kategorii"));
      }
		if ($matches[1] == 'r'){
         $rigths['read'] = true;
		}
		if ($matches[2] == 'w'){
         $rigths['write'] = true;
		}
		if ($matches[3] == 'c'){
         $rigths['controll'] = true;
		};
      return $rigths;
	}

   /**
    * Metoda přidá právo
    */
    public function addRight($value) {
       $this->rights = $this->parseRights($value);
    }
	
	/**
	 * Metoda vrací true pokud má uživatel právo číst
	 * @return boolean -- právo ke čtení
	 */
	final public function isReadable() {
		return $this->rights['read'];
	}
	
	/**
	 * Metoda vrací true pokud má uživatel právo zapisovat
	 * @return boolean -- právo k zápisu
	 */
	final public function isWritable() {
      return $this->rights['write'];
	}
	
	/**
	 * Metoda vrací true pokud má uživatel plné právo
	 * @return boolean -- plné právo
	 */
	final public function isControll() {
      return $this->rights['controll'];
	}
}
?>