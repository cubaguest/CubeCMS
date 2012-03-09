<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class ShareDocs_View extends View {
   public function mainView() 
   {
      $this->template()->addFile('tpl://dirs.phtml');
   }

   public function dirListView()
   {
      $this->template()->addFile('tpl://files.phtml');
   }
   
   public function editFileView()
   {
      if($this->category()->getRights()->isControll()) {
         $this->controll = true;
      }
      $this->template()->addFile('tpl://file-edit.phtml');
   }
   
   public function fileView()
   {
      if($this->category()->getRights()->isControll()) {
         $this->controll = true;
      }
      $this->template()->addFile('tpl://file.phtml');
   }
   
   public function editDirAccessView()
   {
      $this->template()->addFile('tpl://dir-access.phtml');
   }
   
   public function usersListView(){
      echo json_encode($this->respond);
   }

   public function groupsListView(){
      echo json_encode($this->respond);
   }
   
   
   
   
   
   
   
   
   public function itemsListView()
   {
      $this->template()->addFile('tpl://files.phtml');

   }

   public function uploadFileView()
   {
   }     
}
?>