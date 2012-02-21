<?php
class Teams_Search extends Search {
   public function runSearch() {
      $model = new Teams_Model_Persons();
      // join here
      
      $result = $model
         ->joinFK(Teams_Model_Persons::COLUMN_ID_TEAM)
         ->where(Teams_Model::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))
         ->search($this->getSearchString());
      if($result != false){
         foreach ($result as $res) {
            $name = null;
            if($res->{Teams_Model_Persons::COLUMN_DEGREE} != null) $name .= $res->{Teams_Model_Persons::COLUMN_DEGREE}.' ';
            $name .= $res->{Teams_Model_Persons::COLUMN_NAME}.' '.$res->{Teams_Model_Persons::COLUMN_SURNAME}.' '.$res->{Teams_Model_Persons::COLUMN_DEGREE_AFTER};
            if($res->{Teams_Model::COLUMN_NAME} != null) $name .= ' ('.$res->{Teams_Model::COLUMN_NAME}.')';
            
            $this->addResult(
               $name,
               $this->link()->param('sid', $res->{Teams_Model_Persons::COLUMN_ID})->anchor('team-person-'. $res->{Teams_Model_Persons::COLUMN_ID}),
               $res->{Teams_Model_Persons::COLUMN_TEXT}, $res->{Search::COLUMN_RELEVATION});
         }
      }
   }
}
?>