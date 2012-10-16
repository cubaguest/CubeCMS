<?php
/**
 * Třída JsPluginu SuperFish Menu
 *
 * @copyright  	Copyright (c) 2008-2012 Jakub Matas
 * @version    	$Id: $ VVE 7.16.0 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu Superfish Menu
 */

class JsPlugin_SuperfishMenu extends JsPlugin {
   protected $isVertical = false;

	protected function initJsPlugin() {
//      $this->setJsPluginName('jquerysmlmenu');
      $this->setJsFilesDir('superfish');
	}

   public function setVertical($v = false)
   {
      $this->isVertical = $v;
   }
	
	protected function setFiles() {
      $jquery = new JsPlugin_JQuery();
      $jquery->addJQPlugin('hoverIntent');
      $this->addDependJsPlugin($jquery);
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("supersubs.js"));
		$this->addFile(new JsPlugin_JsFile("superfish.js"));
		$this->addFile(new JsPlugin_CssFile("superfish.css"));
      if($this->isVertical){
         $this->addFile(new JsPlugin_CssFile("superfish-vertical.css"));

      }
	}
}
?>