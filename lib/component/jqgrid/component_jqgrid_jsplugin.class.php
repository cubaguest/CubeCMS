<?php
/**
 * Třída JsPluginu JqGrid
 * Třída pro vkládání pluginu pro zobrazovaní tabulek
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.0.8 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu JqGrid
 * @see           http://www.trirand.com/
 */

class Component_JqGrid_JsPlugin extends JsPlugin {
	protected function initJsPlugin() {
      $this->setJsPluginName('jqgrid');
	}
	
	protected function setFiles() {
      $jquery = new JsPlugin_JQuery();
      $jquery->addUICore();
      $this->addDependJsPlugin($jquery);
//		Přidání css stylu
		$this->addFile(new JsPlugin_CssFile('css/ui.jqgrid.css'));
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("i18n/grid.locale-".Locale::getLang().".js"));
		$this->addFile(new JsPlugin_JsFile("jquery.jqGrid.min.js"));
//		$this->addFile(new JsPlugin_JsFile("jquery.jqgrid.loader.js"));
	}
}
/*
 * Rozdělit do částí:
 * 1. base
 * 2. modal edit
 * 3. form edit (inside)
 */
?>