<?php

/**
 * Pomocná třída JsPluginu a Epluginu pro práci Less Soubory.
 * Třída slouží pro práci s javascript soubory v JsPluginu a Epluginu. Umožňuje
 * jednoduché nasatvené parametrů souboru.
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: jsplugin_jsfile.class.php 649 2009-09-20 08:31:42Z jakub $ VVE3.9.4 $Revision: 649 $
 * @author			$Author: jakub $ $Date: 2009-09-20 10:31:42 +0200 (Sun, 20 Sep 2009) $
 * 						$LastChangedBy: jakub $ $LastChangedDate: 2009-09-20 10:31:42 +0200 (Sun, 20 Sep 2009) $
 * @abstract 		Třida pro práci s javascript soubry
 */
class JsPlugin_LessFile extends JsPlugin_CssFile {

   function __construct($file, $virtual = false, $dir = null) {
		$this->file = $file;
      $this->virtualFile = false;
      $this->dir = AppCore::getAppWebDir().Template::JAVASCRIPTS_DIR.DIRECTORY_SEPARATOR.'JSPLUGINNAME'.DIRECTORY_SEPARATOR;
	}
   
   function __toString()
   {
      return $this->compileLess();
   }

   protected function compileLess()
   {
      require_once AppCore::getAppLibDir().CUBECMS_LIB_DIR.DIRECTORY_SEPARATOR."nonvve".DIRECTORY_SEPARATOR."lessphp".DIRECTORY_SEPARATOR."lessc.inc.php";
      
      $rpFile = str_replace('/', DIRECTORY_SEPARATOR, $this->file);
      $rpMainDir = AppCore::getAppLibDir().Template::JAVASCRIPTS_DIR.DIRECTORY_SEPARATOR.$this->getPluginName().DIRECTORY_SEPARATOR;
      $rpFaceDir = Template::faceDir().Template::JAVASCRIPTS_DIR.DIRECTORY_SEPARATOR.$this->getPluginName().DIRECTORY_SEPARATOR;
      $rpParentFaceDir = Template::faceDir(true).Template::JAVASCRIPTS_DIR.DIRECTORY_SEPARATOR.$this->getPluginName().DIRECTORY_SEPARATOR;
      
      if(VVE_SUB_SITE_DIR != null){
         $rpParentFaceDir = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $rpFaceDir);
      }
      
      $path = $url = null;
      if(is_file($rpFaceDir.$rpFile)){ // soubor z face webu
         $path = $rpFaceDir;
      } else if(VVE_SUB_SITE_DIR != null AND is_file($rpParentFaceDir.$rpFile)) { // soubor z nadřazeného face (subdomains)
         $path = $rpParentFaceDir;
      } else if(is_file($rpMainDir.$rpFile)) { // soubor v knihovnách
         $path = $rpMainDir;
      } else {
         $tr = new Translator();
         CoreErrors::addException(new Template_Exception(sprintf($tr->tr('Soubor "%s%s" nebyl nalezen'), $rpMainDir, $rpFile)));
      }

      $url = Url_Request::getBaseWebDir(false).AppCore::ENGINE_CACHE_DIR."/".Template::STYLESHEETS_DIR."/"
          .$this->getPluginName().'/'. str_replace(DIRECTORY_SEPARATOR, '/', pathinfo($rpFile, PATHINFO_DIRNAME)).'/';
      $targetPath = AppCore::getAppCacheDir().Template::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.$this->getPluginName().DIRECTORY_SEPARATOR;

      $dir = pathinfo($targetPath.$rpFile, PATHINFO_DIRNAME);
      FS_Dir::checkStatic($dir);
      
      try {
         $compiledFileUrl = null;
         $options = array( 
            'relativeUrls' => false,
            'sourceMap' => (VVE_DEBUG_LEVEL != 0),
            'sourceMapWriteTo'  => $targetPath.$rpFile.'.map',
            'sourceMapURL'      => Url_Link::getWebURL().'cache/'.Template::STYLESHEETS_DIR.'/'.$this->getPluginName().'/'.$rpFile.'.map',
            'sourceMapRootpath' => Url_Link::getWebURL(),
            'sourceMapBasepath' => AppCore::getAppWebDir(),
            'import_dirs' => array( 
               Face::getCurrent()->getDir().'stylesheets/' => '/faces/'.Face::getCurrent()->getName()."/stylesheets/", // face styles 
               AppCore::getAppLibDir().'faces/'.Face::getCurrent()->getName()."/stylesheets/" => '/faces/'.Face::getCurrent()->getName()."/stylesheets/", // face styles 
               AppCore::getAppWebDir().'stylesheets/' => '/stylesheets/', // base styles 
               AppCore::getAppLibDir().'stylesheets/' => '/stylesheets/', // base styles 
               AppCore::getAppLibDir().'jscripts/'.$this->getPluginName().'/' => 'jscripts/'.$this->getPluginName().'/', // plugin dir styles 
            ),
            'compress' => (VVE_DEBUG_LEVEL == 0),
            'cache_dir'=> $dir,
         );
         $css_file_name = Less_Cache::Get( 
             array( $path.$rpFile => '/cache/'.Template::STYLESHEETS_DIR.'/'.$this->getPluginName().'/' ), 
             $options, Template::getLessVariables() );
         
         $compiledFileUrl = $url.$css_file_name;
      } catch (Exception $exc) {
         new CoreErrors($exc);
      }
      return $compiledFileUrl;
   }

}
