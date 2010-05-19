<?php
class Bands_Search extends Search {
   public function runSearch() {
      $model = new Bands_Model();
      $result = $model->search($this->getSearchString(), !$this->getCategory()->getRights()->isWritable());

      while ($res = $result->fetch()) {
         $this->addResult($res->{Bands_Model::COLUMN_NAME},
                 $this->link()->route('detail', array('urlkey' => $res->{Bands_Model::COLUMN_URLKEY})),
                 $res->{Bands_Model::COLUMN_TEXT}, $res->{Search::COLUMN_RELEVATION});
      }
   }
}
?>