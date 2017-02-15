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
         $toolAdd->setIcon(Template_Toolbox2::ICON_ADD)->setTitle($this->tr("Přidat nový soubor"));
         $toolbox->addTool($toolAdd);
         
         if($this->template()->files){
            $toolEditGroups = new Template_Toolbox2_Tool_PostRedirect('dwfile_edit-grp', $this->tr('Upravit sekce'), $this->link()->route('editGroups'));
            $toolEditGroups->setIcon(Template_Toolbox2::ICON_PEN)->setTitle($this->tr("Upravit a řadit sekce souborů"));
            $toolbox->addTool($toolEditGroups);
         
            $toolAllFiles = new Template_Toolbox2_Tool_PostRedirect('dwfile_grp-allfiles', $this->tr('Hromadné zařazení'), $this->link()->route('editFilesGroups'));
            $toolAllFiles->setIcon(Template_Toolbox2::ICON_WRENCH)->setTitle($this->tr("Hromadné zařazení souborů do sekcí"));
            $toolbox->addTool($toolAllFiles);
         }
         
         $this->toolbox = $toolbox;

         $toolboxEdit = new Template_Toolbox2();
         $toolboxEdit->setIcon(Template_Toolbox2::ICON_WRENCH);

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('dwfile_edit', $this->tr("Upravit soubor"));
         $toolEdit->setIcon(Template_Toolbox2::ICON_PEN)->setTitle($this->tr('Upravit soubor'));
         $toolboxEdit->addTool($toolEdit);
         
         $toolMove = new Template_Toolbox2_Tool_PostRedirect('dwfile_move', $this->tr("Přesunout do kategorie"));
         $toolMove->setIcon(Template_Toolbox2::ICON_MOVE)->setTitle($this->tr('Přesunout soubor do jiné kategorie'));
         $toolboxEdit->addTool($toolMove);

         $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
         $toolDelete->setIcon(Template_Toolbox2::ICON_DELETE);
         $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat soubor?'));
         $toolboxEdit->addTool($toolDelete);
         
         if($this->template()->files){
            foreach ($this->template()->files as &$file) {
               $toolboxEdit->dwfile_edit->setAction($this->link()->route('edit', array('id' => $file->{DownloadFiles_Model::COLUMN_ID})));
               $toolboxEdit->dwfile_move->setAction($this->link()->route('moveCat', array('id' => $file->{DownloadFiles_Model::COLUMN_ID})));
               $this->formDelete->id->setValues($file->{DownloadFiles_Model::COLUMN_ID});
               $toolDelete->setConfirmMeassage(sprintf($this->tr('Opravdu smazat soubor %s ?'), $file->{DownloadFiles_Model::COLUMN_NAME}));
               $file->toolbox = clone $toolboxEdit;
            }
         }
         
         $this->toolboxItem = $toolboxEdit;
      }
   }

   public function addView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://edit.phtml');
   }
   
   public function editFilesGroupsView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://filesassign.phtml');
   }
   
   public function editGroupsView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://editgroups.phtml');
   }
   
   public function moveCatView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://move.phtml');
   }

   public function editView()
   {
      $this->addView();
   }     
}