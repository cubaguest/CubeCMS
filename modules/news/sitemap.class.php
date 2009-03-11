<?php
class NewsSiteMap extends SiteMap {
	public function run() {
      $newsModel = new NewsListModel();

      // kategorie
      $this->addCategoryItem($newsModel->getLastChange());

      $newsArr = $newsModel->getListNews();

      foreach ($newsArr as $news) {
         $this->addItem($this->getLink()->article($news[NewsListModel::COLUMN_NEWS_LABEL],
               $news[NewsListModel::COLUMN_NEWS_ID_NEW]), $news[NewsListModel::COLUMN_NEWS_TIME],
               parent::SITEMAP_SITE_CHANGE_MONTHLY);
      }


//		Načtení posledního záznamu v db
//		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(),
//         array(self::COLUM_TIME_ADD, self::COLUM_TIME_EDIT))
//											 ->order(self::COLUM_TIME_ADD, 'DESC')
//											 ->order(self::COLUM_TIME_EDIT, 'DESC')
//											 ->limit(0,1);
											 
//		$lastPhoto = $this->getDb()->fetchObject($sqlSelect);

//      $timeEdit = $lastPhoto->{self::COLUM_TIME_ADD};
//      if($lastPhoto->{self::COLUM_TIME_EDIT} != null){
//         $timeEdit = $lastPhoto->{self::COLUM_TIME_EDIT};
//      }

//		$this->addItem($this->getLink(), $timeEdit);
	}
	
}
?>