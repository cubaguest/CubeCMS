<?php
class Text_SiteMap extends SiteMap {
	public function run() {
      $textModel = new Text_Model_Detail($this->sys());
      // kategorie
      $this->addCategoryItem($textModel->getLastChange());
	}
}
?>