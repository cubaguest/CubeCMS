<?php
/**
 * Třída pro obsluhu vyjímek modulů
 * Třída rozšiřuje třídu Exception o třídu pro práci s vyjímkami v modulech
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: moduleException.class.php 636 2009-07-07 20:17:18Z jakub $ VVE3.9.1 $Revision: 636 $
 * @author        $Author: jakub $ $Date: 2009-07-07 22:17:18 +0200 (Út, 07 čec 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-07-07 22:17:18 +0200 (Út, 07 čec 2009) $
 * @abstract      Třída pro obsluhu chyb modulů
 */
class ModuleException extends Exception {
   /**
    * Název Modulu
    * @var string
    */
   private $moduleName = null;

   public function  __construct($message = null, $code = null, $moduleName = null) {
      $this->moduleName = $moduleName;
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
