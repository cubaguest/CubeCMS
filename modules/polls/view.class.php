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

   public function voteView() {
      $data = $this->sendData;
      if($this->poll != false) {
         $this->template()->addTplFile('poll_read.phtml');
         $data['data'] = (string)$this->template();
      }
      print (json_encode($data));
   }
}

?>