<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Services_View extends View {
   public function mainView() {
      $this->template()->addTplFile("dirs.phtml");
      Template_Module::setEdit(true);
   }

   public function databaseView() {
      $this->template()->addTplFile("database.phtml");
      Template_Module::setEdit(true);
   }

   public function backupView() {
      $this->template()->addTplFile("backup.phtml");
      Template_Module::setEdit(true);
   }

   public function tablesListView(){
      echo json_encode($this->respond);
   }
}

?>