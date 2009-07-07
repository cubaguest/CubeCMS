<?php
/**
 * Description of UrlValidator
 * Třída slouží pro validaci url adres a emailů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: urlvalidator.class.php 533 2009-03-29 00:11:57Z jakub $ VVE3.9.4 $Revision: 533 $
 * @author        $Author: jakub $ $Date: 2009-03-29 00:11:57 +0000 (Ne, 29 bře 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-03-29 00:11:57 +0000 (Ne, 29 bře 2009) $
 * @abstract 		Třída pro validaci formulářových prvků
 */
class Validator_Url extends Validator {
  	/**
	 * Metoda kontroluje emailovou adresu
	 *
	 * @param string $mail -- adresa, která se má kontrolovat
	 * @return boolean -- true pokud se jedná o email
	 */
	public function checkMail ($email) {
		if (eregi("^[a-z0-9_\.]+@[a-z0-9_\.]+[a-z]{2,3}$", $email)) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Metoda kontroluje správnost url adresy
	 * @param string $url -- url adresa
	 * @return boolean -- vrací true pokud se jedná o url adresu
	 * @todo -- dodělat, není implementována
	 */
	public function checkUrl($url) {
		return true;
	}
}
?>