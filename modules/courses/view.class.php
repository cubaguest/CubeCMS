<?php
class Courses_View extends View {

   public function mainView() {
      $this->template()->addTplFile("list.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('course_add', $this->_('Přidat kurs'),
                 $this->link()->route('addCourse'));
         $toolAdd->setIcon('page_add.png')->setTitle($this->_("Přidat nového kurzu"));
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
      $this->template()->addTplFile("contentlist.phtml");
      echo $this->template();
   }

   public function showCourseView() {
      $this->template()->addTplFile("detail.phtml");
      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('course_reg_list', $this->_('Registrace ke kurzu'),
                 $this->link()->route('registrationsCourse'));
         $toolEdit->setIcon('page_gear.png')->setTitle($this->_("Zobrazit registrace tohoto kurzu"));
         $toolbox->addTool($toolEdit);
         
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('course_edit', $this->_('Upravit kurz'),
                 $this->link()->route('editCourse'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->_("Úprava toho kurzu"));
         $toolbox->addTool($toolEdit);

         $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
         $toolDelete->setIcon('page_delete.png');
         $toolDelete->setConfirmMeassage($this->_('Opravdu smazat kurz?'));
         $toolbox->addTool($toolDelete);

         $this->template()->toolbox = $toolbox;
      }
      $this->courseImage = null;
      if ($this->course->{Courses_Model_Courses::COLUMN_IMAGE} != null){
         $this->courseImage = $this->category()->getModule()->getDataDir(true).$this->course->{Courses_Model_Courses::COLUMN_IMAGE};
      }
   }

   public function archiveView() {
      $this->template()->addTplFile("archive.phtml");
   }

   /**
    * Viewer pro přidání článku
    */
   public function addCourseView() {
      $this->template()->addTplFile("edit.phtml");
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editCourseView() {
      $this->addCourseView();
      if ($this->courseImage != null){
         $this->courseImage = $this->category()->getModule()->getDataDir(true).$this->courseImage;
      }
      $this->edit = true;
   }

   public function placesListView() {
      $plac = array();
      foreach ($this->places as $place)
              array_push ($plac, $place->{Courses_Model_Places::COLUMN_NAME});
      echo json_encode($plac);
   }

   public function registrationsCourseView() {
      $this->template()->addTplFile("list_registrations.phtml");
   }
}

?>
