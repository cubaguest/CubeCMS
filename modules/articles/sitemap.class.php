<?php
class Articles_SiteMap extends SiteMap {
	public function run() {
      $articleModel = new Articles_Model_List();
      // kategorie
      $this->setCategoryLink(new DateTime($articleModel->getLastChange($this->category()->getId())));
      // články
      $articles = $articleModel->getList($this->category()->getId(),0, $this->getMaxItems());

      while ($article = $articles->fetch()) {
         $this->addItem($this->link()->route('detail', array('urlkey' => $article->{Articles_Model_Detail::COLUMN_URLKEY})),
            $article->{Articles_Model_Detail::COLUMN_NAME},
            new DateTime($article->{Articles_Model_Detail::COLUMN_EDIT_TIME}));
      }
      $this->setLinkMore($this->link()->route('archive'), _('archiv'));
	}
}
?>