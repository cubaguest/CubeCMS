<?php
/**
 * Description of html_elementclass
 *
 * @author cuba
 */
class Html_Element {
/**
 * Název elementu
 * @var string
 */
   private $elementName = null;

   /**
    * Konstruktor vytvoří objeket Html elementu
    * @param string $name -- název elementu
    */
   public function  __construct($name) {
      $this->elementName = $name;
   }
}
?>
