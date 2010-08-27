<?php
class Courses_SiteMap extends SiteMap {
	public function run() {
      $coursesM = new Courses_Model_Courses();
      // kategorie
      $this->setCategoryLink($coursesM->getLastChange());
      // články
      $courses = $coursesM->getCourses(0, $this->getMaxItems());

      foreach ($courses as $course) {
         $this->addItem($this->link()->route('detailCourse', array(
             'urlkey' => $course->{Courses_Model_Courses::COLUMN_URLKEY})),
            $course->{Courses_Model_Courses::COLUMN_NAME},
            new DateTime($course->{Courses_Model_Courses::COLUMN_TIME_EDIT}));
      }
      $this->setLinkMore($this->link()->route('listAllCourses'), _('všechny'));
	}
}
?>