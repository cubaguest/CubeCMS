<?php
class ReferencesSiteMap extends SiteMap {
	public function run() {
      $model = new ReferenceModel();

      // kategorie
      $this->addCategoryItem($model->getLastChange());
      
	}
}
?>