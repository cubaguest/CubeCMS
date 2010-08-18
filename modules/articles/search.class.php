<?php
class Articles_Search extends Search {
   public function runSearch() {
      $model = new Articles_Model_Detail();
      $result = $model->search($this->getCategory()->getId(), $this->getSearchString(), !$this->getCategory()->getRights()->isWritable());

      while ($res = $result->fetch()) {
         if((string)$res->{Articles_Model_Detail::COLUMN_ANNOTATION} != null){
            $text = $res->{Articles_Model_Detail::COLUMN_ANNOTATION};
         } else {
            $text = $res->{Articles_Model_Detail::COLUMN_TEXT};
         }
         $this->addResult($res->{Articles_Model_Detail::COLUMN_NAME},
                 $this->link()->route('detail', array('urlkey' => $res->{Articles_Model_Detail::COLUMN_URLKEY})),
                 $text, $res->{Search::COLUMN_RELEVATION});
      }
   }
}
?>