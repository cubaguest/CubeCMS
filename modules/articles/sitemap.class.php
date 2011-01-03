<?php
class Articles_SiteMap extends SiteMap {
	public function run() {
      // kategorie
      $model = new Articles_Model();
      $this->setCategoryLink(new DateTime($model->getLastChange($this->category()->getId())));
      $records = $model->where(Articles_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Articles_Model::COLUMN_PUBLIC.' = 1 AND '
         .Articles_Model::COLUMN_URLKEY.' IS NOT NULL',
         array('idc' => $this->category()->getId()))
         ->limit(0, $this->getMaxItems())->records();

      foreach ($records as $record) {
         $this->addItem($this->link()->route('detail', array('urlkey' => $record->{Articles_Model::COLUMN_URLKEY})),
            $record->{Articles_Model::COLUMN_NAME},
            new DateTime($record->{Articles_Model::COLUMN_EDIT_TIME}));
      }

      $this->setLinkMore($this->link()->route('archive'), $this->tr('Archiv'));
	}
}
?>