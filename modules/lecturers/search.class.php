<?php
class Lecturers_Search extends Search {
   public function runSearch() {
      $model = new Lecturers_Model();
      $result = $model->search($this->getSearchString());
      if($result != false){
         foreach ($result as $res) {
            $name = null;
            if($res->{Lecturers_Model::COLUMN_DEGREE} != null) $name .= $res->{Lecturers_Model::COLUMN_DEGREE}.' ';
            $name .= $res->{Lecturers_Model::COLUMN_NAME}.' '.$res->{Lecturers_Model::COLUMN_SURNAME};
            if($res->{Lecturers_Model::COLUMN_DEGREE_AFTER} != null) $name .= ' '.$res->{Lecturers_Model::COLUMN_DEGREE_AFTER};

            $this->addResult(
               $name,
//                $this->link()->route('detail', array('urlkey' => $res->{Courses_Model_Courses::COLUMN_URLKEY})),
               $this->link()->param('sid', $res->{Lecturers_Model::COLUMN_ID}),
               $res->{Lecturers_Model::COLUMN_TEXT}, $res->{Search::COLUMN_RELEVATION});
         }
      }
   }
}
?>