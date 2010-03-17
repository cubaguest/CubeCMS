<?php
class Actions_SiteMap extends SiteMap {
	public function run() {
      $actionsModel = new Actions_Model_List();
      // kategorie
      $this->addCategoryItem(new DateTime($actionsModel->getLastChange($this->category()->getId())));

      if($this->isFull()){
         $actions = $actionsModel->getAllActions($this->category()->getId());
      } else {
         $actions = $actionsModel->getActionsByAdded($this->category()->getId(), self::SHORT_NUM_RECORD_PER_CAT);
      }

      while ($action = $actions->fetch()) {
         $this->addItem($this->link()->route('detail', array('urlkey' => $action->{Actions_Model_Detail::COLUMN_URLKEY})),
            $action->{Actions_Model_Detail::COLUMN_NAME},
            new DateTime($action->{Actions_Model_Detail::COLUMN_CHANGED}));
      }

      if(!$this->isFull()){
         $this->addItem($this->link()->route('archive'),_('další...'));
      }
	}
}
?>