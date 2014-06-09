<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class LinksList_View extends View {
   public function mainView()
   {
      $this->template()->addFile($this->getTemplate());
      if($this->text != null){
         $this->text = $this->template()->filter((string)$this->text, array('anchors','filesicons'));
      }
      
      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_PEN);

         $toolET = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr('Upravit text'),
                 $this->link()->route('editText'));
         $toolET->setIcon(Template_Toolbox2::ICON_PAGE_EDIT)->setTitle($this->tr("Upravit text"));
         $toolbox->addTool($toolET);
         
         $toolEL = new Template_Toolbox2_Tool_PostRedirect('edit_links', $this->tr('Upravit odkazy'),
                 $this->link()->route('list'));
         $toolEL->setIcon(Template_Toolbox2::ICON_PEN)->setTitle($this->tr("Upravit odkazy"));
         $toolbox->addTool($toolEL);

         $this->toolbox = $toolbox;
      }
   }

   public function editTextView()
   {
      Template_Module::setEdit(true);
      $this->setTinyMCE($this->form->text, 'advanced');
      $this->template()->addFile("tpl://textedit.phtml");
   }
   
   public function listView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile("tpl://list.phtml");
      
      $tlb = new Template_Toolbox2();
      
      $tEdit = new Template_Toolbox2_Tool_Redirect('editLink', $this->tr('Upravit odkaz'), $this->link()->route('edit'));
      $tEdit->setIcon(Template_Toolbox2::ICON_PEN);
      $tlb->addTool($tEdit);
      
      $tDel = new Template_Toolbox2_Tool_Form($this->formDelete);
      $tDel->setConfirmMeassage($this->tr('Opravdu smazat odkaz?'));
      $tDel->setIcon(Template_Toolbox2::ICON_DELETE);
      $tlb->addTool($tDel);
      $this->toolboxLink = $tlb;
   }
   
   public function editView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile("tpl://edit.phtml");
   }
   
   public function addView()
   {
      $this->editView();
   }

}