<?php 
/*
 * php script k vygenerování smajlíků,
 * je součástí pluginu a integruje se přes přidání pluginu
 */

/*
 * Pole se smajlíky
 */
$smilesArray = array(":-)" => "smiley-smile.gif", ":-(" => "smiley-frown.gif", ":-D" => "smiley-laughing.gif",
					 ";-)" => "smiley-wink.gif", ":,-(" => "smiley-cry.gif", ":-P" => "smiley-tongue-out.gif",
					 ":-o" => "smiley-surprised.gif", ":-O" => "smiley-yell.gif", ":-|" => "smiley-undecided.gif",
					 ":-*" => "smiley-kiss.gif", ":-/" => "smiley-foot-in-mouth.gif", ":-&#047;" => "smiley-foot-in-mouth.gif",
					 "B-)" => "smiley-cool.gif", ":-@" => "smiley-embarassed.gif", ":-&#064;" => "smiley-embarassed.gif",
					 "O:-)" => "smiley-innocent.gif", "$-)" => "smiley-money-mouth.gif", "&#036;-)" => "smiley-money-mouth.gif",
					 ":-X" => "smiley-sealed.gif");


//	Uprava pole smajliku a pridani cest k images do pole
	foreach ($smilesArray as $key => $value) {
//		$smilesArray[$key]= "./lib/".$category->getModuleName()."/images/".$value;
		$smilesArray[$key]= MAIN_IMAGES_DIR.PATH_TO_SMILES_IMAGES.$value;
	}


   //$mainTpl->assign("PATH_TO_SMILES", MAIN_IMAGES_DIR.PATH_TO_SMILES_IMAGES);
	$mainTpl->assign("SMILES_ARRAY", $smilesArray);
?>
