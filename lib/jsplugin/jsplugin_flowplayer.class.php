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

class JsPlugin_FlowPlayer extends JsPlugin {
	protected function initJsPlugin() {
      $this->setJsPluginName('flowplayer');
	}
	
	protected function setFiles() {
		$this->addFile(new JsPlugin_JsFile("flowplayer-3.2.8.min.js"));
	}
   
   public static function getPlayerSwf()
   {
      return Url_Request::getBaseWebDir(true).self::JSPLUGINS_BASE_DIR."/flowplayer/flowplayer-3.2.8.swf";
   }
}
?>