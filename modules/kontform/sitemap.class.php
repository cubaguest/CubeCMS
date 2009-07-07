<?php
class Kontform_SiteMap extends SiteMap {
	public function run() {
        $kontformModel = new Kontform_Model_Detail($this->sys());
      // kategorie
      $this->addCategoryItem($kontformModel->getLastChange());
	}
}
?>