<?php
/**
 * Třída pro obsluhu INPUT prvku typu CHECKBOX
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu CHECKBOX. Umožňuje kontrolu
 * správnosti zadání,kódování/dekódování obsahu prvku, jazykovou obsluhu a jeho
 * vykreslení i s popisem v šabloně. Při špatném zadání se stará o zvýraznění
 * chyby.
 *
 *
 * @copyright     Copyright (c) 2008 Jakub Matas
 * @version       $Id: $ VVE 6.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_Multi_Checkbox extends Form_Element_Checkbox {
   protected $options = array();

   protected function init() {
      parent::init();
      $this->setMultiple(true);
   }

   public function setOptions($options, $merge = false)
   {
      $this->options = $merge ? array_merge($this->options, $options) : $options;
   }

   /**
    * Metoda naplní element
    */
   public function populate() {
      parent::populate();
      foreach($this->options as $key => $val) {
         if(!isset($this->values[$key])){
            $this->values[$key] = false;
         }
      }
      $this->unfilteredValues = $this->values;
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function control($renderKey = null) {
      $rKey = $renderKey != null ? $renderKey : $this->renderedId;
      $values = $this->getUnfilteredValues();
      $this->html()->addClass($this->getName()."_class");

      // rozdělení na dva sloupce
      $opts = count($this->options);
      $rows = ceil($opts/2);

      $elements = array();
      foreach($this->options as $optKey => $optName){
         $this->html()->setAttrib('name', $this->getName()."[".$optKey."]");
         $this->html()->setAttrib('id', $this->getName().'_'.$rKey."_".$optKey);

         if($values === true || (isset ($values[$optKey]) && $values[$optKey] === true)) {
            $this->html()->setAttrib('checked', 'checked');
         } else {
            $this->html()->removeAttrib('checked');
         }

         $this->html()->setAttrib('type', 'checkbox');
//         if(!is_array($values) AND !empty ($values)) {
//            $this->html()->setAttrib('value', $values);
//         }

         $l = new Html_Element('label', $optName);
         $l->setAttrib('for', $this->getName().'_'.$rKey."_".$optKey);

         if($renderKey == null){
            $this->renderedId++;
         }
         $elements[] = $this->html().$l;
      }
      $parts = array_chunk($elements, $rows, true);
      return '<table><tr>
         <td>'.implode('<br />', $parts[0]).'</td>
         <td>'.implode('<br />', $parts[1]).'</td>
         </tr></table>';
   }
}
?>
