<?php
/**
 * Třída JsPluginu Dropzone.
 * Třída pro vkládání pluginu pro upload obrízků s náhledy
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: jsplugin_lightbox.class.php 650 2009-09-21 11:13:26Z jakub $ VVE3.9.1 $Revision: 650 $
 * @author			$Author: jakub $ $Date: 2009-09-21 13:13:26 +0200 (Po, 21 zář 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-09-21 13:13:26 +0200 (Po, 21 zář 2009) $
 * @abstract 		Třída JsPluginu PiroBox
 * @see           http://www.pirolab.it/pirobox/
 */

class Component_DropZone_JsPlugin extends JsPlugin {
   
//   protected $respond = array();


   protected function initJsPlugin() 
   {
      $this->setJsFilesDir('dropzone');
	}
	
	protected function setFiles() 
   {
      $this->addDependJsPlugin(new JsPlugin_JQuery());
      //	Přidání css stylu
		$this->addFile(new JsPlugin_CssFile('css/basic.css'));
//		$this->addFile(new JsPlugin_CssFile('css/dropzone.css'));
		//	Přidání js soubrů pluginu
		$this->addFile(new JsPlugin_JsFile("dropzone.min.js"));
	}
   
   public function getJSCode($params = array())
   {
      $params += array(
         'selector' => '.dropzone',
         'postData' => array(),
         'images' => array(),
         'maxFileSize' => 1024*1024*2, // 2MB
         'maxFiles' => 10, // 2MB
         'path' => AppCore::getAppCacheDir().uniqid('dropzone'), // 
         'parallelUploads' => 5, // 2MB
          
      );
      $linksComponent = new Url_Link_Component('DropZone');
      $linkDelete  = (string)$linksComponent->onlyAction('deleteFile', 'json');
      $linkUpload  = (string)$linksComponent->onlyAction('uploadFile', 'json');
      $linkImages  = (string)$linksComponent->onlyAction('getuploadedfiles', 'json');
      ob_start();
      include dirname(__FILE__).DIRECTORY_SEPARATOR.'baseJsCode.php';
      
      return ob_get_clean();
   }
}