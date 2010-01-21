<?php
class Orderform_SiteMap extends SiteMap {
	public function run() {
      // kategorie
      $this->addCategoryItem(filectime("./".AppCore::MODULES_DIR."/orderform/controller.class.php"));
	}
}
?>