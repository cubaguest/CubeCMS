<?php
class Articles_Search extends Search {
   public function runSearch() {
      $model = new Articles_Model();
      $result = $model->where(Articles_Model::COLUMN_ID_CATEGORY.' = :idc', array('idc'=>$this->category()->getId()))->search($this->getSearchString());

      foreach ($result as $res) {
//         if((string)$res->{Articles_Model::COLUMN_ANNOTATION} != null){
//            $text = $res->{Articles_Model::COLUMN_ANNOTATION};
//         } else {
            $text = $res->{Articles_Model::COLUMN_TEXT};
//         }
         $this->addResult(
            $res->{Articles_Model::COLUMN_NAME},
            $this->link()->route('detail', array('urlkey' => $res->{Articles_Model::COLUMN_URLKEY})),
            $text, $res->{Search::COLUMN_RELEVATION});
      }
   }
}
?>