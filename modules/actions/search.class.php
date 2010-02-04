<?php
class Actions_Search extends Search {
   public function runSearch() {
      $model = new Actions_Model_Detail();
      $result = $model->search($this->getCategory()->getId(), $this->getSearchString(), !$this->getCategory()->getRights()->isWritable());

      while ($res = $result->fetch()) {
         $text = null;
         $label = $res->{Actions_Model_Detail::COLUMN_NAME};
         if((string)$res->{Actions_Model_Detail::COLUMN_SUBANME} != null|''){
            $label .= " - ".$res->{Actions_Model_Detail::COLUMN_SUBANME};
         }

         if($res->{Actions_Model_Detail::COLUMN_AUTHOR} != null){
            $text .= $res->{Actions_Model_Detail::COLUMN_AUTHOR}." - ";
         }
         if($res->{Actions_Model_Detail::COLUMN_PLACE} != null){
            $text .= $res->{Actions_Model_Detail::COLUMN_PLACE}." - ";
         }
         $text .= $res->{Actions_Model_Detail::COLUMN_TEXT_CLEAR};

         $this->addResult($label, $this->link()->route('detail',
                 array('urlkey' => $res->{Actions_Model_Detail::COLUMN_URLKEY})),
                 $text, $res->{Search::COLUMN_RELEVATION});
      }
   }
}
?>