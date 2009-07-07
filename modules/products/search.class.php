<?php
class Products_Search extends Search {
   public function runSearch() {
      $sqlSearch = $this->getDb()->select()->table($this->module()->getDbTable())
      ->colums(array(
            parent::RESULT_INDEX_RELEVANCE => 'MATCH('.Products_Model_Detail::COLUMN_PRODUCT_LABEL.'_'.Locale::getLang()
            .') AGAINST(\''.$this->getDb()->escapeString($this->getSearchString()).'\') + MATCH('
            .Products_Model_Detail::COLUMN_PRODUCT_TEXT.'_'.Locale::getLang()
            .') AGAINST(\''.$this->getDb()->escapeString($this->getSearchString()).'\')', Db::COLUMN_ALL))
      ->where('MATCH('.Products_Model_Detail::COLUMN_PRODUCT_LABEL.'_'.Locale::getLang().')',
         "AGAINST('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)", null,Db::COND_OPERATOR_OR)
      ->where('MATCH('.Products_Model_Detail::COLUMN_PRODUCT_TEXT.'_'.Locale::getLang().')',
         "AGAINST('".$this->getDb()->escapeString($this->getSearchString())."' IN BOOLEAN MODE)", null,Db::COND_OPERATOR_AND)
      ->where(Products_Model_Detail::COLUMN_PRODUCT_ID_ITEM, $this->getItems(), Db::OPERATOR_IN, Db::COND_OPERATOR_AND);

      while ($row = $this->getDb()->fetchAssoc($sqlSearch)) {
         $this->addResult($this->getCategory($row[Products_Model_Detail::COLUMN_PRODUCT_ID_ITEM]),
            $this->link($row[Products_Model_Detail::COLUMN_PRODUCT_ID_ITEM])
            ->article($row[Products_Model_Detail::COLUMN_PRODUCT_LABEL.'_'.Locale::getLang()],
               $row[Products_Model_Detail::COLUMN_PRODUCT_ID]),
            $row[Products_Model_Detail::COLUMN_PRODUCT_TEXT.'_'.Locale::getLang()],
            $row[parent::RESULT_INDEX_RELEVANCE],
            $row[Products_Model_Detail::COLUMN_PRODUCT_LABEL.'_'.Locale::getLang()]);
      }
   }
}
?>