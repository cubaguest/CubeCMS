<?php
class Text_Search extends Search {
	public function runSearch() {

      $sqlSearch = $this->getDb()->select()->table(Db::table(Text_Model_Detail::DB_TABLE))
      ->colums(array(
            parent::RESULT_INDEX_RELEVANCE => 'MATCH('.Text_Model_Detail::COLUMN_TEXT.'_'.Locale::getLang()
            .') AGAINST(\''.$this->getDb()->escapeString($this->getSearchString()).'\')', Db::COLUMN_ALL))
      ->where('MATCH('.Text_Model_Detail::COLUMN_TEXT.'_'.Locale::getLang().')',
         "AGAINST('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)", null,Db::COND_OPERATOR_AND)
      ->where(Text_Model_Detail::COLUMN_ID_ITEM, $this->getItems(), Db::OPERATOR_IN, Db::COND_OPERATOR_AND);

//echo $sqlSearch;
      while ($row = $this->getDb()->fetchAssoc($sqlSearch)) {
         $this->addResult($this->getCategory($row[Text_Model_Detail::COLUMN_ID_ITEM]),
            $this->link($row[Text_Model_Detail::COLUMN_ID_ITEM]),
            $row[Text_Model_Detail::COLUMN_TEXT.'_'.Locale::getLang()], $row[parent::RESULT_INDEX_RELEVANCE]);
      }

	}
}
?>