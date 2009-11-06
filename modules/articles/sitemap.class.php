<?php
class Articles_SiteMap extends SiteMap {
	public function run() {
      $articleModel = new Articles_Model_Detail();

      // kategorie
      $this->addCategoryItem($articleModel->getLastChange($this->category()->getId()));


      $articleModel = new Articles_Model_List();
      $articleArr = $articleModel->getList($this->category()->getId());
      foreach ($articleArr as $article) {
         $this->addItem($this->link()->route('detail', array('articlekey' => $article->{Articles_Model_Detail::COLUMN_URLKEY})),
            $article->{Articles_Model_Detail::COLUMN_NAME},
            $article->{Articles_Model_Detail::COLUMN_EDIT_TIME});
      }
	}
}
?>