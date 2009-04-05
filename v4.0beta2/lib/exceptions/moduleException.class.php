<?php
/**
 * Třída pro obsluhu vyjímek modulů
 * Třída rozšiřuje třídu Exception o třídu pro práci s vyjímkami v modulech
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.1 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro obsluhu chyb modulů
 */
class ModuleException extends Exception {
   /**
    * Název Modulu
    * @var string
    */
   private $moduleName = null;

   public function  __construct($message = null, $code = null) {
      if(AppCore::getSelectedModule() == null){
         throw new BadMethodCallException(_('Nepovolené volání ModuleException'));
      }
      $this->moduleName = AppCore::getSelectedModule()->getName();
      parent::__construct($message, $code);
   }

   /**
    * Metoda vrací název modulu
    * @return string -- název modulu
    */
   public function getModuleName() {
      return $this->moduleName;
   }
}
?>
