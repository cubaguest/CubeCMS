<?php
class Projects_View extends View {
   public function mainView() 
   {
      $this->template()->addFile('tpl://sections.phtml');
      $this->createSectionsToolbox();
   }

   public function projectView() 
   {
      $this->template()->addFile('tpl://project.phtml');
      $this->createProjectToolbox();
      $pView = new Photogalery_View($this->pCtrl);
      $pView->addImagesToolbox();
   }
   
   public function sectionView() 
   {
      $this->template()->addFile('tpl://section.phtml');
      $this->createSectionToolbox($this->template()->section);
   }

   public function addSectionView()
   {
      $this->template()->addFile('tpl://edit_section.phtml');
      $this->setTinyMCE($this->form->text, 
         $this->category()->getParam(Photogalery_Controller::PARAM_EDITOR_TYPE, 'advanced'));
   }
   
   public function editSectionView()
   {
      $this->addSectionView();
   }

   public function addProjectView()
   {
      $this->template()->addFile('tpl://edit_project.phtml');
      $this->setTinyMCE($this->form->text, 
         $this->category()->getParam(Photogalery_Controller::PARAM_EDITOR_TYPE, 'advanced'));
   }
   
   public function editProjectView()
   {
      $this->addProjectView();
   }
   
   public function editTextView() {
      $this->setTinyMCE($this->form->text, 'advanced');
      $this->template()->addFile('tpl://projects:edittext.phtml');
   }

   /**
    * Vytvoření toolboxů v detailu
    */
   protected function createDetailToolbox() 
   {
      if($this->category()->getRights()->isControll() OR
              ($this->category()->getRights()->isWritable() AND
                      $this->article->{Articles_Model::COLUMN_ID_USER} == Auth::getUserId())) {
         if(($this->toolbox instanceof Template_Toolbox2) == false){
            $this->toolbox = new Template_Toolbox2();
         }

         $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_article', $this->tr("Upravit položku"), $this->link()->route('edit'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit položku'));
         $this->toolbox->addTool($toolEdit);

         if($this->formPublic instanceof Form){
            $tooldel = new Template_Toolbox2_Tool_Form($this->formPublic);
            $tooldel->setIcon('page_preview.png')->setTitle($this->tr('Zveřejnit položku'));
            $this->toolbox->addTool($tooldel);
         }

         $tooldel = new Template_Toolbox2_Tool_Form($this->formDelete);
         $tooldel->setIcon('page_delete.png')->setTitle($this->tr('Smazat položku'))
            ->setConfirmMeassage($this->tr('Opravdu smazat položku?'));
         $this->toolbox->addTool($tooldel);
         
         if($this->article != false){
            $toolLangLoader = new Template_Toolbox2_Tool_LangLoader($this->article->{Articles_Model::COLUMN_TEXT});
            $this->toolbox->addTool($toolLangLoader);
         }

         if($this->category()->getParam(Articles_Controller::PARAM_PRIVATE_ZONE, false) == true){
            $toolboxP = new Template_Toolbox2();
            $toolboxP->setIcon(Template_Toolbox2::ICON_PEN);
            $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_articlepr', $this->tr("Upravit privátní text"),
            $this->link()->route('editPrivate'));
            $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit privátní text'));
            $toolboxP->addTool($toolEdit);
            $this->toolboxPrivate = $toolboxP;
         }
         
         if(isset ($_GET['l']) AND isset ($this->article[Articles_Model::COLUMN_TEXT][$_GET['l']])){
            $l = $_GET['l'];
            $this->article->{Articles_Model::COLUMN_TEXT} = $this->article[Articles_Model::COLUMN_TEXT][$l];
            $this->article->{Articles_Model::COLUMN_NAME} = $this->article[Articles_Model::COLUMN_NAME][$l];
         }
         $this->article->{Articles_Model::COLUMN_TEXT} = $this->template()->filter((string)$this->article->{Articles_Model::COLUMN_TEXT}, array('anchors'));
      }
   }

   protected function createSectionsToolbox()
   {
      if ($this->rights()->isControll()) {
         $this->toolbox = new Template_Toolbox2();
         $this->toolbox->setIcon(Template_Toolbox2::ICON_ADD);
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_section', $this->tr("Přidat sekci"),
               $this->link()->route('addSection'));
         $toolAdd->setIcon('page_add.png')->setTitle($this->tr('Přidat novou sekci projektů'));
         $this->toolbox->addTool($toolAdd);
      }
      if ($this->rights()->isWritable()) {
         foreach ($this->template()->sections as $key => $sec) {
            $this->template()->sections[$key]->toolbox = clone $this->createSectionToolbox($sec->data);
         }
      }
   }
   
   protected function createProjectToolbox()
   {
      if ($this->rights()->isControll() OR 
         Auth::getUserId() == $this->project->{Projects_Model_Projects::COLUMN_ID_USER}) {
         $this->toolbox = new Template_Toolbox2();
         $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         // edit
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_project', $this->tr("Upravit projekt"),
               $this->link()->route('editProject'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit text projektu'));
         $this->toolbox->addTool($toolEdit);
         // delete
         if(isset ($this->formDelete)){
            $toolDel = new Template_Toolbox2_Tool_Form($this->formDelete);
            $toolDel->setIcon('page_delete.png')->setTitle($this->tr('Smazat projekt'))
               ->setConfirmMeassage($this->tr('Opravdu smazat tento projekt?'));
            $this->toolbox->addTool($toolDel);
         }
      }
   }

   protected function createSectionToolbox($section)
   {
      $toolbox = new Template_Toolbox2();
      $toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
      // add project
      $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_project', $this->tr("Přidat projekt"),
            $this->link()->route('addProject', array('seckey' => $section->{Projects_Model_Sections::COLUMN_URLKEY})));
      $toolAdd->setIcon('page_add.png')->setTitle($this->tr('Přidat nový projekt'));
      $toolbox->addTool($toolAdd);

      if ($this->rights()->isControll()) {
         // edit
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_section', $this->tr("Upravit sekci"),
               $this->link()->route('editSection', array('seckey' => $section->{Projects_Model_Sections::COLUMN_URLKEY})));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit text sekce'));
         $toolbox->addTool($toolEdit);
         // delete
         if(isset ($this->formDelete)){
            $toolDel = new Template_Toolbox2_Tool_Form($this->formDelete);
            $toolDel->setIcon('page_delete.png')->setTitle($this->tr('Smazat sekci'))
               ->setConfirmMeassage($this->tr('Opravdu smazat sekci i s projekty?'));
            $toolDel->getForm()->id->setValues($section->{Projects_Model_Sections::COLUMN_ID});
            $toolbox->addTool($toolDel);
         }
      }
      return $toolbox;
   }
}

?>
