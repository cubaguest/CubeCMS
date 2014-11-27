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
      // metadata
      if ((string) $this->project->{Projects_Model_Projects::COLUMN_KEYWORDS} != null) {
         Template_Core::setPageKeywords($this->project->{Projects_Model_Projects::COLUMN_KEYWORDS});
      }
      if ((string) $this->project->{Projects_Model_Projects::COLUMN_DESCRIPTION} != null) {
         Template_Core::setPageDescription($this->project->{Projects_Model_Projects::COLUMN_DESCRIPTION});
      }
      Template_Core::setMetaTag('author', $this->project->{Model_Users::COLUMN_NAME}.' '.$this->project->{Model_Users::COLUMN_SURNAME});
      
      if ($this->project->{Projects_Model_Projects::COLUMN_THUMB} != null) {
         Template_Core::setMetaTag('og:image', $this->dataDir.$this->project->{Projects_Model_Projects::COLUMN_THUMB} );
      } else if($this->project->{Projects_Model_Projects::COLUMN_IMAGE} != null){
         Template_Core::setMetaTag('og:image', $this->dataDir.$this->project->{Projects_Model_Projects::COLUMN_IMAGE} );
      }
      Template_Navigation::addItem($this->project->{Projects_Model_Projects::COLUMN_NAME}, $this->link());
   }
   
   public function sectionView() 
   {
      $this->template()->addFile('tpl://section.phtml');
      $this->toolbox = $this->createSectionToolbox($this->template()->section);
   }

   public function addSectionView($isEdit = false)
   {
      $this->template()->addFile('tpl://projects:edit_section.phtml');
      $this->setTinyMCE($this->form->text, 
         $this->category()->getParam(Photogalery_Controller::PARAM_EDITOR_TYPE, 'advanced'));
      if(!$isEdit){
         Template_Navigation::addItem($this->tr('Přidání sekce'), $this->link());
      }
      Template::setFullWidth(true);
   }
   
   public function editSectionView()
   {
      $this->addSectionView(true);
      Template_Navigation::addItem(sprintf( $this->tr('Úprava sekce %s'), $this->section->{Projects_Model_Sections::COLUMN_NAME}), 
         $this->link()->route());
   }

   public function addProjectView()
   {
      $this->template()->addFile('tpl://projects:edit_project.phtml');
      $this->setTinyMCE($this->form->text, 
         $this->category()->getParam(Photogalery_Controller::PARAM_EDITOR_TYPE, 'advanced'));
      Template::setFullWidth(true);
   }
   
   public function editProjectView()
   {
      $this->addProjectView();
   }
   
   public function editTextView() {
      $this->setTinyMCE($this->form->text, 'advanced');
      $this->template()->addFile('tpl://projects:edittext.phtml');
      Template::setFullWidth(true);
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
         $this->toolbox->addTool($this->getSectionsSortToolbox());
      }
   }
   
   protected function createProjectToolbox()
   {
      if ($this->rights()->isControll() 
//          OR  Auth::getUserId() == $this->project->{Projects_Model_Projects::COLUMN_ID_USER}
          ) {
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
               ->setConfirmMeassage($this->tr('Opravdu smazat tento projekt?'))
               ->setImportant(true);
            $this->toolbox->addTool($toolDel);
         }
      }
   }

   protected function createSectionToolbox($section)
   {
      if ($this->rights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         // add project
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_project', $this->tr("Přidat projekt"),
               $this->link()->route('addProject', array('seckey' => $section->{Projects_Model_Sections::COLUMN_URLKEY})));
         $toolAdd->setIcon('page_add.png')->setTitle($this->tr('Přidat nový projekt'));
         $toolbox->addTool($toolAdd);

         $toolbox->addTool($this->getSortToolbox($section));

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
                  ->setConfirmMeassage($this->tr('Opravdu smazat sekci i s projekty?'))
                  ->setImportant(true);
               $toolDel->getForm()->id->setValues($section->{Projects_Model_Sections::COLUMN_ID});
               $toolbox->addTool($toolDel);
            }
         }
         return $toolbox;
      }
      return null;
   }
   
   protected function getSortToolbox(Model_ORM_Record $section = null)
   {
      $toolSort = new Template_Toolbox2_Tool_PostRedirect('sort_projects', $this->tr("Řadit projekty"),
               $this->link()->route('sortProjects', array('seckey' => $section ? $section->{Projects_Model_Sections::COLUMN_URLKEY} : null )));
      $toolSort->setIcon(Template_Toolbox2::ICON_MOVE_UP_DOWN)->setTitle($this->tr('Řadit projekty'));
      return $toolSort;
   }
   
   protected function getSectionsSortToolbox()
   {
      $toolSort = new Template_Toolbox2_Tool_PostRedirect('sort_sections', $this->tr("Řadit sekce"),
               $this->link()->route('sortSections'));
      $toolSort->setIcon(Template_Toolbox2::ICON_MOVE_UP_DOWN)->setTitle($this->tr('Řadit sekce'));
      return $toolSort;
   }
   
   public function sortProjectsView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://projects:edit_order.phtml');
   }
   
   public function sortSectionsView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://projects:edit_sorder.phtml');
   }
}
