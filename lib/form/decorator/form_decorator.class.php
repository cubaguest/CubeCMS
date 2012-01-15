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
      if ($decoration != null) {
         $this->decoration = array_merge($this->decoration, $decoration);
      }
   }

   public function addElement(Form_Element $element)
   {
      $row = new Html_Element($this->decoration['rowwrap']);

      if ($element instanceof Form_Element_Hidden) {
         $row->addClass($this->decoration['hiddenClass']);
      }

      $cellLabel = new Html_Element($this->decoration['labelwrap']);
      $cellLabel->addClass($this->decoration['labelwrapclass']);
      //      $cellLabel->setAttrib('width', $this->decoration['labelwrapwidth']);
      foreach ($this->decoration['labelcontent'] as $type) {
         $cellLabel->addContent($element->{$type}());
      }
      $row->addContent($cellLabel);

      $cellCtrl = new Html_Element($this->decoration['ctrlwrap']);
      // sublabel
      $element->htmlSubLabel()->addClass($this->decoration['sublabelclass']);

      foreach ($this->decoration['ctrlcontent'] as $type) {
         $cellCtrl->addContent($element->{$type}());
      }
      // skripty pro práci s prvky
      $js = $element->scripts();
      if($js != null){
         $script = new Html_Element_Script($js);
         // tady může být preproces scriptu
         $cellCtrl->addContent($script);
      }
      $cellCtrl->addClass($this->decoration['ctrlwrapclass']);
      $row->addContent($cellCtrl);
      $this->content .= $row;
   }

   /**
    * Metoda vygeneruje řádek pro formulář
    * @return Html_Element -- objekt Html_elementu
    */
   public function render($createGroupClass = false)
   {
      if ($this->content != null) {
         $dec = new Html_Element($this->decoration['wrap'], $this->content);
         if ($createGroupClass) {
            $dec->addClass($this->decoration['wrapgroupclass']);
         } else {
            $dec->addClass($this->decoration['wrapclass']);
         }
         $field = new Html_Element('fieldset');
         if ($this->groupName != null) {
            $field->addClass('fieldset-alt');
            $name = new Html_Element('span', $this->groupName);
            $addcnt = null;
            if(mb_strlen($this->groupText) <= 80){
               $text = new Html_Element('span', $this->groupText);
               $field->addContent(new Html_Element('legend', $name->addClass('form-legend-name') . $text->addClass('form-legend-text')));
            } else {
               $field->addContent(new Html_Element('legend', $name->addClass('form-legend-name')));
               $text = new Html_Element('p', $this->groupText);
               $field->addContent($text->addClass('form-legend-text'));
            }
         }
         $field->addContent($dec);
         return (string) $field;
      } else {
         return null;
      }
   }

   /**
    * Metoda nastaví název skupiny
    * @param string $name -- tag legend
    * @return Form_Decorator 
    */
   public function setGroupName($name)
   {
      $this->groupName = $name;
      return $this;
   }

   /**
    * Metoda nastaví popisek skupiny
    * @param string $text -- popisek uvnitře fieldsetu
    * @return Form_Decorator
    */
   public function setGroupText($text)
   {
      $this->groupText = $text;
      return $this;
   }

   /**
    * Magická metoda vrátí obsah dekorátoru jako řetězec
    * @return string (nejčastěji fieldset)
    */
   public function __toString()
   {
      return $this->render();
   }

}
?>
