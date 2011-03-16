<?php
/**
 * Třída JsPluginu Jquery Cookie.
 * Třída pro vkládání pluginu pro zobrazovaní popup oken s obrázkem.
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id: $ Cube CMS 7.2.1 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu JQuery Cookie
 * @see           http://www.jstree.com/
 */

class JsPlugin_JQueryCookie extends JsPlugin {
	protected function initJsPlugin() {}
	protected function setFiles() {
      $this->addDependJsPlugin(new JsPlugin_JQuery());
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("jquery.cookie.js"));
	}
}
?>