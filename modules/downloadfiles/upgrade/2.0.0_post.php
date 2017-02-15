<?php
// projít všechny soubory a podle kategorií je rozřadit do nových skupin

$mFiles = new DownloadFiles_Model();

$files = $mFiles->columns(array('*', 'id_category'))->records();
$sections = array();
foreach ($files as $file) {
   if(!isset($sections[$file->{DownloadFiles_Model::COLUMN_ID_CATEGORY}])){
      // create sections
      $sec = DownloadFiles_Model_Sections::getNewRecord();
      $sec->{DownloadFiles_Model_Sections::COLUMN_ID_CATEGORY} = $file->{DownloadFiles_Model::COLUMN_ID_CATEGORY};
      $sec->{DownloadFiles_Model_Sections::COLUMN_NAME} = 'Základní';
      $sec->save();
      $sections[$file->{DownloadFiles_Model::COLUMN_ID_CATEGORY}] = $sec->getPK();
   } 
   $file->{DownloadFiles_Model::COLUMN_ID_SECTION} = $sections[$file->{DownloadFiles_Model::COLUMN_ID_CATEGORY}];
   $file->save();
}