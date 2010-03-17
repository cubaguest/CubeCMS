<?php
class Articles_SiteMap extends SiteMap {
	public function run() {
      $articleModel = new Articles_Model_List();
      // kategorie
      $this->addCategoryItem(new DateTime($articleModel->getLastChange($this->category()->getId())));
      // články
      if($this->isFull()){
         $articles = $articleModel->getListAll($this->category()->getId());
      } else {
         $articles = $articleModel->getList($this->category()->getId(),0,self::SHORT_NUM_RECORD_PER_CAT);
      }

      while ($article = $articles->fetch()) {
         $this->addItem($this->link()->route('detail', array('urlkey' => $article->{Articles_Model_Detail::COLUMN_URLKEY})),
            $article->{Articles_Model_Detail::COLUMN_NAME},
            new DateTime($article->{Articles_Model_Detail::COLUMN_EDIT_TIME}));
      }
      $this->addArchiveLink();
	}

   public function addArchiveLink() {
      if(!$this->isFull()){
         $this->addItem($this->link()->route('archive'),_('další...'));
      }
   }
}
?>