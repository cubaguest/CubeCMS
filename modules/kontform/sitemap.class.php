<?php
class Kontform_SiteMap extends SiteMap {
	public function run() {
      $this->addCategoryItem(filectime("./".AppCore::MODULES_DIR."/kontform/controller.class.php"));
	}
}
?>