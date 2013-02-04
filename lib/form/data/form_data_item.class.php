<?php

/**
 * Třída Form_Data
 *
 * Datové úložiště pro formulářová data
 *
 * @copyright     Copyright (c) 2008-2011 Jakub Matas
 * @version       $Id:  $ VVE 7.3 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída
 */
class Form_Data_Item extends TrObject {

   private $name = null;

   private $elementType = null;

   private $value = null;

   private $note = null;

   public function __construct($elementType, $name, $value = null, $note = null)
   {
      $this->setElementType($elementType);
      $this->setName($name);
      $this->setValue($value);
      $this->setNote($note);
   }


   public function setElementType($name)
   {
      $this->elementType = $name;
      return $this;
   }

   public function setName($name)
   {
      $this->name = $name;
      return $this;
   }

   public function setValue($val)
   {
      $this->value = $val;
      return $this;
   }

   public function setNote($note)
   {
      $this->note = $note;
      return $this;
   }

   public function getElementType()
   {
      return $this->elementType;
   }

   public function getName()
   {
      return $this->name;
   }

   public function getValue()
   {
      return $this->value;
   }

   public function getNote()
   {
      return $this->note;
   }

   public function __sleep()
   {
      return array('name', 'value', 'note');
   }

   public function __wakeup()
   {
   }
}
?>
