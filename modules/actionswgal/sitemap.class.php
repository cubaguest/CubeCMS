<?php
class Actionswgal_SiteMap extends SiteMap {
	public function run() {
      $actionModel = new Actions_Model_List($this->sys());

      // kategorie
      $this->addCategoryItem($actionModel->getLastChange());
      $actionsArr = $actionModel->getListActions();
      foreach ($actionsArr as $action) {
         $this->addItem($this->link()->article($action[Actions_Model_Detail::COLUMN_ACTION_LABEL],
               $action[Actions_Model_Detail::COLUMN_ACTION_ID]),
               $action[Actions_Model_Detail::COLUMN_ACTION_LABEL],
               $action[Actions_Model_Detail::COLUMN_ACTION_TIME]);
      }
	}
}
?>