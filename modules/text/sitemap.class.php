<?php
class Text_SiteMap extends SiteMap {
	public function run() {
      $textModel = new Text_Model_Detail();
      // kategorie
      $this->addCategoryItem($textModel->getLastChange($this->category()->getId()));
	}
}
?>