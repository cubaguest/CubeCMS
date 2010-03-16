<?php
class Cinemaprogram_Search extends Search {
   public function runSearch() {
      $model = new CinemaProgram_Model_Detail();
      $result = $model->search($this->getSearchString());

      while ($res = $result->fetch()) {
         $res->{CinemaProgram_Model_Detail::COL_NAME_ORIG} != null ?
                 $oName = ' ('.$res->{CinemaProgram_Model_Detail::COL_NAME_ORIG}.')' : $oName = null;
         $this->addResult($res->{CinemaProgram_Model_Detail::COL_NAME}.$oName,
                 $this->link()->route('detail', array('id' => $res->{CinemaProgram_Model_Detail::COL_ID},
                     'name' => vve_cr_url_key($res->{CinemaProgram_Model_Detail::COL_NAME}))),
                 $res->{CinemaProgram_Model_Detail::COL_LABEL_CLEAR}, $res->{Search::COLUMN_RELEVATION});
      }
   }
}
?>