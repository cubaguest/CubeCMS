<?php
/**
 * Třída pro obsluhu skupiny elemntů tlačítek a vybrání akce podle zadaného tlačítka
 *
 * @copyright  	Copyright (c) 2012 Jakub Matas
 * @version    	$Id: $ VVE 7.16 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu skupiny elementů submit
 */
class Form_Element_Multi_Submit extends Form_Element_Multi {
   
//    protected $confirmMessages = array();
   
   protected function init() {
      $this->htmlElement = new Html_Element('input');
      $this->html()->setAttrib('type', 'submit');
   }

   public function populate() {
      foreach ($this->elements as $element) {
         if(isset ($_REQUEST[$element->getName()])) {
            $this->values = $element->getName();
         }
      }
      $this->unfilteredValues = $this->values;
      $this->isPopulated = true;
   }
   
   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function control($renderKey = null) {
      $str = null;
      foreach ($this->elements as $element){
         $str .= $element->control($renderKey);
      }
      if($renderKey == null){
         $this->renderedId++;
      }
      return (string)$str;
   }

   /**
    * Metoda vrací label
    * @return string
    */
   public function label($renderKey = null, $after = false) {
      return null;
   }
}
?>
