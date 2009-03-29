<?php
/**
 * Třída pro přenos daty mezi controlerem, viewrem a šablonami. Data jsou
 * ukládána a vybírána přes pole.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu dat mezi viewrem a controlerem
 * @todo          nebylo by marné implementovat přes objekt pole
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
	 */
	function __construct() {}
	
	/**
	 * Metoda uloži zadaná data pod zadaný index
	 *
	 * @param string/integer $index -- index dat
	 * @param mixed $value -- data
	 */
	public function addData($index, $value){
		//TODO dodělat kontrolu, jestli položka už není vložena
		$this->data[$index] = $value;
	}
	
	/**
	 * Metoda vrací data ze zadaného indexu
	 *
	 * @param string/integer $index -- index pod kterým jsou data uložena
	 * @param mixed $defaultValue -- výchozí hodnota
	 * @return mixed -- uložená data
	 */
	public function getData($index, $defaultValue = null) {
		if(isset($this->data[$index])){
			return $this->data[$index];
		}
      return $defaultValue;
	}
	
	/**
	 * Metoda uloži zadaný eplugin pod zadaný index
	 *
	 * @param string/integer $index -- index epluginu
	 * @param Eplugin $eplugin -- objekt epluginu
	 */
	public function addEplugin($index, Eplugin &$eplugin){
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
      try {
         if(!isset($this->ePlugins[$index])){
            throw new InvalidArgumentException(sprintf(_('Eplugin %s nebyl přiřazen'), $index),1);
         }
         return $this->ePlugins[$index];
      } catch (InvalidArgumentException $e) {
         new CoreErrors($e);
      }
	}
	
	/**
	 * Metoda uloži zadaný link pod zadaný index
	 *
	 * @param string/integer $index -- index linku
	 * @param Links $link -- objekt linku
	 */
	public function addLink($index, Links  $link){
		//TODO dodělat kontrolu, jestli položka už není vložena
		$this->links[$index] = $link;
	}
	
	/**
	 * Metoda vrací link ze zadaného indexu
	 *
	 * @param string/integer $index -- index pod kterým je link uložen
	 * @return Links -- uložený link
	 */
	public function getLink($index) {
		if(isset($this->links[$index])){
			return $this->links[$index];
		} else {
			return new Links();
		}
	}
	
	/**
	 * Metoda vrací všechna uložená data v poli
	 *
	 * @return array -- pole s daty
	 */
	public function getAllData() {
		return $this->data;
	}
	
	/**
	 * Metoda vrací všechny uložené odkazy v poli
	 *
	 * @return array -- pole s daty
	 */
	public function getAllLinks() {
		return $this->links;
	}
}
?>