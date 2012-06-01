<?php

/**
 * Třída pro obsluhu vlastností mmodulu jádra
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: module.class.php 826 2010-01-18 13:17:32Z jakub $ VVE3.9.4 $Revision: 826 $
 * @author        $Author: jakub $ $Date: 2010-01-18 14:17:32 +0100 (Po, 18 led 2010) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2010-01-18 14:17:32 +0100 (Po, 18 led 2010) $
 * @abstract 		Třída pro obsluhu vlastností modulu
 */
class Module_Core extends TrObject {

   private $name = null;
   private $template = null;
   private $link = null;
   private $category = null;


   public function __construct(Category_Core $moduleCategory) 
   {
      $this->link = new Url_Link();
      $this->template = new Template($this->link);
      $this->category = $moduleCategory;
      $this->link->category($moduleCategory->getUrlKey());
   }

   public function __toString() 
   {
      return (string)$this->name;
   } 
   
   /**
    * Metoda vrací název modulu
    * @return string
    */
   public function getName() 
   {
      return $this->name;
   }

   public function runController() 
   {}

   public function runView() 
   {}

   /**
    * Metoda vrací aktuální odkaz
    * @return Url_Link 
    */
   public function link() 
   {
      return $this->link;
   }

   /**
    * Metoda vrací objekt šablony
    * @return Template
    */
   public final function template() 
   {
      return $this->template;
   }
}

?>
