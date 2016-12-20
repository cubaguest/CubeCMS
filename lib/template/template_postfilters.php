<?php
/**
 * Třída s posrfiltry, aplikovanými na proměnné v šabloně. Měla by být implementována
 * přímo do renderu šablon
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída s postfiltry
 */
class Template_Postfilters {
   private static $emoticonsTranslate = array(
      ':-D' => '<img src="images/smiles/face-laugh.png" alt=":-D" />',
      ':-(' => '<img src="images/smiles/face-sad.png" alt=":-(" />',
      ':-/' => '<img src="images/smiles/face-uncertain.png" alt=":-/" />',
      ':-|' => '<img src="images/smiles/face-plain.png" alt=":-|" />',
      ';-)' => '<img src="images/smiles/face-wink.png" alt=";-)" />',
      ':-*' => '<img src="images/smiles/face-kiss.png" alt=":-*" />',
      ':*' => '<img src="images/smiles/face-kiss.png" alt=":*" />',
      'O:-)' => '<img src="images/smiles/face-angel.png" alt="O:-)" />',
      'O:)' => '<img src="images/smiles/face-angel.png" alt="O:)" />',
      '>:)' => '<img src="images/smiles/face-evil.png" alt=">:)" />',
      'D:<' => '<img src="images/smiles/face-angry.png" alt="D:<" />',
      'D:-<' => '<img src="images/smiles/face-angry.png" alt="D:-<" />',
      ':-O' => '<img src="images/smiles/face-surprise.png" alt=":-O" />',
      ':O' => '<img src="images/smiles/face-surprise.png" alt=":O" />',
      ':)' => '<img src="images/smiles/face-smile.png" alt=":)" />',
      ':-)' => '<img src="images/smiles/face-smile.png" alt=":-)" />'
      );


    /**
	 * Metoda převede předložky a některé znaky s normální mezerou na nezalomitelné mezery
	 *
	 * @param string -- zadaný text
	 * @return string -- text. u kterého jsou převedeny předložky
	 */
	public static function czechTypo($text) {
		$czechPripositions = "(k|s|v|z|a|i|o|u|ve|ke|ku|za|ze|na|do|od|se|po|pod|před|nad|bez|pro|při|Ing.|Bc.|Arch.|Mgr.)";

		// překlad předložek na konci řádku
		$pattern = "/[[:blank:]]{1}".$czechPripositions."[[:blank:]]{1}/";
		$replacement = " \\1&nbsp;";
		$text = preg_replace($pattern, $replacement, $text);

		$pattern = "/&(?![lt])[a-z]+;".$czechPripositions."[[:blank:]]{1}/";
		$replacement = "&nbsp;\\1&nbsp;";
		$text = preg_replace($pattern, $replacement, $text);

		//	zkratky množin
		$pattern = "/([0-9])[[:blank:]]{1}(kč|˚C|V|A|W)/";
		$replacement = "\\1&nbsp;\\2";
		$text = preg_replace($pattern, $replacement, $text);

		//mezera mezi číslovkami
		$pattern = "/([0-9])([[:blank:]]{1})([0-9]{3})/";
		$replacement = "\\1&nbsp;\\3";
		$text = preg_replace($pattern, $replacement, $text);
		return $text;
	}

	/**
	 * Metoda převede předložky a některé znaky s normální mezerou na nezalomitelné mezery
	 *
	 * @param string -- zadaný text
	 * @return string -- text. u kterého jsou převedeny předložky
	 */
	public static function typo($text, $link = null, $tplobj = null, $lang = null) {
      if(!$lang){
         $lang = Locales::getLang();
      }
      switch ($lang) {
         case 'cs':
            $czechPripositions = "(k|s|v|z|a|i|o|u|ve|ke|ku|za|ze|na|do|od|se|po|pod|před|nad|bez|pro|při|Ing.|Bc.|Arch.|Mgr.)";

            // překlad předložek na konci řádku
            $pattern = "/[[:blank:]]{1}".$czechPripositions."[[:blank:]]{1}/";
            $replacement = " \\1&nbsp;";
            $text = preg_replace($pattern, $replacement, $text);

            $pattern = "/&[a-z]+;".$czechPripositions."[[:blank:]]{1}/";
            $replacement = "&nbsp;\\1&nbsp;";
            $text = preg_replace($pattern, $replacement, $text);

            //	zkratky množin
            $pattern = "/([0-9])[[:blank:]]{1}(kč|˚C|V|A|W)/";
            $replacement = "\\1&nbsp;\\2";
            $text = preg_replace($pattern, $replacement, $text);

            //mezera mezi číslovkami
            $pattern = "/([0-9])([[:blank:]]{1})([0-9]{3})/";
            $replacement = "\\1&nbsp;\\3";
            $text = preg_replace($pattern, $replacement, $text);

            break;
      }
		return $text;
	}

   /**
    * Metoda převede emotikony v řetězci na obrázky
    * @param string $string -- řetězec
    * @return string
    */
   public static function emoticons($string){
      $string = strtr($string, self::$emoticonsTranslate);
      return $string;
   }
}
?>
