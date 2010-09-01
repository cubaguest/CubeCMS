<?php
class Actions_Rss extends Rss {
   public function  runController() {
      $model = new Actions_Model_List();
      $actions = $model->getActionsByAdded($this->category()->getId(), VVE_FEED_NUM);


      while ($action = $actions->fetch()) {

         $dateTimeStr = null;
         $dateTimeStr = ': '.vve_date("%x", new DateTime($action->{Actions_Model_Detail::COLUMN_DATE_START}));
         $stopDate = vve_date("%x", new DateTime($action->{Actions_Model_Detail::COLUMN_DATE_STOP}));
         if($action->{Actions_Model_Detail::COLUMN_DATE_START} != $action->{Actions_Model_Detail::COLUMN_DATE_STOP}
            AND $action->{Actions_Model_Detail::COLUMN_DATE_STOP} != null) {
            $dateTimeStr .= ' - '.$stopDate;
         }
         if($action->{Actions_Model_Detail::COLUMN_TIME} != null) {
            $time = new DateTime($action->{Actions_Model_Detail::COLUMN_TIME});
            $dateTimeStr .= ' - '.$time->format("G:i");
         }

         $this->getRssComp()->addItem($action->{Actions_Model_Detail::COLUMN_NAME}.$dateTimeStr,$action->{Actions_Model_Detail::COLUMN_TEXT},
                 $this->link()->route('detail', array('urlkey' => $action->{Actions_Model_Detail::COLUMN_URLKEY})),
                 new DateTime($action->{Actions_Model_Detail::COLUMN_ADDED}),
                 $action->{Model_Users::COLUMN_USERNAME},null,null);
      }
   }
}
?>
