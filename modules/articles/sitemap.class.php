<?php
class Articles_SiteMap extends SiteMap {
	public function run() {
      $articleModel = new Articles_Model_List($this->sys());

      // kategorie
      $this->addCategoryItem($articleModel->getLastChange());
      $articleArr = $articleModel->getListArticles();
      foreach ($articleArr as $article) {
         $this->addItem($this->link()->article($article[Articles_Model_Detail::COLUMN_ARTICLE_LABEL],
               $article[Articles_Model_Detail::COLUMN_ARTICLE_ID]),
            $article[Articles_Model_Detail::COLUMN_ARTICLE_LABEL],
            $article[Articles_Model_Detail::COLUMN_ARTICLE_EDIT_TIME]);
      }
	}
}
?>