<?php
/**
 * Třída JsPluginu DataTables.
 * Třída pro vkládání pluginu pro práci s tabulkama (filtrování, řazení, ...).
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.0.1 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu DataTables
 * @see           http://www.datatables.net/
 */

class JsPlugin_DataTables extends JsPlugin {
	protected function initJsPlugin() {
      $this->setJsPluginName('jquerydatatables');
	}
	
	protected function setFiles() {
      $this->addDependJsPlugin(new JsPlugin_JQuery());
//		Přidání css stylu
		$this->addFile(new JsPlugin_CssFile('datatables.css'));
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("jquery.dataTables.min.js"));
	}

   public function addHiddenNodesPlugin() {
      $this->addFile(new JsPlugin_JsFile("gethiddennodes.plugin.js", false, 'plugins/'));
   }
}
?>