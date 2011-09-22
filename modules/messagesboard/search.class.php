<?php
class MessagesBoard_Search extends Search {
   public function runSearch() {
      $model = new MessagesBoard_Model();
      $result = $model->where(MessagesBoard_Model::COLUMN_ID_CATEGORY.' = :idc', 
         array('idc'=>$this->category()->getId()))->search($this->getSearchString());

      foreach ($result as $res) {
         $text = $res->{MessagesBoard_Model::COLUMN_TEXT};
         $this->addResult(
            vve_tpl_truncate(strip_tags($res->{MessagesBoard_Model::COLUMN_TEXT}), 70),
            $this->link()->param('id', $res->{MessagesBoard_Model::COLUMN_ID}),
            $text, $res->{Search::COLUMN_RELEVATION});
      }
   }
}
?>