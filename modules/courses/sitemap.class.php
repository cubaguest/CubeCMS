<?php
class Courses_SiteMap extends SiteMap {
	public function run() {
      $coursesM = new Courses_Model_Courses();
      // kategorie
      $this->addCategoryItem($coursesM->getLastChange());
      // články
      if($this->isFull()){
         $courses = $coursesM->getCourses();
      } else {
         $courses = $coursesM->getCourses(0,self::SHORT_NUM_RECORD_PER_CAT);
      }

      foreach ($courses as $course) {
         $this->addItem($this->link()->route('detail', array(
             'urlkey' => $course->{Courses_Model_Courses::COLUMN_URLKEY})),
            $course->{Courses_Model_Courses::COLUMN_NAME},
            new DateTime($course->{Courses_Model_Courses::COLUMN_TIME_EDIT}));
      }
      $this->addArchiveLink();
	}

   public function addArchiveLink() {
      if(!$this->isFull()){
         $this->addItem($this->link()->route('listAllCourses'),_('další...'));
      }
   }
}
?>