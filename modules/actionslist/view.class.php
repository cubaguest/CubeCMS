<?php
class ActionsList_View extends View {
   public function init() {
      $this->template()->addCssFile("style.css");
   }


   public function mainView() {
      $this->template()->addTplFile("list.phtml");
   }

   public function listCatAddView(){
      $this->template()->addTplFile("addgoto.phtml");
   }

   public function exportView() {
      $feed = new Component_Feed(true);

      $feed ->setConfig('type', $this->type);
      $feed ->setConfig('title', $this->category()->getName());
      $feed ->setConfig('desc', $this->category()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION});
      $feed ->setConfig('link', $this->link());

      while ($action = $this->actions->fetch()) {
         $startDate = strftime("%x", $action->{Actions_Model_Detail::COLUMN_DATE_START});
         $stopDate = strftime("%x", $action->{Actions_Model_Detail::COLUMN_DATE_STOP});
         $stopDateString = null;
         if($startDate != $stopDate) {
            $stopDateString = " - ".$stopDate;
         }
         $desc = "<h3>".$startDate.$stopDateString."</h3>";
         $desc .= $action->{Actions_Model_Detail::COLUMN_TEXT};

         $feed->addItem($action->{Actions_Model_Detail::COLUMN_NAME},$desc,
                 $this->link()->category($row->curlkey)->route('detail',
                         array('urlkey' => $action->{Actions_Model_Detail::COLUMN_URLKEY})),
                 new DateTime($action->{Actions_Model_Detail::COLUMN_ADDED}),
                 $action->{Model_Users::COLUMN_USERNAME},null,null,
                 $action->{Actions_Model_Detail::COLUMN_URLKEY}."_".$action->{Actions_Model_Detail::COLUMN_ID}."_".
                 $action->{Actions_Model_Detail::COLUMN_CHANGED});
      }
      $feed->flush();
   }
}

?>