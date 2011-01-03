<?php
/**
 * Třída pro překlad textů
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id:  $ VVE 6.4 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro překlady
 */

class Translator_Module extends Translator {
   public function  __construct($domain = 'engine') {
      $this->domain = strtolower($domain);
      parent::__construct();
   }
      /**
    *  Metoda načte překlady
    */
   protected function loadTranslations(){
      $this->loadFile(AppCore::getAppWebDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$this->domain.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR,
         Template::faceDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$this->domain.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR);
   }

   public function apppendDomain($domain){
      $this->loadFile(AppCore::getAppWebDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$domain.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR,
         Template::faceDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$domain.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR);
   }
}
?>
