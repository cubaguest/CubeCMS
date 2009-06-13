<?php
/**
 * Abstraktní třída pro Model.
 * Třída pro základní vytvoření objektu modelu, jak souborového tak 
 * databházového. Obsahuje pouze přístup k vybranému modulu.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Abstraktní třída pro vytvoření modelu
 */

abstract class Model {
   
   /**
    * Proměná obsahuj eobjek tsystémových proměných
    * @var ModuleSys 
    */
   private $sys = null;

   /**
    * KOnstruktor vytvoří objekt modelu
    * @param ModuleSys $sys -- systémové informace
    */
   public function  __construct(ModuleSys $sys = null) {
      if($sys != null){
      $this->sys = $sys;
      } else {
         $this->sys = new ModuleSys();
      }

      $this->init();
   }

   /**
    * Metoda vrací systémový objekt
    * @return ModuleSys -- objekt systému
    */
   final public function sys() {
      return $this->sys;
   }

	/**
	 * Metoda vrací objekt modulu
	 * @return Module -- objekt modulu
	 */
	final public function module() {
      return $this->sys()->module();
	}




	/**
	 * Abstraktní metoda pro inicializaci modelu pokud je třeba
	 */
	protected function init(){}	
}
?>