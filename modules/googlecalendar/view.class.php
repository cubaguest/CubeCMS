<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class GoogleCalendar_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://main.phtml');
      if($this->category()->getRights()->isWritable()){
         $this->toolbox = new Template_Toolbox2();
      }
   }
}

?>