<?php
/**
 * Třída JsPluginu TextAreaAutoResize
 * Třída pro vkládání pluginu pro automatickou změnu velikost textarea
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.0.5 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu TextArea Resizer
 * @see           http://plugins.jquery.com/project/TextAreaResizer
 */

class JsPlugin_TextAreaAutoResize extends JsPlugin {
	protected function initJsPlugin() {
      $this->setJsPluginName('jquerytextareaautoresize');
	}
	
	protected function setFiles() {
      $jquery = new JsPlugin_JQuery();
      $this->addDependJsPlugin($jquery);
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("jquery.autoresize.js"));
	}
}
?>