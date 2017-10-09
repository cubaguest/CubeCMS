<?php
class Courses_View extends View {

   public function mainView() {
      $this->template()->addFile("tpl://list.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('course_add', $this->tr('Přidat položku'),
                 $this->link()->route('addCourse'));
         $toolAdd->setIcon(Template_Toolbox2::ICON_ADD)->setTitle($this->tr("Přidání nové položky"));
         $toolbox->addTool($toolAdd);
         $this->template()->toolbox = $toolbox;
      }
   }

   public function listAllCoursesView() {
      $this->isAllList = true;
      $this->mainView();
   }

   public function topView() {
      $this->mainView();
   }

   public function contentView() {
      $this->template()->addFile("tpl://contentlist.phtml");
      echo $this->template();
   }

   public function showCourseView() {
      $this->template()->addFile("tpl://detail.phtml");
      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolRegs = new Template_Toolbox2_Tool_PostRedirect('course_reg_list', $this->tr('Registrace k položce'),
                 $this->link()->route('registrationsCourse'));
         $toolRegs->setIcon(Template_Toolbox2::ICON_GROUP)->setTitle($this->tr("Zobrazit registrace tohoto kurzu"));
         $toolbox->addTool($toolRegs);
         
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('course_edit', $this->tr('Upravit položku'),
                 $this->link()->route('editCourse'));
         $toolEdit->setIcon(Template_Toolbox2::ICON_PEN)->setTitle($this->tr("Úprava této položky"));
         $toolbox->addTool($toolEdit);

         $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
         $toolDelete->setIcon(Template_Toolbox2::ICON_DELETE);
         $toolDelete->setImportant(true);
         $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat položku?'));
         $toolbox->addTool($toolDelete);

         $this->template()->toolbox = $toolbox;
      }
      Template_Navigation::addItem($this->course->{Courses_Model::COLUMN_NAME}, $this->link());
   }

   /**
    * Viewer pro přidání článku
    */
   public function addCourseView() {
      $this->template()->addFile("tpl://edit.phtml");
      $this->setTinyMCE($this->form->textShort, 'simple');
      $this->setTinyMCE($this->form->text, 'advanced');
      $this->setTinyMCE($this->form->textPrivate, 'advanced');
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editCourseView() {
      $this->edit = true;
      $this->addCourseView();
      $this->edit = true;
   }


   public function placesListView() {
      $plac = array();
      foreach ($this->places as $place)
              array_push ($plac, $place->{Courses_Model_Places::COLUMN_NAME});
      echo json_encode($plac);
   }

   public function registrationsCourseView() {
      $this->template()->addFile("tpl://list_registrations.phtml");
   }
}
