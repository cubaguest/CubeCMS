<?php
class ArticlesSearch extends Search {
   public function runSearch() {

      $sqlSearch = $this->getDb()->select()->table($this->getModule()->getDbTable())
      ->colums(array(
            parent::RESULT_INDEX_RELEVANCE => 'MATCH ('.ArticleDetailModel::COLUMN_ARTICLE_LABEL.'_'.Locale::getLang()
            .') AGAINST (\''.$this->getDb()->escapeString($this->getSearchString()).'\') + MATCH ('
            .ArticleDetailModel::COLUMN_ARTICLE_TEXT.'_'.Locale::getLang()
            .') AGAINST (\''.$this->getDb()->escapeString($this->getSearchString()).'\')', Db::COLUMN_ALL))
      ->where('MATCH('.ArticleDetailModel::COLUMN_ARTICLE_LABEL.'_'.Locale::getLang().')',
         "AGAINST ('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)", null,Db::COND_OPERATOR_OR)
      ->where('MATCH('.ArticleDetailModel::COLUMN_ARTICLE_TEXT.'_'.Locale::getLang().')',
         "AGAINST ('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)", null,Db::COND_OPERATOR_AND)
      ->where(ArticleDetailModel::COLUMN_ARTICLE_ID_ITEM, $this->getItems(), Db::OPERATOR_IN, Db::COND_OPERATOR_AND);

      while ($row = $this->getDb()->fetchAssoc($sqlSearch)) {
         $this->addResult($this->getCategory($row[ArticleDetailModel::COLUMN_ARTICLE_ID_ITEM]),
            $this->getLink($row[ArticleDetailModel::COLUMN_ARTICLE_ID_ITEM])
            ->article($row[ArticleDetailModel::COLUMN_ARTICLE_LABEL.'_'.Locale::getLang()],
               $row[ArticleDetailModel::COLUMN_ARTICLE_ID]),
            $row[ArticleDetailModel::COLUMN_ARTICLE_TEXT.'_'.Locale::getLang()],
            $row[parent::RESULT_INDEX_RELEVANCE],
            $row[ArticleDetailModel::COLUMN_ARTICLE_LABEL.'_'.Locale::getLang()]);
      }
   }
}
?>