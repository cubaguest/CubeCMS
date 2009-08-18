<?php
class News_Search extends Search {
	public function runSearch() {

      $sqlSearch = $this->getDb()->select()->table(Db::table(News_Model_Detail::DB_TABLE))
      ->colums(array(
            parent::RESULT_INDEX_RELEVANCE => 'MATCH('.News_Model_Detail::COLUMN_NEWS_LABEL.'_'.Locale::getLang()
            .') AGAINST(\''.$this->getDb()->escapeString($this->getSearchString()).'\') + MATCH('
            .News_Model_Detail::COLUMN_NEWS_TEXT.'_'.Locale::getLang()
            .') AGAINST(\''.$this->getDb()->escapeString($this->getSearchString()).'\')', Db::COLUMN_ALL))
      ->where('MATCH('.News_Model_Detail::COLUMN_NEWS_LABEL.'_'.Locale::getLang().')',
         "AGAINST('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)", null,Db::COND_OPERATOR_OR)
      ->where('MATCH('.News_Model_Detail::COLUMN_NEWS_TEXT.'_'.Locale::getLang().')',
         "AGAINST('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)", null,Db::COND_OPERATOR_AND)
      ->where(News_Model_Detail::COLUMN_NEWS_ID_ITEM, $this->getItems(), Db::OPERATOR_IN, Db::COND_OPERATOR_AND);

//echo $sqlSearch;
      while ($row = $this->getDb()->fetchAssoc($sqlSearch)) {
         $this->addResult($this->getCategory($row[News_Model_Detail::COLUMN_NEWS_ID_ITEM]),
            $this->link($row[News_Model_Detail::COLUMN_NEWS_ID_ITEM])
               ->article($row[News_Model_Detail::COLUMN_NEWS_LABEL.'_'.Locale::getLang()],
               $row[News_Model_Detail::COLUMN_NEWS_ID_NEW]),
            $row[News_Model_Detail::COLUMN_NEWS_TEXT.'_'.Locale::getLang()],
            $row[parent::RESULT_INDEX_RELEVANCE],
            $row[News_Model_Detail::COLUMN_NEWS_LABEL.'_'.Locale::getLang()]);
      }
	}
}
?>