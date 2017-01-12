<?php

/**
 * Třída dekorátoru formuláře pro vertikální zobrazení
 *
 * @copyright  	Copyright (c) 2014 Jakub Matas
 * @version    	$Id: $ VVE 8.0.3 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída dekorátoru pro formulář
 */
class Form_Decorator_Vertical extends Form_Decorator_Horizontal implements Form_Decorator_Interface {

   /**
    * Renderuje ovládací prvek
    * @param Html_Element $param
    */
   public function createForm()
   {
      $html = clone $this->form->html();
      $html
          ->addClass('form-vertical')
          ->setAttrib('role', 'form');
      // hlášky
      $html->addContent($this->createMsgBox());
      // kontrolní prvky
      $pHtml = new Html_Element('div', $this->form->elementCheckForm->control());
      $pHtml->addContent($this->form->elementFormID->control());
      if($this->form->protectForm && $this->form->elementToken instanceof Form_Element_Token){
         $pHtml->addContent((string)$this->form->elementToken->controll());
      }
      $pHtml->addClass('inline');
      $html->addContent($pHtml);
      return $html;
   }
   
   public function createRow($name, $formElements)
   {
      $row = parent::createRow($name, $formElements);
      if($formElements[$name] instanceof Form_Element_Checkbox){
         $row->addClass('form-group-inline');
      }
      return $row;
   }
}
