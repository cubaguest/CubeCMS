<?php
/**
 * Třída JsPluginu SMLMenu (Simple Multi Level Menu)
 * Třída pro vkládání pluginu pro práci s menu (víceúrovňové menu, které se rouevírá při kliknutí)
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu SMLMenu
 */

class JsPlugin_SMLMenu extends JsPlugin {
	protected function initJsPlugin() {
      $this->setJsPluginName('jquerysmlmenu');
	}
	
	protected function setFiles() {
      $jquery = new JsPlugin_JQuery();
      $this->addDependJsPlugin($jquery);
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("jquery.smlmenu.min.js"));
	}
}
?>