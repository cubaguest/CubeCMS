<?php
/**
 * Ttřída Mail Controll Helperu pro zjednodušení práce s emaily.
 * Třída slouží pro práci s emailovými adresami a emaily(odesílání, atd).
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: mailctrlhelper.class.php 3.0.55 27.9.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro práci s mail prvky v kontroleru - helper
 */

class MailCtrlHelper extends CtrlHelper {
	/**
	 * Metoda kontroluje emailovou adresu
	 * 
	 * @param string -- adresa, která se má kontrolovat
	 * @return boolean -- true pokud se jedná o email
	 * @deprecated -- je lepší použít UrlValidator
	 * @todo -- odstranit (je obsažena v UrlValidator)
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
		
//		 -=-=-=- MAIL SUBJECT
//		$subject = "Information Request from MSA Shipping - Contact Form";
		
//		 -=-=-=- MAIL TEXT
//		echo $toMail.'<br>'.$subject.'<br>'.$message.'<br>'.$headers;
//		Kompletování emailu a odeslní
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