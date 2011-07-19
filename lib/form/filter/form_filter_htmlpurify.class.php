<?php
/**
 * Třída přefiltruje element a odstraní nepovoléné html elementy a atributy
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id: $ VVE7.3.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída fitru pro ošetření html vstupu
 */
class Form_Filter_HTMLPurify extends Form_Filter {
   private $config = array('HTML.Allowed' => 'p[style],span[style],a[href|title],strong,em,img[src|alt],br');

   public function  __construct($allowedTags = 'p[style],span[style],a[href|title],strong,em,img[src|alt],br', $config = array())
   {
      $this->config['HTML.Allowed'] = $allowedTags;
      $this->config = array_merge($this->config, $config);
   }
   
   /**
    * Metoda pro nastavení konfigurace purifieru
    * @param string $key -- název hodnoty
    * @param mixed $value -- hodnota
    * @return Form_Filter_HTMLPurify 
    */
   public function setConfig($key, $value = null)
   {
      if($value != null){
         $this->config[$key] = $value;
      } else {
         unset($this->config[$key]);
      }
      return $this;
   }

   /**
    * Metoda aplikuje filtr na daný element
    * @param Form_Element $elem
    */
   public function filter(Form_Element &$elem, &$values)
   {
      $comPurify = new Component_HTMLPurifier();
      foreach ($this->config as $key => $value) {
         $comPurify->setConfig($key, $value);
      }

      switch (get_class($elem)) {
         case "Form_Element_Text":
         case "Form_Element_TextArea":
         case "Form_Element_Password":
         case "Form_Element_Hidden":
            if($elem->isDimensional() OR $elem->isMultiLang()){
               $values = $comPurify->purifyArray($values);
            } else {
               $values = $comPurify->purify($values);
            }
            break;
         default:
            break;
      }
   }
}
?>
