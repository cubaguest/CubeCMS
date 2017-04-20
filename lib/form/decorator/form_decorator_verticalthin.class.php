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
class Form_Decorator_VerticalThin extends Form_Decorator_Vertical implements Form_Decorator_Interface {

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
      $row = new Html_Element('div');
      $row->addClass('form-group');
      if($formElements[$name] instanceof Form_Element_Hidden){
         $row->addClass('form-group-hidden');
      }
      if( $formElements[$name]->isPopulated() && !$formElements[$name]->isValid() ){
         $row->addClass('has-error');
      }
      if( $formElements[$name]->isAdvanced()){
         $row->addClass('form-group-advanced');
         $this->hasAdvFields = true;
      }
      $row->addContent( $this->createControl($formElements[$name]) );
      if($formElements[$name] instanceof Form_Element_Checkbox){
         $row->addClass('form-group-inline');
      }
      
      return $row;
   }
   
    /**
    * Renderuje ovládací prvek
    * @param type $param
    */
   public function createControl($element)
   {
      $cell = new Html_Element('div');
      $cell->addClass($this->decoration['controlColClass'])->addClass('form-controls');
      
      if($element->isMultiLang()){
         $cell->addContent($element->labelLangs());
      }
      
      if($element instanceof Form_Element_Multi){
         $wrap = new Html_Element('div');
         $wrap->addClass('inline-elements');
         foreach ($element as $e) {
            $this->addControlClass($e);
            
            $wrapControl = new Html_Element('div');
            if($e instanceof Form_Element_Checkbox){
               $wrapControl->addClass('checkbox');
               $wrapControl->addContent($e->control());
               $wrapControl->addContent($e->label(null, true));
            } else {
               $wrapControl->addClass('group');
               $e->htmlLabel()->addClass('sr-only');
               $wrapControl->addContent($e->label());
               $wrapControl->addContent($e->control());
            }
               
            $wrap->addContent($wrapControl);
         }
         $cell->addContent($wrap);
      } else if($element instanceof Form_Element_Checkbox ) {
         $cellInner = new Html_Element('div');
         $cellInner->addClass('checkbox');
         if(mb_strlen($element->getLabel()) > self::CHECKBOX_LABEL_AFTER_CHARS){
            $cellInner->addContent($element->control());
            $cellInner->addContent($element->label(null, true));
         } else {
            $cellInner->addContent($element->control());
         }
         $cell->addContent($cellInner);
         
      } else if($element instanceof Form_Element_Text) {
         $this->addControlClass($element);
         $element->html()->setAttrib('placeholder', $element->getLabel());
         $cell->addContent($element->control());
      } else {
         $this->addControlClass($element);
         $cell->addContent($element->control());
      }
      
      
      // subpopisky a validace
//      if($element->labelValidations() != null){
//         $validations = new Html_Element('div', $element->labelValidations());
//         $validations->addClass('help-block');
//         $cell->addContent($validations);
//      }
//      if($element->getSubLabel() != null){
//         $sublabel = new Html_Element('div', $element->subLabel());
//         $sublabel->addClass('help-block');
//         $cell->addContent($sublabel);
//      }
      $scripts = $element->scripts();
      if($scripts != null){
         $cell->addContent(new Html_Element_Script($scripts));
      }
      
      return $cell;
   }
   
}
