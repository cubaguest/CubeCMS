<?php
class Articles_Rss extends Rss {
   public function  runController() {
      $model = new Articles_Model();
      $records = $model->where(Articles_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Articles_Model::COLUMN_CONCEPT.' = 0'
         .' AND '.Articles_Model::COLUMN_URLKEY.' IS NOT NULL'
         .' AND '.Articles_Model::COLUMN_ADD_TIME.' <= NOW()',
         array('idc' => $this->category()->getId()))
         ->limit(0, VVE_FEED_NUM)->records();

      foreach ($records as $record) {
         if((string)$record->{Articles_Model::COLUMN_ANNOTATION} != null){
            $text = (string)$record->{Articles_Model::COLUMN_ANNOTATION};
         } else {
            $text = (string)$record->{Articles_Model::COLUMN_TEXT};
         }

         $this->getRssComp()->addItem($record->{Articles_Model::COLUMN_NAME}, $text,
                 $this->link()->route('detail', array('urlkey' => $record->{Articles_Model::COLUMN_URLKEY})),
                 new DateTime($record->{Articles_Model::COLUMN_ADD_TIME}),
                 $record->{Model_Users::COLUMN_USERNAME}, null, null);
      }
   }
}
?>
