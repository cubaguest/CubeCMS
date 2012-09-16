<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Services_View extends View {
   
   public function init() 
   {
      Template_Module::setEdit(true);
   } 
   
   public function mainView() {
      $this->template()->addTplFile("dirs.phtml");
   }

   public function databaseView() {
      $this->template()->addTplFile("database.phtml");
      Template_Navigation::addItem($this->tr('Databáze stránek'), $this->link(), null, null, null, true);
   }

   public function backupView() {
      $this->template()->addTplFile("backup.phtml");
      Template_Navigation::addItem($this->tr('Zálohy stránek'), $this->link(), null, null, null, true);
   }
   
   public function tablesListView(){
      echo json_encode($this->respond);
   }
   
   public function dbadminView()
   {
      $this->template()->addFile('tpl://dbadmin.phtml');
   }
}

?>