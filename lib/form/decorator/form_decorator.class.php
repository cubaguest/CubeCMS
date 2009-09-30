<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Třída pro tvorbu dekorátoru formuláře
 *
 * @author cuba
 */
class Form_Decorator {
   private $decoration = array('wrap' => 'table',
      'wrapclass' => 'formTable',
      'wrapgroupclass' => 'formTableGroup',
   'rowwrap' => 'tr',
   'labelwrap' => 'th',
   'labelwrapclass' => 'formLabels',
   'ctrlwrap' => 'td',
   'ctrlwrapclass' => 'formControlls',
   'newline' => false,
   'labelcontent' => array('label'),
   'ctrlcontent' => array('labelLangs', 'controll', 'labelValidations'),
   'labelwrapwidth' => 100,
   'ctrlwrapwidth' => 400);

   private $content = null;

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
   public function  __construct($decoration = null) {
      if($decoration != null) {
         $this->decoration = array_merge($this->decoration, $decoration);
      }
   }

   public function addElement(Form_Element $element) {
      $row = new Html_Element($this->decoration['rowwrap']);
      $cellLabel = new Html_Element($this->decoration['labelwrap']);
      $cellLabel->addClass($this->decoration['labelwrapclass']);
//      $cellLabel->setAttrib('width', $this->decoration['labelwrapwidth']);
      foreach ($this->decoration['labelcontent'] as $type) {
         $cellLabel->addContent($element->{$type}());
      }
      $row->addContent($cellLabel);

      $cellCtrl = new Html_Element($this->decoration['ctrlwrap']);
      foreach ($this->decoration['ctrlcontent'] as $type) {
         $cellCtrl->addContent($element->{$type}());
      }
      // skripty pro práci s prvky
      $cellCtrl->addContent($element->scripts());
      $cellCtrl->addClass($this->decoration['ctrlwrapclass']);
//      $cellCtrl->setAttrib('width', $this->decoration['ctrlwrapwidth']);
      $row->addContent($cellCtrl);

      $this->content .= $row;

   }

   /**
    * Metoda vygeneruje řádek pro formulář
    * @return Html_Element -- objekt Html_elementu
    */
   public function render($createGroupClass = false) {
      if($this->content != null) {
         $dec = new Html_Element($this->decoration['wrap'], $this->content);
         if($createGroupClass){
            $dec->addClass($this->decoration['wrapgroupclass']);
         } else {
            $dec->addClass($this->decoration['wrapclass']);
         }
         return $dec;
      } else {
         return null;
      }
   }
}
?>
