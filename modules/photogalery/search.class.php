<?php
/**
 * @TODO asi přidat hledání ve fotkách podle popisku
 */
class Photogalery_Search extends Search {
	public function runSearch() {
      $model = new Text_Model();
      $result = $model
         ->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))
         ->search($this->getSearchString());
      if($result != false){
         foreach ($result as $res) {
            $this->addResult($res->{Text_Model::COLUMN_LABEL}, $this->link(), $res->{Text_Model::COLUMN_TEXT}, $res->{Search::COLUMN_RELEVATION});
         }
      }
	}
}
?>