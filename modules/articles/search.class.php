<?php
class ArticlesSearch extends Search {
	public function runSearch() {

      $sqlSearch = $this->getDb()->select()->table($this->getModule()->getDbTable())
      ->colums(array(
            parent::RESULT_INDEX_RELEVANCE => 'MATCH ('.NewsDetailModel::COLUMN_NEWS_LABEL.'_'.Locale::getLang()
            .') AGAINST (\''.$this->getDb()->escapeString($this->getSearchString()).'\') + MATCH ('
            .NewsDetailModel::COLUMN_NEWS_TEXT.'_'.Locale::getLang()
            .') AGAINST (\''.$this->getDb()->escapeString($this->getSearchString()).'\')', Db::COLUMN_ALL))
      ->where('MATCH('.NewsDetailModel::COLUMN_NEWS_LABEL.'_'.Locale::getLang().')',
         "AGAINST ('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)", null,Db::COND_OPERATOR_OR)
      ->where('MATCH('.NewsDetailModel::COLUMN_NEWS_TEXT.'_'.Locale::getLang().')',
         "AGAINST ('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)", null,Db::COND_OPERATOR_AND)
      ->where(NewsDetailModel::COLUMN_NEWS_ID_ITEM, $this->getItems(), Db::OPERATOR_IN, Db::COND_OPERATOR_AND);

//echo $sqlSearch;
      while ($row = $this->getDb()->fetchAssoc($sqlSearch)) {
         $this->addResult($this->getCategory($row[TextDetailModel::COLUMN_ID_ITEM]),
            $this->getLink($row[TextDetailModel::COLUMN_ID_ITEM])
               ->article($row[NewsDetailModel::COLUMN_NEWS_LABEL.'_'.Locale::getLang()],
               $row[NewsDetailModel::COLUMN_NEWS_ID_NEW]),
            $row[NewsDetailModel::COLUMN_NEWS_TEXT.'_'.Locale::getLang()],
            $row[parent::RESULT_INDEX_RELEVANCE],
            $row[NewsDetailModel::COLUMN_NEWS_LABEL.'_'.Locale::getLang()]);
      }
	}
}
?>