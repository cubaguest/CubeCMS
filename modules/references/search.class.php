<?php
class ReferencesSearch extends Search {
	public function runSearch() {

      $sqlSearch = $this->getDb()->select()->table($this->getModule()->getDbTable())
      ->colums(array(
            parent::RESULT_INDEX_RELEVANCE => 'MATCH ('.ReferenceModel::COLUMN_REFERENCE_NAME.'_'.Locale::getLang()
            .') AGAINST (\''.$this->getDb()->escapeString($this->getSearchString()).'\') + MATCH ('
            .ReferenceModel::COLUMN_REFERENCE_LABEL.'_'.Locale::getLang()
            .') AGAINST (\''.$this->getDb()->escapeString($this->getSearchString()).'\')', Db::COLUMN_ALL))
      ->where('MATCH('.ReferenceModel::COLUMN_REFERENCE_NAME.'_'.Locale::getLang().')',
         "AGAINST ('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)", null,Db::COND_OPERATOR_OR)
      ->where('MATCH('.ReferenceModel::COLUMN_REFERENCE_LABEL.'_'.Locale::getLang().')',
         "AGAINST ('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)", null,Db::COND_OPERATOR_AND)
      ->where(ReferenceModel::COLUMN_REFERENCE_ID_ITEM, $this->getItems(), Db::OPERATOR_IN, Db::COND_OPERATOR_AND);

      $referencesArray = array();
      while ($row = $this->getDb()->fetchAssoc($sqlSearch)) {
         $this->addResult($this->getCategory($row[ReferenceModel::COLUMN_REFERENCE_ID_ITEM]),
            $this->getLink($row[ReferenceModel::COLUMN_REFERENCE_ID_ITEM]),
            $row[ReferenceModel::COLUMN_REFERENCE_LABEL.'_'.Locale::getLang()],
            $row[parent::RESULT_INDEX_RELEVANCE],
            $row[ReferenceModel::COLUMN_REFERENCE_NAME.'_'.Locale::getLang()]);
         $referencesArray[$row[ReferenceModel::COLUMN_REFERENCE_ID_ITEM]] =
            $row[ReferenceModel::COLUMN_REFERENCE_NAME.'_'.Locale::getLang()];
      }

      $sqlSearch = $this->getDb()->select()->table($this->getModule()->getDbTable(2), 'texts')
      ->colums(array(
            parent::RESULT_INDEX_RELEVANCE => 'MATCH ('.ReferenceModel::COLUMN_TEXT.'_'.Locale::getLang()
            .') AGAINST (\''.$this->getDb()->escapeString($this->getSearchString()).'\')', Db::COLUMN_ALL))
      ->join(array('refer' => $this->getModule()->getDbTable()),
         array(ReferenceModel::COLUMN_REFERENCE_ID_ITEM, 'texts' => ReferenceModel::COLUMN_REFERENCE_ID_ITEM),
         null, array(ReferenceModel::COLUMN_REFERENCE_NAME.'_'.Locale::getLang()))
      ->where('MATCH('.ReferenceModel::COLUMN_TEXT.'_'.Locale::getLang().')',
         "AGAINST ('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)",
         null,Db::COND_OPERATOR_AND)
      ->where('texts.'.ReferenceModel::COLUMN_REFERENCE_ID_ITEM, $this->getItems(), Db::OPERATOR_IN,
         Db::COND_OPERATOR_AND);

      while ($row = $this->getDb()->fetchAssoc($sqlSearch)) {
         $this->addResult($this->getCategory($row[ReferenceModel::COLUMN_REFERENCE_ID_ITEM]),
            $this->getLink($row[ReferenceModel::COLUMN_REFERENCE_ID_ITEM]),
            $row[ReferenceModel::COLUMN_TEXT.'_'.Locale::getLang()], $row[parent::RESULT_INDEX_RELEVANCE]
          ,$row[ReferenceModel::COLUMN_REFERENCE_NAME.'_'.Locale::getLang()]
            );
      }
	}
}
?>