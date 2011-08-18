<?php

/**
 * Třída Form_Data
 * 
 * Datové úložiště pro formulářová data
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id:  $ VVE 7.3 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída 
 */
class Form_Data_Header {

   private $name = null;
   
   private $note = null;
   
   public function __construct($name, $note = null)
   {
      $this->setName($name);
      $this->setNote($note);
   }


   public function setName($name)
   {
      $this->name = $name;
      return $this;
   }
   
   public function setNote($note)
   {
      $this->note = $note;
      return $this;
   }
   
   public function getName()
   {
      return $this->name;
   }
   
   public function getNote()
   {
      return $this->note;
   }
   
}
?>
