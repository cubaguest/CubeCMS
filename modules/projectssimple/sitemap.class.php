<?php
class ProjectsSimple_SiteMap extends SiteMap {
	public function run() {
//      // kategorie
//      $model = new Articles_Model();
//      $this->setCategoryLink(new DateTime($model->getLastChange($this->category()->getId())));
//      $records = $model->where(Articles_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Articles_Model::COLUMN_CONCEPT.' = 0 '
//         .'AND '.Articles_Model::COLUMN_URLKEY.' IS NOT NULL'
//         .' AND '.Articles_Model::COLUMN_ADD_TIME.' <= NOW()',
//         array('idc' => $this->category()->getId()))
//         ->limit(0, $this->getMaxItems())->records();
//
//      foreach ($records as $record) {
//         $this->addItem($this->link()->route('detail', array('urlkey' => $record->{Articles_Model::COLUMN_URLKEY})),
//            $record->{Articles_Model::COLUMN_NAME},
//            new DateTime($record->{Articles_Model::COLUMN_EDIT_TIME}));
//      }
//
//      $this->setLinkMore($this->link()->route('archive'), $this->tr('Archiv'));
	}
}
?>