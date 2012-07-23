<?php
class DownloadFiles_Search extends Search {
	public function runSearch() {
      $model = new DownloadFiles_Model();
      
      $res = $model
         ->where(DownloadFiles_Model::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))
         ->search($this->getSearchString());
      
      if($res != false){
         foreach ($res as $file) {
            $this->addResult($file->{DownloadFiles_Model::COLUMN_NAME}, 
                  $this->link()->anchor('dwfile-'.$file->{DownloadFiles_Model::COLUMN_FILE}), 
                  $file->{DownloadFiles_Model::COLUMN_TEXT}, 
                  $file->{Search::COLUMN_RELEVATION});
         }
      }
	}
}
?>