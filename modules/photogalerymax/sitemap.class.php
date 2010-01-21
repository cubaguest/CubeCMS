<?php
class PhotogalerySiteMap extends SiteMap {
	/**
	 * Sloupec s časem vytvoření
	 * @var string
	 */
	const COLUM_TIME = 'time';
	
	public function run() {
//		Načtení posledního záznamu v db
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(), self::COLUM_TIME)
											 ->order(self::COLUM_TIME, 'DESC')
											 ->limit(0,1);
											 
		$lastPhoto = $this->getDb()->fetchObject($sqlSelect);									 

		$this->addItem($this->getLink(), $lastPhoto->{self::COLUM_TIME});
	}
	
}
?>