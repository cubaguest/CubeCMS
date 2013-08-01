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
   public function  __construct($domain = self::PRIMARY_DOMAIN) {
      parent::__construct($domain);

      $this->translationsS = array_merge(
         isset(self::$translators[self::PRIMARY_DOMAIN]) ? self::$translators[self::PRIMARY_DOMAIN]->getSingulars() : array(),
         self::$translators[$this->domain]->getSingulars());
      $this->translationsP = array_merge(
         isset(self::$translators[self::PRIMARY_DOMAIN]) ? self::$translators[self::PRIMARY_DOMAIN]->getPlurals() : array(),
         self::$translators[$this->domain]->getPlurals());
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
