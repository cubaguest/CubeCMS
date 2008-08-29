<?php
/**
 * EPlugin pro sledování udělaných změn na internetové stránce
 * 
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	Changes class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: changes.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída Epluginu pro zobrazování a logování provedených změn
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
	 * Maximální počet záznamů vytažených z db
	 * @var integer
	 */
	const MAX_NUMBER_OF_CHANGES = 30;
	
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
	static $otherChanges = array();
	
	/**
	 * Pole s popisky
	 * @var array
	 */
	private $labelsArray = array();
	
	/**
	 * Pole s id modulu (items)
	 * @var array
	 */
	private $idItems = null;
	
	/**
	 * ID změny v šabloně
	 * @var ineger
	 */
	private $idChanges = '1';
	
	/**
	 * Počet vrácených záznamů
	 * @var integer
	 */
	private $numberOfReturnRows = 0;
	static $otherNumberOfReturnRows = array();
	
	/**
	 * Metoda inicializace, je spuštěna pří vytvoření objektu  
	 *
	 */
	protected function init(){
		
	}
	
	/**
	 * Metoda nastaví id šablony pro výpis
	 * @param ineger -- id šablony (jakékoliv)
	 */
	public function setIdTpl($id) {
		$this->idChanges = $id;
	}
	
	
	/**
	 * Metoda vrací objekt změny s načtenými změnami u zadaných id článků
	 * @param mixed -- array nebo integer s id článku (popřípadě podpole s id item a id článků)
	 * @return Changes -- vrací objekt Changes (tedy sebe)
	 */
	public function getChanges($idArticles = null, $idItems = null) {
		$this->articleId = $idArticles;
		$this->idItems = $idItems;
		
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
											 ->limit(0,self::MAX_NUMBER_OF_CHANGES)
											 ->join(array("users"=>$this->getSysConfig()->getOptionValue(self::CONFIG_TABLE_USERS, self::CONFIG_TABLES_SECTIONS)),
											 		"changes.".self::COLUM_ID_USER." = users.".self::COLUM_ID_USER, null,
											 array(self::COLUM_USER_NAME, self::COLUM_USER_SURNAME, self::COLUM_USER_USERNAME));
		
//		echo "<pre>";
//		print_r($this->articleId);									 
//		echo "</pre>";									 
											 
		if(is_string($this->articleId) OR is_numeric($this->articleId)){
			$sqlSelect = $sqlSelect->where(self::COLUM_ID_ARTICLE." = ".$this->articleId)
								   ->where(self::COLUM_ID_ITEM." = ".$this->getModule()->getId());
		} else if(is_array($this->articleId) AND !empty($this->articleId)){
			foreach ($this->articleId as $id => $itemId){
				//Pokud je zadáno asociativní pole bez id items
				if(is_string($itemId) OR is_numeric($itemId)){
//					$whereString = null;
//					$whereString.= self::COLUM_ID_ARTICLE." = ".$itemId." AND ".self::COLUM_ID_ITEM." = ".$this->getModule()->getId();
//					$whereString = substr($whereString, 0, strlen($whereString)-4);
//					$sqlSelect = $sqlSelect->where($whereString);
//					if(!empty($this->idItems)){
						$sqlSelect = $sqlSelect->where(self::COLUM_ID_ARTICLE." = ".$itemId." AND ".self::COLUM_ID_ITEM." = ".$this->getModule()->getId(), "OR");
//					} else {
//						$sqlSelect = $sqlSelect->where(self::COLUM_ID_ARTICLE." = ".$itemId." AND ".self::COLUM_ID_ITEM." = ".$this->idItems, "OR");
//					}
				} else if(is_array($itemId) AND !empty($itemId)){
					$whereString = self::COLUM_ID_ITEM." = ".$id." AND (";
					foreach ($itemId as $idArticle) {
						$whereString.= self::COLUM_ID_ARTICLE." = ".$idArticle." OR ";
					}
					$whereString = substr($whereString, 0, strlen($whereString)-4).")";
					$sqlSelect = $sqlSelect->where($whereString, "OR");
				} else if($itemId == null){
					$sqlSelect = $sqlSelect->where(self::COLUM_ID_ITEM." = ".$id, "OR");
				}
			}
					
		} else if (empty($this->articleId)){
			$sqlSelect = $sqlSelect->where(self::COLUM_ID_ITEM." = ".$this->getModule()->getId());
		}
											 
		$sqlSelect = $sqlSelect->order(self::COLUM_TIME, "DESC");
		
//		echo $sqlSelect;
		
		$this->changesArray = $this->getDb()->fetchAssoc($sqlSelect);
		$this->numberOfReturnRows = $this->getDb()->getNumRows();
		
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
		$this->toTpl("CHANGES_LABEL_NAME", _("Provedené změny"));

		self::$otherNumberOfReturnRows[$this->idChanges] = $this->numberOfReturnRows;
		$this->toTpl("CHANGES_NUM_ROWS", self::$otherNumberOfReturnRows);
		
		$this->toTpl("CHANGES_ID", 1);
		
//		if(!empty(self::$otherChanges)){
//			$array = self::$otherChanges;
//		}
		
		self::$otherChanges[$this->idChanges] = $this->changesArray;
		$this->toTpl("CHANGES_ARRAY",self::$otherChanges);
		
		$this->toTplJSPlugin(new SwitchContentEasy());

		
	}

}
?>