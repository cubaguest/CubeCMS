<?php
/**
 * EPlugin pro sledování udělaných změn na internetové stránce
 * @version 0.0.1
 */

class Changes extends Eplugin {
	/**
	 * Název primární šablony s posunovátky
	 * @var string
	 */
	protected $templateFile = 'changes.tpl';

	/**
	 * Název databázové tabulky se změnama
	 * @var string
	 */
	const DB_TABLE_CHANGES = 'changes';
	
	/**
	 * Počet posledních změn zobrazených ve výstupu
	 * @var integer
	 */
	const COUNT_OF_LAST_CHANGES = 1;
	
	/**
	 * Názvy sloupců v db
	 * @var string
	 */
	const COLUM_ID				= 'id_change';
	const COLUM_ID_USER			= 'id_user';
	const COLUM_ID_ITEM			= 'id_item';
	const COLUM_ID_ARTICLE		= 'id_article';
	const COLUM_USER_NAME		= 'name';
	const COLUM_USER_SURNAME	= 'surname';
	const COLUM_USER_USERNAME	= 'username';
	const COLUM_LABEL			= 'label';
	const COLUM_TIME			= 'time';
	
	/**
	 * Název volby s názvem tabulky uživatelů
	 * @var string
	 */
	const CONFIG_TABLE_USERS = 'users_table';
	
	/**
	 * Sekce v configu s informacemi o tabulkách
	 * @var string
	 */
	const CONFIG_TABLES_SECTIONS = 'db_tables';
	
	/**
	 * Proměnná s id článku, u kterého se zobrazí změny
	 * @var integer/array
	 */
	private $articleId = null;
	
	/**
	 * Pole se změnami
	 * @var array
	 */
	private $changesArray = array();
	
	/**
	 * Pole s popisky
	 * @var array
	 */
	private $labelsArray = array();
	
	/**
	 * Metoda inicializace, je spuštěna pří vytvoření objektu  
	 *
	 */
	protected function init()
	{
		
	}
	
	/**
	 * Metoda vrací objekt změny s načtenými změnami u zadaných id článků
	 * @param mixed -- array nebo integer s id článku
	 */
	public function getChanges($idArticles = null) {
		$this->articleId = $idArticles;
		
		$this->getDataFromDb();
		
//		return $this->changesArray;
		return $this;
	}
	
	/**
	 * Metoda uloží změnu do db
	 * @param string -- popis změny
	 * @param integer -- id článku u kterého byla změna provedena
	 * @param integer -- (option) id item u ktré byla změna provedena
	 */
	public function createChange($label, $idArticle, $idItem = null) {
		$sqlInser = $this->getDb()->insert()->into(self::DB_TABLE_CHANGES);
		
		if($idItem == null){
			$sqlInser = $sqlInser->colums(self::COLUM_ID_ARTICLE, self::COLUM_ID_ITEM, self::COLUM_ID_USER, self::COLUM_LABEL, self::COLUM_TIME)
								->values($idArticle, $this->getModule()->getId(), $this->getRights()->getAuth()->getUserId(), $label, time());
		
		} else {
			$sqlInser = $sqlInser->colums(self::COLUM_ID_ARTICLE, self::COLUM_ID_ITEM, self::COLUM_ID_USER, self::COLUM_LABEL, self::COLUM_TIME)
								->values($idArticle, $idItem, $this->getRights()->getAuth()->getUserId(), $label, time());
		}
		
//		vložení záznamu
		$this->getDb()->query($sqlInser);
	}
	
	/**
	 * Metoda načte data z db
	 */
	private function getDataFromDb() {
		$sqlSelect = $this->getDb()->select()->from(array("changes"=>self::DB_TABLE_CHANGES), array("time", "label"))
											 ->join(array("users"=>$this->getSysConfig()->getOptionValue(self::CONFIG_TABLE_USERS, self::CONFIG_TABLES_SECTIONS)),
											 		"changes.".self::COLUM_ID_USER." = users.".self::COLUM_ID_USER, null,
											 array(self::COLUM_USER_NAME, self::COLUM_USER_SURNAME, self::COLUM_USER_USERNAME));
		
		if(!is_array($this->articleId) AND $this->articleId != null){
			$sqlSelect = $sqlSelect->where(self::COLUM_ID_ARTICLE." = ".$this->articleId);
		} else if(is_array($this->articleId) AND $this->articleId != null){
			$whereString = null;
			foreach ($this->articleId as $idArticle) {
				$whereString.= self::COLUM_ID_ARTICLE." = ".$idArticle." OR ";
			}
			$whereString = substr($whereString, 0, strlen($whereString)-4);
			$sqlSelect = $sqlSelect->where($whereString);
		} else {
			
		}
		
		$sqlSelect = $sqlSelect->where("changes.".self::COLUM_ID_ITEM." = ".$this->getModule()->getId())
							   ->order(self::COLUM_TIME, "DESC");
		
//		echo $sqlSelect;
		
		$this->changesArray = $this->getDb()->fetchAssoc($sqlSelect);
		
//		print_r($this->changesArray);
	}
	
//	/**
//	 * Metoda nastavuje id článku
//	 * @param integer -- id článku
//	 */
//	public function setIdArticle($idArticle) {
//		$this->articleId = $idArticle;
//	}
	
	
	/**
	 * Metoda obstarává přiřazení proměných do šablony
	 *
	 */
	protected function assignTpl(){
		$this->toTpl("CHANGES_NAME", _("Provedené změny"));

		$this->toTpl("CHANGES_ARRAY", $this->changesArray);
		
		$this->toTplJSPlugin(new SwitchContentEasy());

		
	}

}
?>