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
   private $content = null;
   
   protected $decoration = array();
   
   /**
    *
    * @var Form
    */
   protected $form = null;
   
   /**
    * Konstruktor vytvoří obal
    * @param array $decoration -- pole s nastavením pro dekorátor
    *
    * <p>
    * prvky<br/>
    * <ul>
    * <li>'wrap' -- obal elementů (table)</li>
    * <li>'rowwrap' -- obal řádku (tr)</li>
    * <li>'labelwrap' -- obal popisku (th)</li>
    * <li>'ctrlwrap' -- obal kontrolního prvku (td)</li>
    * </ul>
    * </p>
    */
   public function __construct($decoration = null)
   {
      if($decoration){
         $this->decoration = array_merge($this->decoration, $decoration);
      }
   }

   /**
    * Metoda vygeneruje řádek pro formulář
    * @return string
    */
   public function render(Form $form)
   {
      $this->form = $form;
      return $this->prepareForm();
   }
   
   protected function prepareForm()
   {
      $html = $this->createForm();
      foreach ($this->form->elementsGroups as $name => $group) {
         if(is_array($group)){
            $html->addContent($this->createGroup($name, $group, $this->form->elements));
         } else {
            $html->addContent($this->createRow($name, $this->form->elements));
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
      return $this->createLabel($formElements[$name]) . $this->createControl($formElements[$name]);
   }
   
   /**
    * Renderuje popisek k prvku
    * @param type $param
    */
   public function createLabel($element)
   {
      $string = null;
      if(
          !$element instanceof Form_Element_Button 
          && !$element instanceof Form_Element_Submit 
          && !$element instanceof Form_Element_SaveCancel 
          ){
         $string .= $element->label();
      }
      return $string;
   }
   
   /**
    * Renderuje ovládací prvek
    * @param type $param
    */
   public function createControl($element)
   {
      $string = null;
      $string .= $element->control();
      $scripts = $element->scripts();
      if($scripts != null){
         $string .= new Html_Element_Script($scripts);
      }
      return $string;
   }
   
   /**
    * Renderuje ovládací prvek
    * @param Html_Element $param
    */
   public function createForm()
   {
      $html = $this->form->html();
      $html->clearContent();
      $html->setAttrib('role', 'form');
      $html->addClass('form-inline');
      $html->addContent($this->createMsgBox());
      // kontrolní prvky
      $html->addContent($this->form->elementCheckForm->control());
      $html->addContent($this->form->elementFormID->control());
      if($this->form->protectForm && $this->form->elementToken instanceof Form_Element_Token){
         $html->addContent($this->form->elementToken->controll());
      }
      return $html;
   }
   
   protected function createMsgBox()
   {
      $errHtml = new Html_Element('div');
      $errHtml->addClass('form-errors');
      $errHtml->setAttrib('style', 'display:none;');
      $infoHtml = new Html_Element('div');
      $infoHtml->addClass('form-success');
      $infoHtml->setAttrib('style', 'display:none;');
      return $infoHtml.$errHtml;
   }
   
}
