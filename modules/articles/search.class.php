<?php
class Articles_Search extends Search {
   public function runSearch() {
      $model = new Articles_Model();
      $result = $model->search($this->getCategory()->getId(), $this->getSearchString(), !$this->getCategory()->getRights()->isWritable());

      while ($res = $result->fetch()) {
         if((string)$res->{Articles_Model::COLUMN_ANNOTATION} != null){
            $text = $res->{Articles_Model::COLUMN_ANNOTATION};
         } else {
            $text = $res->{Articles_Model::COLUMN_TEXT};
         }
         $this->addResult($res->{Articles_Model::COLUMN_NAME},
                 $this->link()->route('detail', array('urlkey' => $res->{Articles_Model::COLUMN_URLKEY})),
                 $text, $res->{Search::COLUMN_RELEVATION});
      }
   }
}
?>