<?php
/**
 * Ttřída textového Controll Helperu
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	LocaleCtrlHelper class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: localectrlhelper.class.php 3.0.55 27.9.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro práci s textovými prvky v kontroleru - helper
 */

class TextCtrlHelper extends CtrlHelper {
	/**
	 * Metoda kontroluje emailovou adresu
	 * 
	 * @param string -- adresa, která se má kontrolovat
	 * @return boolean -- true pokud se jedná o email
	 */
	public function checkMail ($email) {

		if (Eregl("^[a-z0-9_\.]+@[a-z0-9_\.]+[a-z]{2,3}$", $email)) {
			return true;
		}
		else {
			return false;
		}
	}
	
	
	/**
	 * Metoda dekóduje znaky na entity html
	 *
	 * @param string -- text k dekódování
	 * @return string -- dekódovaný text
	 */
	public function decodeHtmlSpecialChars($text) {
		//TODO pořešid pokud je zaplé tiny mce
		$text = htmlspecialchars_decode($text);
		
		return $text;
	}
	
	/**
	 * Funkce odstrani nekorektni znaky z řetězce
	 *
	 * @param string -- řetězec z kterého se odstraní znaky
	 * @return string -- výsledný řetězec
	 */
	public function utf2ascii($text)
	{
		$return = Str_Replace(
		Array("á","č","ď","é","ě","í","ľ","ň","ó","ř","š","ť","ú","ů","ý","ž","Á","Č","Ď","É","Ě","Í","Ľ","Ň","Ó","Ř","Š","Ť","Ú","Ů","Ý","Ž") ,
		Array("a","c","d","e","e","i","l","n","o","r","s","t","u","u","y","z","A","C","D","E","E","I","L","N","O","R","S","T","U","U","Y","Z") ,
		$text);

		$return = Str_Replace(Array(" ", "_"), "-", $return); //nahradí mezery a podtržítka pomlčkami
		$return = Str_Replace(array("----","---","--"), "-", $return); //odstraní nekolik pomlcek za sebou
		$return = Str_Replace(Array("(",")",".","!",",","\"","'"), "", $return); //odstraní ().!,"'
		$return = StrToLower($return); //velká písmena nahradí malými.
		return $return;
	}
	
}


?>