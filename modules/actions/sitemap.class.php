<?php
class Actions_SiteMap extends SiteMap {
	public function run() {
      $actionsModel = new Actions_Model_List();
      // kategorie
      $this->setCategoryLink(new DateTime($actionsModel->getLastChange($this->category()->getId())));

      $actions = $actionsModel->getActionsByAdded($this->category()->getId(), $this->getMaxItems());

      while ($action = $actions->fetch()) {
         $this->addItem($this->link()->route('detail', array('urlkey' => $action->{Actions_Model_Detail::COLUMN_URLKEY})),
            $action->{Actions_Model_Detail::COLUMN_NAME},
            new DateTime($action->{Actions_Model_Detail::COLUMN_CHANGED}));
      }

      $this->setLinkMore($this->link()->route('archive'),_('archiv'));
	}
}
?>