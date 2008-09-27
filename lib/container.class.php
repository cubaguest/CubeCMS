<?php
/**
 * Třída pro práci s daty mezi controlerrem a viewrem
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	Container class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: container.class.php 3.0.5 27.9.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro obsluhu dat mezi viewrem a controlerem
 */
class Container {
	/**
	 * Pole s uživatelskými daty
	 * @var array
	 */
	private $data = array();
	
	/**
	 * Pole s použitými epluginy
	 * @var array
	 */
	private $ePlugins = array();
	
	/**
	 * pole s odkazy
	 * @var array
	 */
	private $links = array();
	
	
	/**
	 * Konstruktor
	 *
	 */
	function __construct() {
		;
	}
	
	/**
	 * Metoda uloži zadaná data pod zadaný index
	 *
	 * @param string/integer -- index dat
	 * @param mixed -- data
	 */
	public function addData($index, $value){
		//TODO dodělat kontrolu, jestli položka už není vložena
		$this->data[$index] = $value;
	}
	
	/**
	 * Metoda vrací data ze zadaného indexu
	 *
	 * @param string/integer -- index pod kterým jsou data uložena
	 * @return mixed -- uložená data
	 */
	public function getData($index) {
		return $this->data[$index];
	}
	
	/**
	 * Metoda uloži zadaný eplugin pod zadaný index
	 *
	 * @param string/integer -- index epluginu
	 * @param Eplugin -- objekt epluginu
	 */
	public function addEplugin($index, Eplugin $eplugin){
		//TODO dodělat kontrolu, jestli položka už není vložena
		$this->ePlugins[$index] = $eplugin;
	}
	
	/**
	 * Metoda vrací eplugin ze zadaného indexu
	 *
	 * @param string/integer -- index pod kterým je Eplugin uložen
	 * @return Eplugin -- uložený Eplugin
	 */
	public function getEplugin($index) {
		return $this->ePlugins[$index];
	}
	
	/**
	 * Metoda uloži zadaný link pod zadaný index
	 *
	 * @param string/integer -- index linku
	 * @param Links -- objekt linku
	 */
	public function addLink($index, Links  $link){
		//TODO dodělat kontrolu, jestli položka už není vložena
		$this->links[$index] = $link;
	}
	
	/**
	 * Metoda vrací link ze zadaného indexu
	 *
	 * @param string/integer -- index pod kterým je link uložen
	 * @return Links -- uložený link
	 */
	public function getLink($index) {
		return $this->links[$index];
	}
	
	
	
}

?>