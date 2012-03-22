<?php
class Journals_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");

      $this->addListToolbox();
   }

   private function addListToolbox()
   {
      if($this->category()->getRights()->isWritable()) {
         // main
         $toolbox = new Template_Toolbox2();
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('journal_add', $this->_('Přidat deník'), $this->link()->route('add'));
         $toolAdd->setIcon('book_add.png')->setTitle($this->_("Přidat nový deník"));
         $toolbox->addTool($toolAdd);

         $this->toolbox = $toolbox;

         // items
         $toolboxEdit = new Template_Toolbox2();
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('journal_edit', $this->_("Upravit deník"), $this->link()->route('edit'));
         $toolEdit->setIcon('book_edit.png')->setTitle($this->_('Upravit deník'));
         $toolboxEdit->addTool($toolEdit);

            
         if($this->formDelete != null){
            $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
            $toolDelete->setIcon('user_delete.png');
            $toolDelete->setConfirmMeassage($this->_('Opravdu smazat deník?'));
            $toolboxEdit->addTool($toolDelete);
         }
         
         $this->toolboxEdit = $toolboxEdit;
      }
   }
   
   public function showView()
   {
      $this->template()->addFile('tpl://detail.phtml');
      $this->template()->setEdit(true);
      
      if($this->category()->getRights()->isWritable()) {
         // main
         $toolbox = new Template_Toolbox2();
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('journal_edit', $this->_('Upravit deník'), $this->link()
            ->route('edit', array('year' => $this->journal->{Journals_Model::COLUMN_YEAR},'number' => $this->journal->{Journals_Model::COLUMN_NUMBER}) ));
         $toolEdit->setIcon('book_edit.png')->setTitle($this->_("Upravit zobrazený deník"));
         $toolbox->addTool($toolEdit);

         $this->toolbox = $toolbox;
            
         if($this->formDelete != null){
            $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
            $toolDelete->setIcon('book_delete.png');
            $toolDelete->setConfirmMeassage($this->_('Opravdu smazat deník?'));
            $this->toolbox->addTool($toolDelete);
         }
      }
   }
   

   public function showLastView()
   {
      $this->showView();
   }
   
   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      $this->template()->addTplFile("edit.phtml");
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->edit = true;
      $this->addView();
   }
}

?>
