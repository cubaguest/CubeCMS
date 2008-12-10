<?php
/**
 * Třída pro validaci prvků
 * Třída obsluhuje zakladní třídu pro tvorbu validátorů.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: validator.class.php 3.0.0 beta1 29.8.2008
 * @author			Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro validaci
 */

class Validator {
	/**
	 * Obejt pro informační hlášky
	 * @var Messages
	 */
	private $infomsg = null;

	/**
	 * Obejt pro chybové hlášky hlášky
	 * @var Messages
	 */
	private $errmsg = null;

	/**
	 * Objekt modulu
	 * @var Module
	 */
	private $module = null;

	/**
	 * Konstruktor nastaví základní parametry
	 */
	final public function  __construct() {
		if(AppCore::getSelectedModule() instanceof Module){
			$this->module = AppCore::getSelectedModule();
		}

		if(AppCore::getModuleMessages() instanceof Messages){
			$this->infomsg = AppCore::getModuleMessages();
		}

		if(AppCore::getModuleErrors() instanceof Messages){
			$this->errmsg = AppCore::getModuleErrors();
		}
	}

	/**
	 * Metoda vrací objekt s informačními zprávami
	 * @return Messages -- objekt zpráv
	 */
	final public function infoMsg() {
		return $this->infomsg;
	}

	/**
	 * Metoda vrací objekt s chybovými zprávami
	 * @return Messages -- objekt zpráv
	 */
	final public function errMsg() {
		return $this->errmsg;
	}
}
?>
