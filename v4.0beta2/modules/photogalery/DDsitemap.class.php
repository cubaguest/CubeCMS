<?php
class PhotogalerySiteMap extends SiteMap {
	/**
	 * Sloupec s časem vytvoření
	 * @var string
	 */
	const COLUM_TIME_EDIT = 'time_edit';
	const COLUM_TIME_ADD = 'time_add';
	
	public function run() {
//		Načtení posledního záznamu v db
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(),
         array(self::COLUM_TIME_ADD, self::COLUM_TIME_EDIT))
											 ->order(self::COLUM_TIME_ADD, 'DESC')
											 ->order(self::COLUM_TIME_EDIT, 'DESC')
											 ->limit(0,1);
											 
		$lastPhoto = $this->getDb()->fetchObject($sqlSelect);									 

      $timeEdit = $lastPhoto->{self::COLUM_TIME_ADD};
      if($lastPhoto->{self::COLUM_TIME_EDIT} != null){
         $timeEdit = $lastPhoto->{self::COLUM_TIME_EDIT};
      }

		$this->addItem($this->getLink(), $timeEdit);
	}
	
}
?>