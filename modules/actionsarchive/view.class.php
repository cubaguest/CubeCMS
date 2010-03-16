<?php
class ActionsArchive_View extends View {
   public function init() {
   }


   public function mainView() {
      $this->template()->addTplFile("list.phtml");
   }

   public function editLabelView() {
      $this->template()->addTplFile('editlabel.phtml', 'actions');
   }
}

?>