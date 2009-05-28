<?php
/*
 * Třída modelu s listem Novinek
 */
class ActionsListModel extends DbModel {
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
	public function getCountActions() {
		if(!$this->countActionsLoaded){
         $sqlCount = $this->getDb()->select()->table($this->getModule()->getDbTable())
         ->colums(array("count"=>"COUNT(*)"))
         ->where(ActionDetailModel::COLUMN_ACTION_ID_ITEM, $this->getModule()->getId())
			->where(ActionDetailModel::COLUMN_ACTION_DISABLED, (int)false);

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
   public function getSelectedListActions($from, $count=5) {
      $sqlSelect = $this->getDb()->select()->table($this->getModule()->getDbTable(), 'actions')
      ->colums(array(ActionDetailModel::COLUMN_ACTION_LABEL => "IFNULL(".ActionDetailModel::COLUMN_ACTION_LABEL.'_'.Locale::getLang().", ".ActionDetailModel::COLUMN_ACTION_LABEL.'_'.Locale::getDefaultLang().")",
            ActionDetailModel::COLUMN_ACTION_TEXT => "IFNULL(".ActionDetailModel::COLUMN_ACTION_TEXT.'_'.Locale::getLang()
            .", ".ActionDetailModel::COLUMN_ACTION_TEXT.'_'.Locale::getDefaultLang().")",
            ActionDetailModel::COLUMN_ACTION_ID_USER, ActionDetailModel::COLUMN_ACTION_ID, 
            ActionDetailModel::COLUMN_ACTION_TIME, ActionDetailModel::COLUMN_ACTION_DATE_START,
            ActionDetailModel::COLUMN_ACTION_DATE_STOP, Db::COLUMN_ALL))
      ->where("actions.".ActionDetailModel::COLUMN_ACTION_ID_ITEM, $this->getModule()->getId())
      ->where("actions.".ActionDetailModel::COLUMN_ACTION_DISABLED, (int)false)
      ->limit($from, $count)
//      ->order("actions.".ActionDetailModel::COLUMN_ACTION_TIME, Db::ORDER_DESC)
      ;

		$returArray = $this->getDb()->fetchAll($sqlSelect);
		return $returArray;
	}

	/**
	 * Metoda vrací pole s vybranými novinkami
	 *
	 * @return array -- pole novinek
	 */
	public function getListNews() {
      $sqlSelect = $this->getDb()->select()->table($this->getModule()->getDbTable(), 'news')
      ->colums(array(self::COLUMN_ACTION_LABEL => "IFNULL(".self::COLUMN_ACTION_LABEL.'_'.Locale::getLang().", ".self::COLUMN_ACTION_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_ACTION_TEXT => "IFNULL(".self::COLUMN_ACTION_TEXT.'_'.Locale::getLang().", ".self::COLUMN_ACTION_TEXT.'_'.Locale::getDefaultLang().")",
            self::COLUMN_ACTION_ID_USER, self::COLUMN_ACTION_ID_NEW, self::COLUMN_ACTION_TIME))
      ->where("news.".self::COLUMN_ACTION_ID_ITEM, $this->getModule()->getId())
      ->where("news.".self::COLUMN_ACTION_DELETED, (int)false)
      ->order("news.".self::COLUMN_ACTION_TIME, Db::ORDER_DESC);

//		if($this->tableUsers != null){
//			$sqlSelect=$sqlSelect->join(array("users" => $tableUsers), "users.".self::COLUMN_ACTION_ID_USER." = news.".self::COLUMN_ACTION_ID_USER, null, Auth::USER_NAME);
//		}

		$returArray = $this->getDb()->fetchAll($sqlSelect);

		return $returArray;
	}
	
	/**
	 * Metoda nastaví tabulku s uživateli
	 *
	 * @param string -- název tanulky s uživateli
	 */
	public function setTableUsers($tableUsers) {
		$this->tableUsers = $tableUsers;
	}
	
	
	private function getUserTable() {
		$tableUsers = AppCore::sysConfig()->getOptionValue(Auth::CONFIG_USERS_TABLE_NAME, Config::SECTION_DB_TABLES);
		
		return $tableUsers;
	}

   public function getLastChange() {
      $sqlSelect = $this->getDb()->select()
      ->table($this->getModule()->getDbTable())
      ->colums(self::COLUMN_ACTION_TIME)
      ->limit(0, 1)
      ->order(self::COLUMN_ACTION_TIME, Db::ORDER_DESC);

      $returArray = $this->getDb()->fetchObject($sqlSelect);

      if(!empty ($returArray)){
         $returArray = $returArray->{self::COLUMN_ACTION_TIME};
      }
		return $returArray;
   }
	
}

?>