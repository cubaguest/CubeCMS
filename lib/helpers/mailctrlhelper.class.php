<?php
/**
 * Ttřída Mail Controll Helperu pro zjednodušení práce s emaily.
 * Třída slouží pro práci s emailovými adresami a emaily(odesílání, atd).
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro práci s mail prvky v kontroleru - helper
 */

class MailHelper extends Helper {
	/**
	 * Metoda kontroluje emailovou adresu
	 * 
	 * @param string -- adresa, která se má kontrolovat
	 * @return boolean -- true pokud se jedná o email
	 * @deprecated -- je lepší použít UrlValidator
	 * @todo -- odstranit (je obsažena v UrlValidator)
	 */
	public function checkMail ($email) {
		$name = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]'; // znaky tvořící uživatelské jméno
		$domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])'; // jedna komponenta domény
		return eregi("^$name+(\\.$name+)*@($domain?\\.)+$domain\$", $email);
	}
	
	/**
	 * Metoda odešle zadaný email
	 *
	 * @param string -- na jaký mail se má zpráva odeslat
	 * @param string -- předmět zprávy
	 * @param string -- samotná zpráva
	 * @param string -- z jakého emailu se bude odesílat
	 * @param string -- (option) hlavičky emailu
	 * 
	 * @return boolean -- true pokud se podařilo email odeslat
	 */
	public function sendMail($toMail,$subject,$message,$fromMail,$headers = null) {
//		Vytvoření hlavičky
//		 -=-=-=- MAIL HEADERS
		$headers .= "From: $fromMail\n";
		$headers .= "Reply-To: $fromMail\n";
		$headers .= "MIME-Version: 1.0\n";
    	$headers .= "X-Priority: 3 \n";
		$headers .= "Content-Type: text/plain; charset=UTF-8\n";
		$headers .= "Content-Transfer-Encoding: 8bit\n";
		$headers .= "Return-Path: ".$fromMail."\n\n";
		return mail($toMail, $subject, $message, $headers);
	}
	
	/**
	 * Metoda přeloží podle zadaného pole vnitřek emailu
	 * @deprecated 
	 */
	public function translateText($text, $translateArray) {
		foreach ($translateArray as $translation) {
			$text = str_replace($translation[self::MAIL_TPL_STRING_TRANS], $translation[self::MAIL_TPL_VALUE_TRANS], $text);
		}
		return $text;
	}
}
?>