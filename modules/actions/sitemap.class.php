<?php
class Actions_SiteMap extends SiteMap {
	public function run() {
      $actionsModel = new Actions_Model_List();
      // kategorie
      $this->addCategoryItem(new DateTime($actionsModel->getLastChange($this->category()->getId())));

      $actions = $actionsModel->getAllActions($this->category()->getId());
      while ($action = $actions->fetch()) {
         $this->addItem($this->link()->route('detail', array('urlkey' => $action->{Actions_Model_Detail::COLUMN_URLKEY})),
            $action->{Actions_Model_Detail::COLUMN_NAME},
            new DateTime($action->{Actions_Model_Detail::COLUMN_CHANGED}));
      }
	}
}
?>