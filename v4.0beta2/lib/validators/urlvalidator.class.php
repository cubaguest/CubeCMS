<?php
/**
 * Description of UrlValidator
 * Třída slouží pro validaci url adres a emailů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro validaci formulářových prvků
 */
class UrlValidator extends Validator {
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