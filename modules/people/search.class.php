<?php
class People_Search extends Search {
   public function runSearch() {
      $model = new People_Model();
      $result = $model->search($this->getSearchString());
      if($result != false){
         foreach ($result as $res) {
            $name = null;
            if($res->{People_Model::COLUMN_DEGREE} != null) $name .= $res->{People_Model::COLUMN_DEGREE}.' ';
            $name .= $res->{People_Model::COLUMN_NAME}.' '.$res->{People_Model::COLUMN_SURNAME};
            if($res->{People_Model::COLUMN_DEGREE_AFTER} != null) $name .= ' '.$res->{People_Model::COLUMN_DEGREE_AFTER};

            $this->addResult(
               $name,
               $this->link()->param('sid', $res->{People_Model::COLUMN_ID}),
               $res->{People_Model::COLUMN_TEXT}, $res->{Search::COLUMN_RELEVATION});
         }
      }
   }
}