<?php
class AdvEvents_Search extends Search {
   public function runSearch() {
      
      
//      $begin = new DateTime();
//      $begin->modify('-1 year');
//      $end = new DateTime();
//      $end->modify('+10 years');
//      $events = AdvEventsBase_Model_Events::searchEvents($begin, $end, array('fulltext' => $this->getSearchString()));
      
      $model = new AdvEventsBase_Model_Events();
      $result = $model
//          ->where(AdvEventsBase_Model_Events::COLUMN_ID_CATEGORY.' = :idc', array('idc'=>$this->category()->getId()))
          ->search($this->getSearchString());
      if(!$result){
         return;
      }
      foreach ($result as $res) {
         $text = $res->{AdvEventsBase_Model_Events::COLUMN_PEREX};
         if($text == null){
            $text = $res->{AdvEventsBase_Model_Events::COLUMN_TEXT};
         }
         $this->addResult(
            $res->{AdvEventsBase_Model_Events::COLUMN_NAME}
            . ( $res->{AdvEventsBase_Model_Events::COLUMN_SUBNAME} != null ? ' &mdash; '.$res->{AdvEventsBase_Model_Events::COLUMN_SUBNAME}.'' : '' ),
            $this->link()->route('detail', array('id' => $res->getPK())),
            $text, $res->{Search::COLUMN_RELEVATION});
      }
   }
}