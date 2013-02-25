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
   protected $cssFile = null;

	protected function initJsPlugin() {
//      $this->setJsPluginName('jquerysmlmenu');
      $this->setJsFilesDir('superfish');
	}

   public function setVertical($v = false)
   {
      $this->isVertical = $v;
   }

   /**
    * Nasatví externí css soubor
    * @param null $css
    */
   public function setCss($css = false)
   {
      if($css != null){
         $css = tp(Template::STYLESHEETS_DIR."/".$css);
      }
      $this->cssFile = $css;
   }

	protected function setFiles() {
      $jquery = new JsPlugin_JQuery();
      $jquery->addJQPlugin('hoverIntent');
      $this->addDependJsPlugin($jquery);
		//		Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("supersubs.js"));
		$this->addFile(new JsPlugin_JsFile("superfish.js"));
      if($this->cssFile === false){
         $this->addFile(new JsPlugin_CssFile("superfish.css?nocompress"));
         if($this->isVertical){
            $this->addFile(new JsPlugin_CssFile("superfish-vertical.css?nocompress"));

         }
      } else if($this->cssFile != null){
         $this->addFile($this->cssFile);
      }
   }
}
?>