<?php

/**
 * Třída pro obsluhu vlastností mmodulu
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: module.class.php 826 2010-01-18 13:17:32Z jakub $ VVE3.9.4 $Revision: 826 $
 * @author        $Author: jakub $ $Date: 2010-01-18 14:17:32 +0100 (Po, 18 led 2010) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2010-01-18 14:17:32 +0100 (Po, 18 led 2010) $
 * @abstract 		Třída pro obsluhu vlastností modulu
 */
class Module_Core {

   private $name = null;
   private $template = null;
   private $link = null;

   public function __construct() {
      $this->link = new Url_Link();
      $this->template = new Template($this->link);
   }

   /**
    * Metoda vrací název modulu
    * @return string
    */
   public function getName() {
      return $this->name;
   }

   public function runController() {

   }

   public function runView() {

   }

   /**
    * Metoda vrací aktuální odkaz
    * @return Url_Link 
    */
   public function link() {
      return $this->link;
   }

   /**
    * Metoda vrací objekt šablony
    * @return Template
    */
   public final function template() {
      return $this->template;
   }
}

?>
