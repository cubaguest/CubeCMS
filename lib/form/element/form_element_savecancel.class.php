<?php
/**
 * Třída pro obsluhu skupiny elemntů
 *
 * @copyright  	Copyright (c) 2010 Jakub Matas
 * @version    	$Id: $ VVE 6.4 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu skupiny elementů
 */
class Form_Element_SaveCancel extends Form_Element {
   private $labels = array();


   protected function init() {
      $this->htmlElement = new Html_Element('input');
      $this->html()->setAttrib('type', 'submit');
      if($this->getLabel() == null){
         $this->labels = array(_('Uložit'),_('Zrušit'));
      } else {
         if(!is_array($this->getLabel())){
            throw new UnexpectedValueException(_('Pro skupinu elementů SaveCancel musí být label zadán jako pole se dvěma popiskama'));
         }
         $this->labels = $this->getLabel();
      }
   }

   public function populate() {
      parent::populate();
      if($this->unfilteredValues == $this->labels[0]){
         $this->unfilteredValues = $this->values = true;
      } else {
         $this->unfilteredValues = $this->values = false;
      }

   }
   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      
      $this->setValues($this->labels[0]);
      $this->html()->clearClasses();
      $this->html()->addClass('button-save');
      $ctrlSave = (string)parent::controll();
      $this->setValues($this->labels[1]);
      $this->html()->clearClasses();
      $this->html()->addClass('button-cancel');
      $ctrlCancel = (string)parent::controll();
      return $ctrlSave.$ctrlCancel;
   }

   /**
    * Metoda vrací label
    * @return string
    */
   public function label() {
      return null;
   }

   public function  __toString() {
      return (string)$this->controll();
   }
}
?>
