<?php
class Actions_Search extends Search {
	public function runSearch() {
      $sqlSearch = $this->getDb()->select()->table(Db::table(Actions_Model_Detail::DB_TABLE))
      ->colums(array(
            parent::RESULT_INDEX_RELEVANCE => 'MATCH('.Actions_Model_Detail::COLUMN_ACTION_LABEL.'_'.Locale::getLang()
            .') AGAINST(\''.$this->getDb()->escapeString($this->getSearchString()).'\') + MATCH('
            .Actions_Model_Detail::COLUMN_ACTION_TEXT.'_'.Locale::getLang()
            .') AGAINST(\''.$this->getDb()->escapeString($this->getSearchString()).'\')', Db::COLUMN_ALL))
      ->where(array(array('MATCH('.Actions_Model_Detail::COLUMN_ACTION_LABEL.'_'.Locale::getLang().')',
         "AGAINST('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)",null),
             Db::COND_OPERATOR_OR,
         array('MATCH('.Actions_Model_Detail::COLUMN_ACTION_TEXT.'_'.Locale::getLang().')',
         "AGAINST('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)",null)), Db::COND_OPERATOR_AND)
      ->where(Actions_Model_Detail::COLUMN_ACTION_ID_ITEM, $this->getItems(), Db::OPERATOR_IN, Db::COND_OPERATOR_AND)
      ->where(Actions_Model_Detail::COLUMN_ACTION_DATE_START, time(), "<=")
      ->where(Actions_Model_Detail::COLUMN_ACTION_DATE_STOP, time(), ">=");

      while ($row = $this->getDb()->fetchAssoc($sqlSearch)) {
         $this->addResult($this->getCategory($row[Actions_Model_Detail::COLUMN_ACTION_ID_ITEM]),
            $this->link($row[Actions_Model_Detail::COLUMN_ACTION_ID_ITEM])
               ->article($row[Actions_Model_Detail::COLUMN_ACTION_LABEL.'_'.Locale::getLang()],
               $row[Actions_Model_Detail::COLUMN_ACTION_ID]),
            $row[Actions_Model_Detail::COLUMN_ACTION_TEXT.'_'.Locale::getLang()],
            $row[parent::RESULT_INDEX_RELEVANCE],
            $row[Actions_Model_Detail::COLUMN_ACTION_LABEL.'_'.Locale::getLang()]);
      }
	}
}
?>