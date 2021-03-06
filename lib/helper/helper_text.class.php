<?php
/**
 * Ttřída textového Helperu pro zjednodušení práce s texty.
 * Třída poskytuje některé funkce pro práci s textem.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.3 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro práci s textovými prvky - helper
 */

class Helper_Text extends Helper {
   /**
    * Pole znaků které se odstraňují protoože se nejedná o písmena
    * @var array
    */
	private $nonLettersSymbols = array('/','\\','\'','"',',','.','<','>','?',';',':','[',']',
      '{','}','|','=','+','-','_',')','(','*','&','^','%','$','#','@','!','~','`');

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
	public function utf2ascii(&$text){
//		$return = Str_Replace(
//		Array("á","č","ď","é","ě","í","ľ","ň","ó","ř","š","ť","ú","ů","ý","ž","Á","Č",
//         "Ď","É","Ě","Í","Ľ","Ň","Ó","Ř","Š","Ť","Ú","Ů","Ý","Ž") ,
//		Array("a","c","d","e","e","i","l","n","o","r","s","t","u","u","y","z","A","C",
//         "D","E","E","I","L","N","O","R","S","T","U","U","Y","Z") ,
//		$text);

		$text = strtr($text,
		Array("á" => 'a',"č"=>'c',"ď"=>'d',"é"=>'e',"ě"=>'e',"í"=>'i',"ľ"=>'l',"ň"=>'n',
         "ó"=>'o',"ř"=>'r',"š"=>'s',"ť"=>'t',"ú"=>'u',"ů"=>'u',"ý"=>'y',"ž"=>'z',"Á"=>'A',
         "Č"=>'C',"Ď"=>'D',"É"=>'E',"Ě"=>'E',"Í"=>'I',"Ľ"=>'L',"Ň"=>'N',"Ó"=>'O',"Ř"=>'R',
         "Š"=>'S',"Ť"=>'T',"Ú"=>'U',"Ů"=>'U',"Ý"=>'Y',"Ž"=>'Z'));
//		Array("a","c","d","e","e","i","l","n","o","r","s","t","u","u","y","z","A","C",
//         "D","E","E","I","L","N","O","R","S","T","U","U","Y","Z") ,
//		$text);
      $text = preg_replace("/[ \_-]{1,}/", "-", $text);
      $text = preg_replace("/[().\"\'!?<>,]?/", "", $text);
//		$return = Str_Replace(Array(" ", "_"), "-", $return); //nahradí mezery a podtržítka pomlčkami
//		$return = Str_Replace(array("----","---","--"), "-", $return); //odstraní nekolik pomlcek za sebou
//		$text = Str_Replace(Array("(",")",".","!",",","\"","'"), "", $text); //odstraní ().!,"'
//		$return = StrToLower($return); //velká písmena nahradí malými.
      return $text;
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

   /**
    * Metoda odstraní znaky které nejsou písmena
    * @param striing $string -- řetězec ze kterého se bude odstraňovat
    * @return string -- řetězec bez specielních znaků
    */
	public function removeNonLettersSymbols($string) {
		for ($i = 0; $i < sizeof($this->symbols); $i++) {
			$string = str_replace($this->symbols[$i],' ',$string);
		}
		return trim($string);
	}

   /**
    * Metoda ořeže řetězec na podřebnou délu se zachováním slov
    * @param string $text -- text pro ořezání
    * @param integer $count -- počet znaků pro ořezání
    * @param string $endString -- (option)řetězec zakončení (např. ...)
    * @return string -- ořezaná řetězec
    */
   public function truncate($text,$count, $endString = null) {
      if (strlen($text) > $count) {
         $text = mb_substr($text, 0, $count);
         $text = mb_substr($text,0,strrpos($text," "));
         //This strips the full stop:
         if ((mb_substr($text, -1)) == ".") {
            $text = mb_substr($text,0,(strrpos($text,".")));
         }
         $text = $text.$endString;
      }
      return $text;
   }
}
?>