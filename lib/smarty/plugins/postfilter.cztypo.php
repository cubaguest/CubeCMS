<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     postfilter.cztypo.php
 * Type:     postfilter
 * Name:     cztypo
 * Purpose:  Convert space to html space a special czech chars
 * -------------------------------------------------------------
 */
// function smarty_postfilter_cztypo($source, &$smarty)
 function smarty_postfilter_cztypo($source, &$smarty)
 {
   $czechPripositions = "(k|s|v|z|a|i|o|u|ve|ke|ku|za|ze|na|do|od|se|si|po|pod|před|nad|bez|pro|při|Ing.|Bc.|Arch.|Mgr.)";
	// překlad předložek na konci řádku
	$pattern = "[[:blank:]\"]{1}".$czechPripositions."[[:blank:]]{1}";
	$replacement = " \\1&nbsp;";
	$text = eregi_replace($pattern, $replacement, $source);

	$pattern = "&[a-z]+;".$czechPripositions."[[:blank:]]{1}";
	$replacement = "&nbsp;\\1&nbsp;";
	$text = eregi_replace($pattern, $replacement, $text);

	//	zkratky množin
	$pattern = "([0-9])[[:blank:]]*(kč|˚C|V|A|W)";
	$replacement = "\\1&nbsp;\\2";
	$text = eregi_replace($pattern, $replacement, $text);

	//mezera mezi číslovkami
	$pattern = "([0-9])([[:blank:]]{1})([0-9]{3})";
	$replacement = "\\1&nbsp;\\3";
	$text = eregi_replace($pattern, $replacement, $text);

	//české uvozovky
//	$pattern = "\"([^\\]+)\"";
//	$replacement = "&#8222;\\1&#8220;";
//	$text = eregi_replace($pattern, $replacement, $text);
	return $text;
 }
?>