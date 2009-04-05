<?php
class ArticlesSiteMap extends SiteMap {
	public function run() {
      $articleModel = new ArticlesListModel();

      // kategorie
      $this->addCategoryItem($articleModel->getLastChange());
      $articleArr = $articleModel->getListArticles();
      foreach ($articleArr as $article) {
         $this->addItem($this->getLink()->article($article[ArticleDetailModel::COLUMN_ARTICLE_LABEL],
               $article[ArticleDetailModel::COLUMN_ARTICLE_ID]),
            $article[ArticleDetailModel::COLUMN_ARTICLE_LABEL],
            $article[ArticleDetailModel::COLUMN_ARTICLE_EDIT_TIME],
            parent::SITEMAP_SITE_CHANGE_MONTHLY);
      }
	}
}
?>