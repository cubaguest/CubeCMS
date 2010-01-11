<?php
/*
 * Třída modelu s listem akcí
 */
class Actions_Model_List extends Model_PDO {
	/**
	 * Metoda vrací počet novinek
	 *
	 * @return integer -- počet novinek
	 */
	public function getCountActions() {
		if(!$this->countActionsLoaded){
         $sqlCount = $this->getDb()->select()->table(Db::table(Actions_Model_Detail::DB_TABLE))
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
	 * Metoda vrací vybrané akce podle času
	 * @return PDOStatement -- pole akcí
	 */
   public function getActions($idCat, $fromTime, $toTime, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $whereP = " AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)";
      } else {
         $whereP = null;
      }
//         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)
//                 ." WHERE (".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
//                 .$whereP
//                 ." ORDER BY ".Actions_Model_Detail::COLUMN_DATE_START." ASC");

         $dbst = $dbc->prepare("SELECT actions.*, user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)." AS actions"
              ." JOIN ".Model_Users::getUsersTable()." AS user ON actions.".Actions_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." WHERE (actions.".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
              . $whereP // public
              ." AND (".Actions_Model_Detail::COLUMN_DATE_START." < :dateE)"
              ." AND (".Actions_Model_Detail::COLUMN_DATE_STOP." > :dateS)"
              ." ORDER BY ".Actions_Model_Detail::COLUMN_DATE_START." ASC");

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':dateS', (int)$fromTime, PDO::PARAM_INT);
      $dbst->bindValue(':dateE', (int)$toTime, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
	}

	/**
	 * Metoda vrací aktuální akci
	 * @return PDOStatement -- pole akcí
	 */
   public function getCurrentActions($idCat, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)
                 ." WHERE (".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
                 ." AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)"
                 ." AND (".Actions_Model_Detail::COLUMN_DATE_START." < :date)"
                 ." AND (".Actions_Model_Detail::COLUMN_DATE_STOP." > :date)"
                 ." ORDER BY ".Actions_Model_Detail::COLUMN_DATE_START." ASC");
      } else {
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)
                 ." WHERE (".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
                 ." AND (".Actions_Model_Detail::COLUMN_DATE_START." < :date)"
                 ." AND (".Actions_Model_Detail::COLUMN_DATE_STOP." > :date)"
                 ." ORDER BY ".Actions_Model_Detail::COLUMN_DATE_START." ASC");
      }
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':date', time(), PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
	}

	/**
	 * Metoda vrací všechny akce
    * @param int $idCat -- id kategorie
    * @param bool $onlyPublic -- (option) jestli jenom veřejné akce
	 * @return array -- pole akcí
	 */
	public function getAllActions($idCat, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $wherePub = " AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)";
      } else {
         $wherePub = null;
      }
         
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)
                 ." WHERE (".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
                 . $wherePub
                 ." ORDER BY ".Actions_Model_Detail::COLUMN_DATE_START." DESC");
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
	}
	
   public function getLastChange() {
   }
	
}

?>