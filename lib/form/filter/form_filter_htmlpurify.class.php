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
   /**
    * Povolené tagy
    * @var string
    */
   private $allowedTags = 'p[style],span[style],a[href|title],strong,em,img[src|alt],br';

   public function  __construct($allowedTags = 'p[style],span[style],a[href|title],strong,em,img[src|alt],br')
   {
      $this->allowedTags = $allowedTags;
   }

   /**
    * Metoda aplikuje filtr na daný element
    * @param Form_Element $elem
    */
   public function filter(Form_Element &$elem, &$values)
   {
      $comPurify = new Component_HTMLPurifier();
      $comPurify->setConfig('HTML.Allowed', $this->allowedTags);

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
