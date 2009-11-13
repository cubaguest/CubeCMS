<?php
/**
 * Třída JsPluginu Jquery DorpDown
 * Třídá vkládá plugin pro tvorbu selectů s html kódem (lepší stylování, podpora obrázků)
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author			$Author: $ $Date:  $
 *						$LastChangedBy:  $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu Jquery DropDown (input select)
 * @see           http://marghoobsuleman.com/
 */

class JsPlugin_JQueryDropDown extends JsPlugin {
	protected function initJsPlugin() {
	}
	
	protected function setFiles() {
      $this->addDependJsPlugin(new JsPlugin_JQuery());
//		Přidání css stylu
		$this->addFile(new JsPlugin_CssFile('jquery.dd.css'));
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("jquery.dd.js"));
	}
}
?>