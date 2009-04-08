<?php
class ContactsSiteMap extends SiteMap {
	public function run() {
      $model = new ContactModel();

      // kategorie
      $this->addCategoryItem($model->getLastChange());
      
	}
}
?>