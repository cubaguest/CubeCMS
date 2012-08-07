<?php
class CronTab_View extends View {
   public function mainView() {
      Template_Module::setEdit(true);
      $this->template()->addTplFile("list.phtml");
   }

   public function tasksListView(){
      echo json_encode($this->respond);
   }

}

?>
