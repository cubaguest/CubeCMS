<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Services_View extends View {
   public function mainView() {
      $this->template()->addTplFile("dirs.phtml");
   }

   public function databaseView() {
      $this->template()->addTplFile("database.phtml");
   }

   public function backupView() {
      $this->template()->addTplFile("backup.phtml");
   }

   public function tablesListView(){
      echo json_encode($this->respond);
   }
}

?>