<?php
class Text_Search extends Search {
	public function runSearch() {
      $model = new Text_Model();
      $result = $model
         ->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :sk', 
            array('idc' => $this->category()->getId(), 'sk' => Text_Controller::TEXT_MAIN_KEY))
         ->search($this->getSearchString());
      if($result != false){
         foreach ($result as $res) {
            $this->addResult($res->{Text_Model::COLUMN_LABEL}, $this->link(), $res->{Text_Model::COLUMN_TEXT}, $res->{Search::COLUMN_RELEVATION});
         }
      }
	}
}
?>