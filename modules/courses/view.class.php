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
   
   public function exportFeedView() {
      $feed = new Component_Feed(true);

      $feed->setConfig('type', $this->type);
      $feed->setConfig('css', 'rss.css');
      $feed->setConfig('title', $this->category()->getName());
      $feed->setConfig('desc', $this->category()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION});
      $feed->setConfig('link', $this->link());
      foreach ($this->courses as $course) {
         $desc = null;
         // datum
         $desc .= "<strong>Konání kurzu:</strong> ".vve_date("%x", new DateTime($course->{Courses_Model_Courses::COLUMN_DATE_START}));
         if($course->{Courses_Model_Courses::COLUMN_DATE_STOP} != null){
            $desc .= ' - '.vve_date("%x", new DateTime($course->{Courses_Model_Courses::COLUMN_DATE_STOP}));
         }
         $desc .= '<br />';

         // cena
         if($course->{Courses_Model_Courses::COLUMN_PRICE} != null|0){
            $desc .= "<strong>Cena:</strong> ".$course->{Courses_Model_Courses::COLUMN_PRICE}." Kč<br /> ";
         }

         // místo
         $desc .= "<strong>Místo konání:</strong> ".$course->{Courses_Model_Courses::COLUMN_PLACE}."<br /> ";

         // lektoři
         $desc .= "<strong>Lektoři:</strong><br />";
         foreach ($this->lecturers[$course->{Courses_Model_Courses::COLUMN_ID}] as $lecturer) {
            $degree = $lecturer->{Lecturers_Model::COLUMN_DEGREE};
            if($degree != null){
               $desc .=  $degree;
               if($degree[strlen($degree)-1] != '.'){
                  $desc .=  '.';
               }
               $desc .=  ' ';
            }
            $desc .= $lecturer->{Lecturers_Model::COLUMN_SURNAME}." ".$lecturer->{Lecturers_Model::COLUMN_NAME}."<br />";
         }

         $desc .= $course->{Courses_Model_Courses::COLUMN_TEXT_SHORT};

         $feed->addItem($course->{Courses_Model_Courses::COLUMN_NAME}, $desc,
                 $this->link()->route('detailCourse', array('urlkey' => $course->{Courses_Model_Courses::COLUMN_URLKEY})),
                 new DateTime($course->{Courses_Model_Courses::COLUMN_TIME_ADD}),
                 $course->{Model_Users::COLUMN_USERNAME}, null, null,
                 $course->{Courses_Model_Courses::COLUMN_URLKEY}."_".$course->{Courses_Model_Courses::COLUMN_ID});
         
      }
      $feed->flush();
   }

}

?>
