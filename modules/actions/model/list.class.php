<?php
/*
 * Třída modelu s listem akcí
 */
class Actions_Model_List extends Model_Db {
	/**
	 * Sloupce u tabulky uživatelů
	 * @var string
	 */
	const COLUMN_USER_NAME = 'username';
	const COLUMN_ISER_ID =	 'id_user';	
	
	/**
	 * Celkový počet novinek
	 * @var integer
	 */
	private $allActionsCount = 0;
	private $countActionsLoaded = false;
	
	/**
	 * Tabulka s uživateli
	 * @var string
	 */
	private $tableUsers = null;
	
	/**
	 * Metoda vrací počet novinek
	 *
	 * @return integer -- počet novinek
	 */
	public function getCountActions($all = false) {
		if(!$this->countActionsLoaded){
         $sqlCount = $this->getDb()->select()->table($this->module()->getDbTable())
         ->colums(array("count"=>"COUNT(*)"))
         ->where(Actions_Model_Detail::COLUMN_ACTION_ID_ITEM, $this->module()->getId())
			->where(Actions_Model_Detail::COLUMN_ACTION_DISABLED, (int)false)
         ->where(Actions_Model_Detail::COLUMN_ACTION_DATE_START, time(), "<=")
         ->where(Actions_Model_Detail::COLUMN_ACTION_DATE_STOP, time(), ">=");;

			$count = $this->getDb()->fetchObject($sqlCount);
         if(!empty ($count)){
            $this->allActionsCount = $count->count;
            $this->countActionsLoaded = true;
         }
		}
		return $this->allActionsCount;
	}
	
	
	/**
	 * Metoda vrací pole s vybranými akcemi
	 * 
	 * @return array -- pole akcí
	 */
   public function getSelectedListActions($from, $count=5, $all = false) {
      $sqlSelect = $this->getDb()->select()->table($this->module()->getDbTable(), 'actions')
      ->colums(array(Actions_Model_Detail::COLUMN_ACTION_LABEL => "IFNULL(".Actions_Model_Detail::COLUMN_ACTION_LABEL
            .'_'.Locale::getLang().", ".Actions_Model_Detail::COLUMN_ACTION_LABEL.'_'.Locale::getDefaultLang().")",
            Actions_Model_Detail::COLUMN_ACTION_TEXT_SHORT => "IFNULL(".Actions_Model_Detail::COLUMN_ACTION_TEXT_SHORT
            .'_'.Locale::getLang().", ".Actions_Model_Detail::COLUMN_ACTION_TEXT_SHORT.'_'.Locale::getDefaultLang().")",
            Actions_Model_Detail::COLUMN_ACTION_TEXT => "IFNULL(".Actions_Model_Detail::COLUMN_ACTION_TEXT.'_'.Locale::getLang()
            .", ".Actions_Model_Detail::COLUMN_ACTION_TEXT.'_'.Locale::getDefaultLang().")",
            Actions_Model_Detail::COLUMN_ACTION_ID_USER, Actions_Model_Detail::COLUMN_ACTION_ID,
            Actions_Model_Detail::COLUMN_ACTION_TIME, Actions_Model_Detail::COLUMN_ACTION_DATE_START,
            Actions_Model_Detail::COLUMN_ACTION_DATE_STOP, Db::COLUMN_ALL))
      ->where("actions.".Actions_Model_Detail::COLUMN_ACTION_ID_ITEM, $this->module()->getId())
//      ->where("actions.".Actions_Model_Detail::COLUMN_ACTION_DISABLED, (int)false)
      ->limit($from, $count)
//      ->order("actions.".Actions_Model_Detail::COLUMN_ACTION_TIME, Db::ORDER_DESC)
      ;
      if(!$all){
         $sqlSelect->where("actions.".Actions_Model_Detail::COLUMN_ACTION_DATE_START, time(), "<=")
         ->where("actions.".Actions_Model_Detail::COLUMN_ACTION_DATE_STOP, time(), ">=");
      }
//      echo $sqlSelect;

		$returArray = $this->getDb()->fetchAll($sqlSelect);
		return $returArray;
	}

	/**
	 * Metoda vrací pole se všemi akcemi
	 *
	 * @return array -- pole akcí
	 */
	public function getListActions() {
      $sqlSelect = $this->getDb()->select()->table($this->module()->getDbTable(), 'action')
      ->colums(array(Actions_Model_Detail::COLUMN_ACTION_LABEL => "IFNULL(".Actions_Model_Detail::COLUMN_ACTION_LABEL
            .'_'.Locale::getLang().", ".Actions_Model_Detail::COLUMN_ACTION_LABEL.'_'.Locale::getDefaultLang().")",
            Actions_Model_Detail::COLUMN_ACTION_TEXT_SHORT => "IFNULL(".Actions_Model_Detail::COLUMN_ACTION_TEXT_SHORT
            .'_'.Locale::getLang().", ".Actions_Model_Detail::COLUMN_ACTION_TEXT_SHORT.'_'.Locale::getDefaultLang().")",
            Actions_Model_Detail::COLUMN_ACTION_TEXT => "IFNULL(".Actions_Model_Detail::COLUMN_ACTION_TEXT
            .'_'.Locale::getLang().", ".Actions_Model_Detail::COLUMN_ACTION_TEXT.'_'.Locale::getDefaultLang().")",
            Actions_Model_Detail::COLUMN_ACTION_ID, Actions_Model_Detail::COLUMN_ACTION_TIME))
      ->where("action.".Actions_Model_Detail::COLUMN_ACTION_ID_ITEM, $this->module()->getId())
//      ->where("action.".Actions_Model_Detail::COLUMN_ACTION_DELETED, (int)false)
      ->where("action.".Actions_Model_Detail::COLUMN_ACTION_DATE_START, time(), "<=")
      ->where("action.".Actions_Model_Detail::COLUMN_ACTION_DATE_STOP, time(), ">=")
      ->order("action.".Actions_Model_Detail::COLUMN_ACTION_TIME, Db::ORDER_DESC);

//		if($this->tableUsers != null){
//			$sqlSelect=$sqlSelect->join(array("users" => $tableUsers), "users.".self::COLUMN_ACTION_ID_USER." = news.".self::COLUMN_ACTION_ID_USER, null, Auth::USER_NAME);
//		}

		$returArray = $this->getDb()->fetchAll($sqlSelect);

		return $returArray;
	}
	
   public function getLastChange() {
      $sqlSelect = $this->getDb()->select()
      ->table($this->module()->getDbTable())
      ->colums(Actions_Model_Detail::COLUMN_ACTION_TIME)
      ->limit(0, 1)
      ->order(Actions_Model_Detail::COLUMN_ACTION_TIME, Db::ORDER_DESC);

      $returArray = $this->getDb()->fetchObject($sqlSelect);

      if(!empty ($returArray)){
         $returArray = $returArray->{Actions_Model_Detail::COLUMN_ACTION_TIME};
      }
		return $returArray;
   }
	
}

?>