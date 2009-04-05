<?php
class TextSiteMap extends SiteMap {
	public function run() {
      $textModel = new TextDetailModel();
      // kategorie
      $this->addCategoryItem($textModel->getLastChange());
	}
}
?>