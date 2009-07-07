<?php
/**
 * Třída pro validaci prvků
 * Třída obsluhuje zakladní třídu pro tvorbu validátorů.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: validator.class.php 533 2009-03-29 00:11:57Z jakub $ VVE3.9.4 $Revision: 533 $
 * @author        $Author: jakub $ $Date: 2009-03-29 00:11:57 +0000 (Ne, 29 bře 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-03-29 00:11:57 +0000 (Ne, 29 bře 2009) $
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
