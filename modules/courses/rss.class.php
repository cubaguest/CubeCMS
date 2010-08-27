<?php
class Courses_Rss extends Rss {
   public function  runController() {
      $model = new Courses_Model_Courses();
      $courses = $model->getCoursesForFeed(VVE_FEED_NUM);

      $lecturers = array();
      foreach ($courses as $course) {
         $lecturers[$course->{Courses_Model_Courses::COLUMN_ID}] = $model->getLecturers($course->{Courses_Model_Courses::COLUMN_ID});
      }

      foreach ($courses as $course) {
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
         foreach ($lecturers[$course->{Courses_Model_Courses::COLUMN_ID}] as $lecturer) {
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

         $this->getRssComp()->addItem($course->{Courses_Model_Courses::COLUMN_NAME}, $desc,
                 $this->link()->route('detailCourse', array('urlkey' => $course->{Courses_Model_Courses::COLUMN_URLKEY})),
                 new DateTime($course->{Courses_Model_Courses::COLUMN_TIME_ADD}),
                 $course->{Model_Users::COLUMN_USERNAME}, null, null,
                 $course->{Courses_Model_Courses::COLUMN_URLKEY}."_".$course->{Courses_Model_Courses::COLUMN_ID});

      }
   }
}
?>
