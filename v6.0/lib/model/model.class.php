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
    * KOnstruktor vytvoří objekt modelu
    * @param Module_Sys $sys -- systémové informace
    */
   public function  __construct() {
//      if($sys != null){
//      $this->sys = $sys;
//      } else {
//         $this->sys = new Module_Sys();
//      }
      $this->init();
   }

	/**
	 * Abstraktní metoda pro inicializaci modelu pokud je třeba
	 */
	protected function init(){}	
}
?>