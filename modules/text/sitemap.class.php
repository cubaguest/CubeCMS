<?php
class Text_SiteMap extends SiteMap {
	public function run() {
      $textModel = new Text_Model_Detail();
      // kategorie
      $this->addCategoryItem(new DateTime($textModel->getLastChange($this->category()->getId())));
	}
}
?>