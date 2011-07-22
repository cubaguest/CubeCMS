<?php
class Courses_Search extends Search {
   public function runSearch() {
      $model = new Courses_Model_Courses();
      $result = $model
//       ->where(Articles_Model::COLUMN_ID_CATEGORY.' = :idc', array('idc'=>$this->category()->getId()))
      ->search($this->getSearchString());
      if($result != false){
         foreach ($result as $res) {
            $this->addResult(
               $res->{Courses_Model_Courses::COLUMN_NAME},
               $this->link()->route('detailCourse', array('urlkey' => $res->{Courses_Model_Courses::COLUMN_URLKEY})),
               $res->{Courses_Model_Courses::COLUMN_TEXT}, $res->{Search::COLUMN_RELEVATION});
         }
      }
   }
}
?>