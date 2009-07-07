<?php
/**
 * Třída pro validaci prvků
 * Třída obsluhuje zakladní třídu pro tvorbu validátorů.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro validaci
 */

class Validator {
	/**
	 * Konstruktor nastaví základní parametry
	 */
	final public function  __construct() {}

	/**
	 * Metoda vrací objekt s informačními zprávami
	 * @return Messages -- objekt zpráv
	 */
	final public function infoMsg() {
      return AppCore::getInfoMessages();
	}

	/**
	 * Metoda vrací objekt s chybovými zprávami
	 * @return Messages -- objekt zpráv
	 */
	final public function errMsg() {
      return AppCore::getUserErrors();
	}
}
?>
