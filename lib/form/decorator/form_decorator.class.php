<?php

/**
 * Třída dekorátoru formuláře (tato dřída upsahuje implementaci dekorátoru pomocí
 * tabulek. Jejím děděním lze dekorátor upravit)
 *
 * @copyright  	Copyright (c) 2011 Jakub Matas
 * @version    	$Id: $ VVE 7.1 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída dekorátoru pro formulář
 */
class Form_Decorator implements Form_Decorator_Interface {

   private $decoration = array('wrap' => 'table',
      'wrapclass' => 'form-table',
      'wrapgroupclass' => 'form-table form-table-group',
      'grouplabelclass' => 'form-group-text',
      'rowwrap' => 'tr',
      'labelwrap' => 'th',
      'labelwrapclass' => 'form-labels',
      'sublabelclass' => 'form-sub-label',
      'ctrlwrap' => 'td',
      'ctrlwrapclass' => 'form-controlls',
      'newline' => false,
      'labelcontent' => array('label'),
      'ctrlcontent' => array('labelLangs', 'controll', 'labelValidations', 'subLabel'), // název metod pro render
      'labelwrapwidth' => 100,
      'ctrlwrapwidth' => 400,
      'hiddenClass' => 'hidden');
   private $content = null;
   private $groupText = null;
   private $groupName = null;

   /**
    * Konstruktor vytvoří obal
    * @param array $decoration -- pole s nastavením pro dekorátor prvky
    */
   public function __construct($decoration = array())
   {
   }

   /**
    * 
    * @param Form $form
    * @return string
    */
   public function render(Form $form)
   {
      $html = $this->createForm($form);
      foreach ($form->elementsGroups as $name => $group) {
         if(is_array($group)){
            $html->addContent($this->createGroup($name, $group, $form->elements));
         } else {
            $html->addContent($this->createRow($name, $form->elements));
         }
      }
      return (string)$html;
   }
   
   /**
    * Renderuje celou skupinu elementů
    * @param type $param
    */
   public function createGroup($name, $params, $formElements)
   {
      if(empty($params['elements'])){
         return null;
      }
      
      $grp = new Html_Element('fieldset');
      $name = new Html_Element('span', $params['label']);
      if(mb_strlen($params['text']) <= 80){
         $text = new Html_Element('span', new Html_Element('small', $params['text']));
         $grp->addContent(new Html_Element('legend', $name->addClass('legend-name') . $text->addClass('legend-text')));
      } else {
         $grp->addContent(new Html_Element('legend', $name->addClass('legend-name')));
         $text = new Html_Element('div', $params['text']);
         $grp->addContent($text->addClass('form-legend-text'));
      }
      // lementy
      foreach ($params['elements'] as $name => $realname) {
         $grp->addContent($this->createRow($name, $formElements));
      }
      
      return $grp;
   }
   
   /**
    * Renderuje řádek elementu
    * @param type $param
    */
   public function createRow($name, $formElements)
   {
      $row = new Html_Element('div');
      $row->addClass('form-group');
      if( $formElements[$name]->isPopulated() && !$formElements[$name]->isValid() ){
         $row->addClass('has-error');
      }
      $row->addContent( $this->createLabel($formElements[$name]) );
      $row->addContent( $this->createControl($formElements[$name]) );
      
      return $row;
   }
   
   /**
    * Renderuje popisek k prvku
    * @param type $param
    */
   public function createLabel($element)
   {
      $cell = new Html_Element('div');
      $cell->addClass('col-md-3')->addClass('form-labels');
      if(
          !$element instanceof Form_Element_Button 
          && !$element instanceof Form_Element_Submit 
          && !$element instanceof Form_Element_SaveCancel 
          ){
         $cell->addContent($element->label());
      }
      return $cell;
   }
   
   /**
    * Renderuje ovládací prvek
    * @param type $param
    */
   public function createControl($element)
   {
      $cell = new Html_Element('div');
      $cell->addClass('col-md-9')->addClass('form-controls');
      
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
      } else {
         $this->addControlClass($element);
         $cell->addContent($element->control());
      }
      
      
      // subpopisky a validace
      if($element->labelValidations() != null){
         $validations = new Html_Element('div', $element->labelValidations());
         $validations->addClass('help-block');
         $cell->addContent($validations);
      }
      if($element->getSubLabel() != null){
         $sublabel = new Html_Element('div', $element->subLabel());
         $sublabel->addClass('help-block');
         $cell->addContent($sublabel);
      }
      
      return $cell;
   }
   
   /**
    * Přidá potřebné třídy do elementu
    */
   protected function addControlClass(Form_Element $element)
   {
      if($element instanceof Form_Element_Button 
          || $element instanceof Form_Element_Submit
          ){
         $element->html()->addClass('btn')->addClass('btn-default');
      } 
      // textová pole
      else if(
          $element instanceof Form_Element_Text 
          || $element instanceof Form_Element_Password 
          || $element instanceof Form_Element_File 
          || $element instanceof Form_Element_TextArea 
          || ($element instanceof Form_Element_Select && !$element instanceof Form_Element_Radio) ){
         $element->html()->addClass('form-control');
      } 
      else if($element instanceof Form_Element_SaveCancel){
         $element->cssClasses['confirmClass'] = array_merge($element->cssClasses['confirmClass'], array('btn', 'btn-success'));
         $element->cssClasses['cancelClass'] = array_merge($element->cssClasses['cancelClass'], array('btn', 'btn-danger'));
      }
//      else if($element instanceof Form_Element_SaveCancel){
//      }
   }


   /**
    * Renderuje ovládací prvek
    * @param Html_Element $param
    */
   public function createForm(Form $form)
   {
      $html = clone $form->html();
      $html
          ->addClass('form-horizontal')
          ->setAttrib('role', 'form');
      // kontrolní prvky
      $pHtml = new Html_Element('div', $form->elementCheckForm->control());
      if($form->protectForm && $form->elementToken instanceof Form_Element_Token){
         $pHtml->addContent((string)$form->elementToken->controll());
      }
      $pHtml->addClass('inline');
      $html->addContent($pHtml);
      return $html;
   }
   
}
