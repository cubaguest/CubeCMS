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
   protected $formElementLabel = array();

   private $cancelConfirmMsg = true;


   protected function init() {
      $this->htmlElement = new Html_Element('input');
      $this->html()->setAttrib('type', 'submit');
      if($this->getLabel() == null){
         $this->formElementLabel = array($this->tr('Uložit'),$this->tr('Zrušit'));
      } else {
         if(!is_array($this->getLabel())){
            throw new UnexpectedValueException($this->tr('Pro skupinu elementů SaveCancel musí být label zadán jako pole se dvěma popiskama'));
         }
         $this->formElementLabel = $this->getLabel();
      }
   }

   public function setCancelConfirm($param = true) {
      $this->cancelConfirmMsg = $param;
   }

   /**
    * Metoda vrací jestli byl element vůbec odeslán
    * @return bool
    */
   public function isSend() {
      if(isset ($_REQUEST[$this->getName().'_ok']) OR isset ($_REQUEST[$this->getName().'_cancel'])){
         return true;
      }
      return false;
   }

   public function populate() {
      if(isset ($_REQUEST[$this->getName().'_ok'])) {
         $this->values = true;
      } else {
         $this->values = false;
      }
      $this->unfilteredValues = $this->values;
      $this->isPopulated = true;

   }
   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      $this->setValues($this->formElementLabel[0]);
      $this->html()->clearClasses();
      $this->html()->removeAttrib('onclick');
      $this->html()->addClass('button-save');
      $ctrlSave = clone parent::controll();
      $this->renderedId--;
      $ctrlSave->setAttrib('name', $this->getName().'_ok');
      $ctrlSave->setAttrib('id', $this->getName().'_ok_'.$this->renderedId);

      $this->setValues($this->formElementLabel[1]);
      $this->html()->clearClasses();
      if($this->cancelConfirmMsg == true){
         $this->html()->setAttrib('onclick', 'return confirm(\''.$this->tr('Opravdu zrušit změny?').'\')');
      }
      $this->html()->addClass('button-cancel');
      $ctrlCancel = clone parent::controll();
      $this->renderedId--;
      $ctrlCancel->setAttrib('name', $this->getName().'_cancel');
      $ctrlCancel->setAttrib('id', $this->getName().'_cancel_'.$this->renderedId);
      $this->renderedId++;
      return (string)$ctrlSave.(string)$ctrlCancel;
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
