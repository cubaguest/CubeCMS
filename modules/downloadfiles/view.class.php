<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class DownloadFiles_View extends View {
   public function mainView() 
   {
      $this->template()->addFile('tpl://main.phtml');
      if($this->category()->getRights()->isWritable()) {
         $this->controlls = true;
         
         $toolbox = new Template_Toolbox2();
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('dwfile_add', $this->tr('Přidat soubor'), $this->link()->route('add'));
         $toolAdd->setIcon('page_add.png')->setTitle($this->tr("Přidat nový soubor"));
         $toolbox->addTool($toolAdd);
         
         $this->toolbox = $toolbox;

         $toolboxEdit = new Template_Toolbox2();
         $toolboxEdit->setIcon('page_edit.png');

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('dwfile_edit', $this->tr("Upravit soubor"));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit osobu'));
         $toolboxEdit->addTool($toolEdit);

         $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
         $toolDelete->setIcon('page_delete.png');
         $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat soubor?'));
         $toolboxEdit->addTool($toolDelete);
         
         foreach ($this->template()->files as &$file) {
            $toolboxEdit->dwfile_edit->setAction($this->link()->route('edit', array('id' => $file->{DownloadFiles_Model::COLUMN_ID})));
            $this->formDelete->id->setValues($file->{DownloadFiles_Model::COLUMN_ID});
            $toolDelete->setConfirmMeassage(sprintf($this->tr('Opravdu smazat soubor %s ?'), $file->{DownloadFiles_Model::COLUMN_NAME}));
            $file->toolbox = clone $toolboxEdit;
         }
         
         $this->toolboxItem = $toolboxEdit;
      }
   }

   public function addView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://edit.phtml');
   }

   public function editView()
   {
      $this->addView();
   }     
}
?>