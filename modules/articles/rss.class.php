<?php
class Articles_Rss extends Rss {
   public function  runController() {
      $model = new Articles_Model_List();
      $articles = $model->getList($this->category()->getId(), 0, VVE_FEED_NUM);

      while ($article = $articles->fetch()) {
         if((string)$article->{Articles_Model_Detail::COLUMN_ANNOTATION} != null){
            $text = (string)$article->{Articles_Model_Detail::COLUMN_ANNOTATION};
         } else {
            $text = (string)$article->{Articles_Model_Detail::COLUMN_TEXT};
         }
         $this->getRssComp()->addItem($article->{Articles_Model_Detail::COLUMN_NAME}, $text,
                 $this->link()->route('detail', array('urlkey' => $article->{Articles_Model_Detail::COLUMN_URLKEY})),
                 new DateTime($article->{Articles_Model_Detail::COLUMN_ADD_TIME}),
                 $article->{Model_Users::COLUMN_USERNAME}, null, null);
      }
   }
}
?>
