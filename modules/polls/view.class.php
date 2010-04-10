<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Polls_View extends View {
   public function mainView() {
      $this->template()->addTplFile('polls.phtml');
      $this->template()->addCssFile('style.css');
   }

   public function addView() {
      $this->template()->addTplFile('edit.phtml');
   }

   public function editView() {
      $this->addView();
   }

   public function pollDataView() {
      if($this->poll != false AND $this->poll != null) {
         $this->template()->addTplFile('poll_read.phtml');
         $this->pollData = (string)$this->template();
      }
   }
}

?>