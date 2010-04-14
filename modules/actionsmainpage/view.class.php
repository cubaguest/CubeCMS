<?php
class ActionsMainPage_View extends View {
   public function init() {
   }

   public function mainView() {
      $this->template()->addTplFile("list.phtml");
   }
}

?>