<?php
/**
 * Třída JsPluginu PrettyPhoto.
 * Třída pro vkládání pluginu pro zobrazovaní popup oken s obrázkem.
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id: $ VVE 7.6 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu PrettyPhoto
 * @see           http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/
 */

class JsPlugin_PrettyPhoto extends JsPlugin {
	protected function initJsPlugin() {}
	
	protected function setFiles() {
      $this->addDependJsPlugin(new JsPlugin_JQuery());
      //		Přidání css stylu
		$this->addFile(new JsPlugin_CssFile('prettyPhoto.css'));
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("jquery.prettyPhoto.js"));
	}
}
?>