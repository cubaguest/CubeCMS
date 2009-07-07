<?php
/*
 * Třída modelu s listem referencí
 */
class References_Model_List extends Model_Db {
	/**
	 * Celkový počet novinek
	 * @var integer
	 */
	private $allRefCount = 0;
	private $countRefLoaded = false;
	
	/**
	 * Tabulka s uživateli
	 * @var string
	 */
	private $tableUsers = null;
	
	/**
	 * Metoda vrací počet článků
	 *
	 * @return integer -- počet článků
	 */
	public function getCountReferences() {
		if(!$this->countRefLoaded){
         $sqlCount = $this->getDb()->select()->table($this->module()->getDbTable())
         ->colums(array("count"=>"COUNT(*)"))
         ->where(References_Model_Detail::COLUMN_ID_ITEM, $this->module()->getId());
			$count = $this->getDb()->fetchObject($sqlCount);
			$this->allRefCount = $count->count;
			$this->countRefLoaded = true;
		}
		return $this->allRefCount;
	}
	
	
	/**
	 * Metoda vrací pole s vybranými referencemi
	 * 
	 * @return array -- pole referencí
	 */
   public function getSelectedListReferences($from, $count=5) {
      echo $sqlSelect = $this->getDb()->select()->table($this->module()->getDbTable(), 'refer')
      ->colums(array(References_Model_Detail::COLUMN_NAME => "IFNULL(".References_Model_Detail::COLUMN_NAME
            .'_'.Locale::getLang().", ".References_Model_Detail::COLUMN_NAME.'_'.Locale::getDefaultLang().")",
            References_Model_Detail::COLUMN_TEXT => "IFNULL(".References_Model_Detail::COLUMN_TEXT.'_'.Locale::getLang()
            .", ".References_Model_Detail::COLUMN_TEXT.'_'.Locale::getDefaultLang().")",
            References_Model_Detail::COLUMN_ID, References_Model_Detail::COLUMN_TIME))
      ->join(array('photo' => $this->module()->getDbTable(2)), 
         array('refer' => References_Model_Detail::COLUMN_ID,
         References_Model_Photos::COLUMN_ID_REFERENCE), Db::JOIN_LEFT, References_Model_Photos::COLUMN_FILE)
      ->where("refer.".References_Model_Detail::COLUMN_ID_ITEM, $this->module()->getId())
      ->limit($from, $count)
      ->group('photo.'.References_Model_Photos::COLUMN_ID_REFERENCE)
      ->order("refer.".References_Model_Detail::COLUMN_TIME, Db::ORDER_DESC);
		$returArray = $this->getDb()->fetchAll($sqlSelect);
		return $returArray;
	}

/**
	 * Metoda vrací pole se všemi referencí
	 *
	 * @return array -- pole referencí
	 */
	public function getListReferences() {
      $sqlSelect = $this->getDb()->select()->table($this->module()->getDbTable(), 'refertb')
      ->colums(array(References_Model_Detail::COLUMN_NAME => "IFNULL(".References_Model_Detail::COLUMN_NAME
            .'_'.Locale::getLang().", ".References_Model_Detail::COLUMN_NAME.'_'.Locale::getDefaultLang().")",
            References_Model_Detail::COLUMN_TEXT => "IFNULL(".References_Model_Detail::COLUMN_TEXT
            .'_'.Locale::getLang().", ".References_Model_Detail::COLUMN_TEXT.'_'.Locale::getDefaultLang().")",
            References_Model_Detail::COLUMN_ID_USER, References_Model_Detail::COLUMN_ID,
            References_Model_Detail::COLUMN_EDIT_TIME))
      ->where("refertb.".References_Model_Detail::COLUMN_ID_ITEM, $this->module()->getId())
      ->order("refertb.".References_Model_Detail::COLUMN_TIME, Db::ORDER_DESC);

		$returArray = $this->getDb()->fetchAll($sqlSelect);
		return $returArray;
	}

   public function getLastChange() {
      $sqlSelect = $this->getDb()->select()
      ->table($this->module()->getDbTable())
      ->colums(References_Model_Detail::COLUMN_TIME)
      ->limit(0, 1)
      ->order(References_Model_Detail::COLUMN_TIME, Db::ORDER_DESC);

      $returArray = $this->getDb()->fetchObject($sqlSelect);

      if(!empty ($returArray)){
         $returArray = $returArray->{References_Model_Detail::COLUMN_TIME};
      }
		return $returArray;
   }
	
}

?>