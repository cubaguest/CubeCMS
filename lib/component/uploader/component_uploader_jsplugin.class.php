<?php
/**
 * Třída JsPluginu komponenty Uploader.
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id: $ VVE7.3.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída JsPluginu komponenty Uploader
 */

class Component_Uploader_JsPlugin extends JsPlugin {


   protected function initJsPlugin() {
      $this->setJsFilesDir('uploader');
   }

   protected function setFiles() {
      $this->addFile(new JsPlugin_JsFile("fileuploader.js"));
//      $this->addFile(new JsPlugin_CssFile("fileuploader.css"));
   }
}
?>