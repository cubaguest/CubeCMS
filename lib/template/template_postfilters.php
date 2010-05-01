<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of template_postfilters
 *
 * @author jakub
 */
class Template_Postfilters {
    /**
	 * Metoda převede předložky a některé znaky s normální mezerou na nezalomitelné mezery
	 *
	 * @param string -- zadaný text
	 * @return string -- text. u kterého jsou převedeny předložky
	 */
	public function czechTypo($text) {
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
		return $text;
	}
}
?>
