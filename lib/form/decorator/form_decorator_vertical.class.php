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
class Form_Decorator_Vertical extends Form_Decorator implements Form_Decorator_Interface {

   /**
    * Renderuje ovládací prvek
    * @param Html_Element $param
    */
   public function createForm(Form $form)
   {
      $html = clone $form->html();
      $html
          ->addClass('form-vertical')
          ->setAttrib('role', 'form');
      // kontrolní prvky
      $pHtml = new Html_Element('div', $form->elementCheckForm->control());
      $pHtml->addContent($form->elementFormID->control());
      if($form->protectForm && $form->elementToken instanceof Form_Element_Token){
         $pHtml->addContent((string)$form->elementToken->controll());
      }
      $pHtml->addClass('inline');
      $html->addContent($pHtml);
      return $html;
   }
   
}
