<?php
class TextSearch extends Search {
	public function runSearch() {
      $sqlSearch = $this->getDb()->select()->table($this->getModule()->getDbTable())
      ->where('MATCH('.TextDetailModel::COLUMN_TEXT.'_'.Locale::getLang().')',
         "AGAINST ('".$this->getSearchString()."' IN BOOLEAN MODE)", null,Db::COND_OPERATOR_AND)
      ->where(TextDetailModel::COLUMN_ID_ITEM, $this->getItems(), Db::OPERATOR_IN, Db::COND_OPERATOR_AND)
      ->order('5 * MATCH('.TextDetailModel::COLUMN_TEXT.'_'.Locale::getLang()
         .') AGAINST ('.$this->getSearchString().') + MATCH('.TextDetailModel::COLUMN_TEXT
         .'_'.Locale::getLang().') AGAINST ('.$this->getSearchString().')', Db::ORDER_DESC);

      echo $sqlSearch;

	}
}
?>