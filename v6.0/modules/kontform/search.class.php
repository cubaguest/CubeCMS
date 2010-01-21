<?php
class Kontform_Search extends Search {
	public function runSearch() {

      $sqlSearch = $this->getDb()->select()->table($this->getModule()->getDbTable())
      ->colums(array(
            parent::RESULT_INDEX_RELEVANCE => 'MATCH ('.TextDetailModel::COLUMN_TEXT.'_'.Locale::getLang()
            .') AGAINST (\''.$this->getDb()->escapeString($this->getSearchString()).'\')', Db::COLUMN_ALL))
      ->where('MATCH('.TextDetailModel::COLUMN_TEXT.'_'.Locale::getLang().')',
         "AGAINST ('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)", null,Db::COND_OPERATOR_AND)
      ->where(TextDetailModel::COLUMN_ID_ITEM, $this->getItems(), Db::OPERATOR_IN, Db::COND_OPERATOR_AND);

//echo $sqlSearch;
      while ($row = $this->getDb()->fetchAssoc($sqlSearch)) {
         $this->addResult($this->getCategory($row[TextDetailModel::COLUMN_ID_ITEM]),
            $this->getLink($row[TextDetailModel::COLUMN_ID_ITEM]),
            $row[TextDetailModel::COLUMN_TEXT.'_'.Locale::getLang()], $row[parent::RESULT_INDEX_RELEVANCE]);
      }

	}
}
?>