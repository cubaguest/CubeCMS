<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Pokus_View extends View {
   public function mainView() {
      $this->template()->addTplFile("main.phtml");
   }

   public function messagesView() {
   }

   public function ajaxView() {
      $this->template()->addTplFile("ajaxform.phtml");
   }
}

?>