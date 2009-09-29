<?php
/**
 * Iterface k validátoru formuláře
 */
interface Form_Validator_Interface {
   /**
    * Metoda provede validaci prvku
    * @param Form_Element $elemObj -- element
    */
   public function validate(Form_Element $elemObj);

   /**
    * Metoda přidá do elementu prvky z validace
    * @param Form_Element $element -- samotný element
    */
   public function addHtmlElementParams(Form_Element $element);

}
?>
