<?php
class ArticlesList_SiteMap extends SiteMap {
	public function run() {
      // kategorie
      $this->addCategoryItem(new DateTime($articleModel->getLastChange($this->category()->getId())));
   }
}
?>