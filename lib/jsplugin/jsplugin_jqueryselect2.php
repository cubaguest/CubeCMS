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

class JsPlugin_JQueryTagsInput extends JsPlugin {
	protected function initJsPlugin() {
      $this->setJsPluginName('jquerytagsinput');
	}
	
	protected function setFiles() {
	   $jq = new JsPlugin_JQuery();
	   $jq->addUIAutoComplete();
      $this->addDependJsPlugin($jq);
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("jquery.tagsinput.min.js"));
		$this->addFile(new JsPlugin_CssFile("jquery.tagsinput.css"));
	}
}
?>