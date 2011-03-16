<?php
/**
 * Třída JsPluginu JsTree.
 * Třída pro vkládání pluginu pro zobrazovaní popup oken s obrázkem.
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id: $ Cube CMS 7.2.1 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu JsTree
 * @see           http://www.jstree.com/
 */

class JsPlugin_JsTree extends JsPlugin {
   protected $config = array('theme' => 'classic');

	protected function initJsPlugin() {
      
	}
	
	protected function setFiles() {
      $this->addDependJsPlugin(new JsPlugin_JQuery());
      $this->addDependJsPlugin(new JsPlugin_JQueryCookie());
//		Přidání css stylu
		$this->addFile(new JsPlugin_CssFile('themes/'.$this->getCfgParam('theme').'/style.css'));
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("jquery.jstree.js"));
	}
}
?>