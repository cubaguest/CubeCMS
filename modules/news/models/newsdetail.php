<?php
/*
 * Třída modelu s listem Novinek
 */
class NewsDetailModel extends DbModel {
	/**
	 * Názvy sloupců v databázi
	 * @var string
	 */
	const COLUMN_NEWS_LABEL = 'label';
	const COLUMN_NEWS_LABEL_LANG_PREFIX = 'label_';
	const COLUMN_NEWS_TEXT = 'text';
	const COLUMN_NEWS_TEXT_LANG_PREFIX = 'text_';
	const COLUMN_NEWS_URLKEY = 'urlkey';
	const COLUMN_NEWS_TIME = 'time';
	const COLUMN_NEWS_ID_USER = 'id_user';
	const COLUMN_NEWS_ID_ITEM = 'id_item';
	const COLUMN_NEWS_ID_NEW = 'id_new';
	const COLUMN_NEWS_DELETED = 'deleted';
	
	/**
	 * Sloupce u tabulky uživatelů
	 * @var string
	 */
	const COLUMN_USER_NAME = 'username';
	const COLUMN_ISER_ID =	 'id_user';	
	
	/**
	 * Speciální imaginární sloupce
	 * @var string
	 */
//	const COLUMN_NEWS_LANG = 'lang';
//	const COLUMN_NEWS_EDITABLE = 'editable';
//	const COLUMN_NEWS_EDIT_LINK = 'editlink';	
	
	
	/**
	 * Metoda uloží novinku do db
	 *
	 * @param array -- pole s texty novinky
	 * @param boolean -- jestli se ukládá nová novinky nebo upravuje uložená
	 */
	public function saveNewNews($newsArray, $mainLabel, $idUser = 0) {
//			vygenerování url klíče	
			$dbHelper = new DbCtrlHelper();
			$urlKey = $dbHelper->generateDatabaseUrlKey($mainLabel, $this->getModule()->getDbTable(), self::COLUMN_NEWS_URLKEY);
			
			//Vygenerování sloupců do kterých se bude zapisovat
			$columsArray = array_keys($newsArray);
			$valuesArray = array_values($newsArray);
			array_push($columsArray, self::COLUMN_NEWS_URLKEY);
			array_push($valuesArray, $urlKey);
			array_push($columsArray, self::COLUMN_NEWS_ID_ITEM);
			array_push($valuesArray, $this->getModule()->getId());
			array_push($columsArray, self::COLUMN_NEWS_TIME);
			array_push($valuesArray, time());
			array_push($columsArray, self::COLUMN_NEWS_ID_USER);
			array_push($valuesArray, $idUser);

            

			$sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable())
					->colums($columsArray)
					->values($valuesArray);
				
		//		Vložení do db
		if($this->getDb()->query($sqlInsert)){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Metoda vrací novinku podle zadaného url klíče a v aktuálním jazyku
	 *
	 * @param string -- url klíč
	 * @return array -- pole s novinkou
	 */
	public function getNewsDetailByUrlkeySelLang($urlkey) {
		//		načtení novinky z db
		$sqlSelect = $this->getDb()->select()->from(array('news' => $this->getModule()->getDbTable()), array(self::COLUMN_NEWS_LABEL =>"IFNULL(".self::COLUMN_NEWS_LABEL_LANG_PREFIX.Locale::getLang().",".self::COLUMN_NEWS_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
							self::COLUMN_NEWS_TEXT =>"IFNULL(".self::COLUMN_NEWS_TEXT_LANG_PREFIX.Locale::getLang().",".self::COLUMN_NEWS_TEXT_LANG_PREFIX.Locale::getDefaultLang().")",
							self::COLUMN_NEWS_TIME, self::COLUMN_NEWS_URLKEY, self::COLUMN_NEWS_ID_NEW, self::COLUMN_NEWS_ID_USER))
									 ->join(array('user' => $this->getUserTable()), 'news.'.self::COLUMN_NEWS_ID_USER.' = user.'.self::COLUMN_ISER_ID, null, self::COLUMN_USER_NAME)
									 ->where('news.'.self::COLUMN_NEWS_ID_ITEM." = ".$this->getModule()->getId())
									 ->where('news.'.self::COLUMN_NEWS_URLKEY." = '".$urlkey."'")
									 ->where('news.'.self::COLUMN_NEWS_DELETED.' = '.(int)false);
									 
		$news = $this->getDb()->fetchAssoc($sqlSelect, true);

		return $news;
	}
	
	/**
	 * Metoda vrací novinku podle zadaného url klíče ve všech jazycích
	 *
	 * @param string -- url klíč
	 * @return array -- pole s novinkou
	 */
	public function getNewsDetailByUrlkey($urlkey) {
		//		načtení novinky z db
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
									 ->where(self::COLUMN_NEWS_ID_ITEM." = ".$this->getModule()->getId())
									 ->where(self::COLUMN_NEWS_URLKEY." = '".$urlkey."'")
									 ->where(self::COLUMN_NEWS_DELETED.' = '.(int)false);
									 
		$news = $this->getDb()->fetchAssoc($sqlSelect, true);

		return $news;
	}
	
	/**
	 * Metoda uloží upravenou ovinku do db
	 *
	 * @param array -- pole s detaily novinky
	 */
	public function saveEditNews($newsArray, $urlkey, $idNews = null) {
		//TODO dodělat generování nového url klíče

		$sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable())
											 ->set($newsArray)
											 ->where(self::COLUMN_NEWS_URLKEY." = '".$urlkey."'");

		if($idNews != null){
			$sqlInsert = $sqlInsert->where(self::COLUMN_NEWS_ID_NEW." = ".$idNews);
		}

		// vložení do db
		if($this->getDb()->query($sqlInsert)){
			return true;
		} else {
			return false;
		};
	}
	
	public function deleteNews($idNews) {
		//			smazání novinky
		$sqlUpdate = $this->getDb()->update()->table($this->getModule()->getDbTable())
							->set(array(self::COLUMN_NEWS_DELETED => (int)true))
							->where(self::COLUMN_NEWS_ID_NEW." = ".$idNews);
			
		if($this->getDb()->query($sqlUpdate)){
			return true;
		} else {
			return false;
		};
	}
	
	
	private function getUserTable() {
		$tableUsers = AppCore::sysConfig()->getOptionValue(Auth::CONFIG_USERS_TABLE_NAME, Config::SECTION_DB_TABLES);
		
		return $tableUsers;
	}
	
}

?>