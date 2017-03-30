<?php
/**
 * Třída Bootstrap typehead
 * 
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 8.7.6 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu TypeHead
 * @see          https://github.com/bassjobsen/Bootstrap-3-Typeahead 
 */

class JsPlugin_BootstrapTypehead extends JsPlugin {
	protected function initJsPlugin() {
      $this->setJsPluginName('bootstrap-typehead');
      $this->setJsFilesDir('bootstrap-typehead');
	}
	
	protected function setFiles() {
      $this->addDependJsPlugin(new JsPlugin_JQuery());
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("bootstrap3-typeahead.min.js"));
	}
}