<?php
/**
 * Třída JsPluginu Jquery Color Picker
 * Třídá vkládá plugin pro tvorbu palety pro výáběr barvy
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id: $ VVE 7.4.0 $Revision: $
 * @author			$Author: $ $Date:  $
 *						$LastChangedBy:  $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu Jquery Color Picker (input text)
 */

class JsPlugin_JQueryColorPicker extends JsPlugin {
	protected function initJsPlugin() {}
	
	protected function setFiles() {
      $this->addDependJsPlugin(new JsPlugin_JQuery());
      //		Přidání css stylu
		$this->addFile(new JsPlugin_CssFile('jquery.color-picker.css'));
		//		Přidání js soubrů pluginu
		if(VVE_DEBUG_LEVEL == 0){
         $this->addFile(new JsPlugin_JsFile("jquery.color-picker.min.js"));
      } else {
         $this->addFile(new JsPlugin_JsFile("jquery.color-picker.js"));
      }
	}
}
?>