<?php
/**
 * Ttřída textového Controll Helperu pro zjednodušení práce s texty.
 * Třída poskytuje některé funkce pro práci s textem.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: localectrlhelper.class.php 3.0.55 27.9.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro práci s textovými prvky v kontroleru - helper
 */

class TextCtrlHelper extends CtrlHelper {
	/**
	 * Metoda dekóduje znaky na entity html
	 *
	 * @param string -- text k dekódování
	 * @return string -- dekódovaný text
	 * @deprecated 
	 */
	public function decodeHtmlSpecialChars($text) {
		//TODO pořešid pokud je zaplé tiny mce
		$text = htmlspecialchars_decode($text);
		
		return $text;
	}
	
	/**
	 * Metoda odstrani nekorektni znaky z řetězce
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

	/**
	 * Metoda odstraní všechny html tagy ze zadaného stringu
	 *
	 * @param string $string -- řětězec
	 * @return string -- řetězec bez tagů
	 */
	public function removeHtmlTags($string) {
		$string=ereg_replace("<[^>]+>", "", $string);
		return $string;
	}
	
}


?>