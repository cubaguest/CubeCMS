<?php
// všechny obrázky uložit do modelu pod položku soubor
$model = new HPSlideShow_Model();
$items = $model->columns(array(HPSlideShow_Model::COLUMN_ID))->records();
if($items){
   foreach($items as $i) {
      $model
         ->where(HPSlideShow_Model::COLUMN_ID." = :id", array('id' => $i->getPK()))
         ->update(array(HPSlideShow_Model::COLUMN_FILE => $i->getPK().'.jpg'));
   }
}
