<?php
class News_SiteMap extends SiteMap {
	public function run() {
      $newsModel = new News_Model_List($this->sys());

      // kategorie
      $this->addCategoryItem($newsModel->getLastChange());
      $newsArr = $newsModel->getListNews();
      foreach ($newsArr as $news) {
         $this->addItem($this->link()->article($news[News_Model_List::COLUMN_NEWS_LABEL],
               $news[News_Model_List::COLUMN_NEWS_ID_NEW]),
               (string)$news[News_Model_List::COLUMN_NEWS_LABEL],
               $news[News_Model_List::COLUMN_NEWS_TIME],
               parent::SITEMAP_SITE_CHANGE_MONTHLY);
      }
	}
}
?>