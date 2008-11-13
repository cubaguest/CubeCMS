<?php
/**
 * Soubor s konfiguraci Javascript pluginu
 * a definici vsech pluginu
 */

/*
 * Zakladni adresar s pluginama
 */
define("PLUGIN_DIR", "./jscripts/");

//Plugin pro potvrzovani dialogu
$PLUGIN_SUBMIT_FORM = array("dir" =>"submit_form",
				  "jsfiles" => array("submit.js"),
				  "cssfiles" => null,
				  "default" => null,
				  "phpscript" => null);

//Plugin Tiny-MCE
$PLUGIN_TINY_MCE = array("dir" =>"tiny_mce",
				  "jsfiles" => array("tiny_mce.js"),
				  "cssfiles" => null,
				  "default" =>"tiny_mce_default.js",
				  "phpscript" => null);
/**
 * Pro tinyMCE soubor s obrázky, které jsou v listu při přidávání
 */
define("PLUGIN_TINY_MCE_IMAGES_FILE", "./cache/imageslist.js");
define("PLUGIN_TINY_MCE_IMAGES_FILE_TEXT_START", "var tinyMCEImageList = new Array(\n");
define("PLUGIN_TINY_MCE_IMAGES_FILE_TEXT_END", "\n);");

//Plugin lightbox pro zobrazeni fotek
$PLUGIN_LIGHT_BOX = array("dir" =>"lightbox",
				  "jsfiles" => array("prototype.js", "scriptaculous.js?load=effects", "lightbox.js"),
				  "cssfiles" => array("lightbox.css"),
				  "default" => null,
				  "phpscript" => null);

//Plugin thumbnailwiever pro zobrazeni fotek
$PLUGIN_THUMBNAIL_BOX = array("dir" =>"thumbnailwiev",
				  "jsfiles" => array("thumbnailviewer.js"),
				  "cssfiles" => array("thumbnailviewer.css"),
				  "default" => null,
				  "phpscript" => null);

/*
 * Plugin pro zobrazeni jednoducheho kalendare
 */
$PLUGIN_CALENDAR = array("dir" =>"calendar",
				  "jsfiles" => array("calendarDateInputs.js"),
				  "cssfiles" => null,
				  "default" => null,
				  "phpscript" => null);

/*
 * Plugin pro počítání znaků
 */
$PLUGIN_COUNTER = array("dir" =>"counter",
				  "jsfiles" => array("counter.js"),
				  "cssfiles" => null,
				  "default" => null,
				  "phpscript" => null);

/*
 * Plugin pro přidávání textu do textarey nebo inputu
 */
$PLUGIN_ADD_TO_TEXTAREA = array("dir" =>"addtotextarea",
				  				"jsfiles" => array("addtotextarea.js"),
				  				"cssfiles" => null,
				  				"default" => null,
				  				"phpscript" => null);

/*
 * Plugin pro nastavení scrolování na kotvu v dokumentu
 */
$PLUGIN_SCROLL_PAGE_TO_ANCHOR = array("dir" =>"scroll_page",
				  					  "jsfiles" => array("scroll_to_anchor.js"),
				  					  "cssfiles" => null,
				  					  "default" => null,
				  					  "phpscript" => null);

/*
 * Plugin pro nastavení scrolování na kotvu v dokumentu
 */
$PLUGIN_SCROLL_PAGE_TO_POSITION = array("dir" =>"scroll_page",
				  						"jsfiles" => array("scroll_to_position.js"),
				  						"cssfiles" => null,
				  						"default" => null,
				  						"phpscript" => null);

/**
 * Plugin pro zobrazování tooltipu (popisků u odkazů)
 *
 * Balloontip
 * @link http://www.dynamicdrive.com/dynamicindex5/balloontooltip.htm
 */
$PLUGIN_BALLOON_TIPS = array("dir" =>"tooltips",
				  			 "jsfiles" => array("balloontip.js"),
				  			 "cssfiles" => array("balloontip.css"),
				  			 "default" => null,
				  			 "phpscript" => null);


/**
 * Plugin pro rozbalování textů
 *
 * SwitchContent
 * @link http://www.dynamicdrive.com/dynamicindex17/switchcontent.htm
 */
$PLUGIN_SWITCH_CONTENT = array("dir" =>"switchcontent",
				  			   "jsfiles" => array("switchcontent.js"),
				  			   "cssfiles" => null,
//				  			   "default" => "switchcontentdf.js",
				  			   "default" => null,
				  			   "phpscript" => null);


/**
 * Plugin pro rozbalování textů s animací
 *
 * Accordion Content script
 * @link http://www.dynamicdrive.com/dynamicindex17/ddaccordion.htm
 */
$PLUGIN_ACCORDION_CONTENT = array("dir" =>"accordioncontent",
				  			   "jsfiles" => array("ddaccordion.js", "jquery-1.2.2.pack.js"),
				  			   "cssfiles" => null,
				  			   "default" => "ddaccordion_df.js",
//				  			   "default" => null,
				  			   "phpscript" => null);



/*
 * Plugin pro zobrazení smajlíků
 */
$PLUGIN_SMILES = array("dir" =>"smiles",
				  "jsfiles" => array("smiles.js"),
				  "cssfiles" => null,
				  "default" => null,
				  "phpscript" => array("smiles.php"));

/**
 * Pro tinyMCE soubor s obrázky, které jsou v listu při přidávání
 */
define("PLUGIN_TINY_MCE_IMAGES_FILE", "./cache/imageslist.js");
define("PLUGIN_TINY_MCE_IMAGES_FILE_TEXT_START", "var tinyMCEImageList = new Array(\n");
define("PLUGIN_TINY_MCE_IMAGES_FILE_TEXT_END", "\n);");


//Example
//Plugin lightbox pro zobrazeni fotek
//$PLUGIN_LIGHT_BOX = array("dir" =>"lightbox",
//				  "jsfiles" => array("prototype.js", "scriptaculous.js?load=effects", "lightbox.js"),
//				  "cssfiles" => array("lightbox.css"),
//				  "default" =>"tiny_mce_default.js");
?>
