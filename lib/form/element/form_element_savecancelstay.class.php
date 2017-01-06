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
class Form_Element_SaveCancelStay extends Form_Element {
   
   const STATE_SAVE_CLOSE = 1;
   const STATE_SAVE = 2;
   const STATE_CANCEL = 0;


   protected $formElementLabel = array();

   private $cancelConfirmMsg = true;

   protected function init() {
      $this->htmlElement = new Html_Element('button');
      $this->html()->setAttrib('type', 'submit');
      if($this->getLabel() == null){
         $this->formElementLabel = array($this->tr('Uložit'),$this->tr('Uložit a zavřít'), $this->tr('zavřít'));
      } else {
         if(!is_array($this->getLabel())){
            throw new UnexpectedValueException($this->tr('Pro skupinu elementů SaveCancel musí být label zadán jako pole se dvěma popiskama'));
         }
         $this->formElementLabel = $this->getLabel();
      }
      $this->cssClasses += array(
          'confirmClass' => array('btn', 'btn-success'),
          'cancelClass' => array('btn', 'btn-danger'),
          );
   }

   public function setCancelConfirm($param = true) {
      $this->cancelConfirmMsg = $param;
   }

   /**
    * Metoda vrací jestli byl element vůbec odeslán
    * @return bool
    */
   public function isSend() {
      if(isset ($_REQUEST[$this->getName().'_ok']) OR isset ($_REQUEST[$this->getName().'_okstay']) OR isset ($_REQUEST[$this->getName().'_cancel'])){
         return true;
      }
      return false;
   }

   public function populate() {
      if(isset ($_REQUEST[$this->getName().'_ok'])) {
         $this->values = self::STATE_SAVE;
      } else if(isset ($_REQUEST[$this->getName().'_ok_close'])) {
         $this->values = self::STATE_SAVE_CLOSE;
      } else {
         $this->values = self::STATE_CANCEL;
      }
      $this->unfilteredValues = $this->values;
      $this->isPopulated = true;

   }
   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function control($renderKey = null) {
      $rKey = $renderKey != null ? $renderKey : $this->renderedId;
      
      // save button
      $this->setValues($this->formElementLabel[0]);
      $this->html()->clearClasses();
      $this->html()->removeAttrib('onclick');
      foreach ($this->cssClasses['confirmClass'] as $class) {
         $this->html()->addClass($class);
      }
      
      $ctrlSave = clone parent::control($renderKey);
      $this->renderedId--;
      $ctrlSave->setContent($this->formElementLabel[0]);
      $ctrlSave->setAttrib('name', $this->getName().'_ok');
      $ctrlSave->setAttrib('id', $this->getName().'_ok_'.$rKey);

      // save and stay button
      $this->setValues($this->formElementLabel[1]);
      $this->html()->clearClasses();
      $this->html()->removeAttrib('onclick');
      foreach ($this->cssClasses['confirmClass'] as $class) {
         $this->html()->addClass($class);
      }
      
      $ctrlSaveStay = clone parent::control($renderKey);
      $this->renderedId--;
      $ctrlSaveStay->setContent($this->formElementLabel[1]);
      $ctrlSaveStay->setAttrib('name', $this->getName().'_ok_close');
      $ctrlSaveStay->setAttrib('id', $this->getName().'_ok_close_'.$rKey);
      
      // cancel button
      $this->setValues($this->formElementLabel[2]);
      $this->html()->clearClasses();
      
      if($this->cancelConfirmMsg == true){
         $this->html()->setAttrib('onclick', 'return confirm(\''.$this->tr('Opravdu zrušit změny?').'\')');
      }
      foreach ($this->cssClasses['cancelClass'] as $class) {
         $this->html()->addClass($class);
      }
      
      $ctrlCancel = clone parent::control($renderKey);
      $this->renderedId--;
      $ctrlCancel->setContent($this->formElementLabel[2]);
      $ctrlCancel->setAttrib('name', $this->getName().'_cancel');
      $ctrlCancel->setAttrib('id', $this->getName().'_cancel_'.$rKey);
      if($renderKey == null){
         $this->renderedId++;
      }
      return (string)$ctrlSave.(string)$ctrlSaveStay.(string)$ctrlCancel;
   }

   /**
    * Metoda vrací label
    * @return string
    */
   public function label($renderKey = null, $after = false) {
      return null;
   }

   public function  __toString() {
      return (string)$this->control();
   }
}
