<?php
/**
 * Třída pro práci s šablonou nápovědy.
 *
 * @copyright  	Copyright (c) 2008-2012 Jakub Matas
 * @version    	$Id: $ VVE 7.14 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate:  $
 * @abstract 		Třída pro obsluhu šablony nápovědy
 */


class Template_Help extends Template {
   /**
    * Objekt kategorie pro kterou je šablona tvořena
    * @var Category
    */
   private $module = null;
   
   private $moduleAction = null;

   /**
    * KOnstruktor vytvoří objekt šablony pro modul
    * @param Url_Link_Module $link -- objekt odkazu
    * @param Category $category -- objekt kategorie
    */
   function  __construct($action = null, $module = null) {
      parent::__construct(new Url_Link());
      $this->module = $module;
      $this->moduleAction = $action;
   }

   protected function loadHelpFile($file) 
   {
      $this->addFile('tpl://help_box.phtml');
      ob_start();
      include $file;
      $this->content = ob_get_clean();
   }
   
   public function __toString() 
   {
      // pokus o načtení nápovědy k danému modulu a akci
      
      $file = AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$this->module
         .DIRECTORY_SEPARATOR.AppCore::DOCS_DIR.DIRECTORY_SEPARATOR.'help_'.$this->moduleAction.'_'.Locales::getLang().'.phtml';
      $fileCS = AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$this->module
         .DIRECTORY_SEPARATOR.AppCore::DOCS_DIR.DIRECTORY_SEPARATOR.'help_'.$this->moduleAction.'_cs.phtml';
      if(is_file($file)){ // načtení z aktuálního modulu
         $this->loadHelpFile($file);
      } else if(is_file($fileCS)){ // načtení z aktuálního modulu
         $this->loadHelpFile($fileCS);
      } else {
         $pClass = get_parent_class($this->module."_Controller");
         while ($pClass != false && $pClass != 'Controller'){
            $module = strtolower( substr($pClass, 0, -11) ); // remove _Controller word
            $file = AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module
               .DIRECTORY_SEPARATOR.AppCore::DOCS_DIR.DIRECTORY_SEPARATOR.'help_'.$this->moduleAction.'_'.Locales::getLang().'.phtml';
            $fileCS = AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module
               .DIRECTORY_SEPARATOR.AppCore::DOCS_DIR.DIRECTORY_SEPARATOR.'help_'.$this->moduleAction.'_cs.phtml';

            if(is_file($file)){
               $this->loadHelpFile($file);
               break;
            } else if(is_file($fileCS)){
               $this->loadHelpFile($fileCS);
               break;
            }
            $pClass = get_parent_class($pClass);
         }
      }
      return parent::__toString();
   }
}
?>