<?php
/*
 * Třída modelu s listem blogů
 * 
 * note:
 * první je vždy název modulu a potom název modelu
 * první písmena dle standardu jsou velká 
 */
class BlogsListModel extends DbModel {
	public function getBlogList($startRecord, $numRecords) {
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
		->limit($startRecord, $numRecords)
		->where(BlogDetailModel::COLUM_ID_ITEM.' = '.$this->getModule()->getId());


		$blogs = $this->getDb()->fetchAssoc($sqlSelect);
		return $blogs;
	}

	public function getNumBlogs() {
		$sqlCount = $this->getDb()->select()->from($this->getModule()->getDbTable(), array("count"=>"COUNT(*)"))
									->where(BlogDetailModel::COLUM_ID_ITEM. ' = '.$this->getModule()->getId());
//											->where(self::COLUMN_NEWS_DELETED." = ".(int)false);

		$count = $this->getDb()->fetchObject($sqlCount);

		return $count->count;
	}
}
?>