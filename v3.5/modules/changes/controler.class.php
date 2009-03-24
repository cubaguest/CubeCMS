<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class ChangesController extends Controller {
	/**
	 * Název $_GET kterým se přenáší řazení
	 * @var string
	 */
	const ORDER_SGET_NAME = 'order';
	
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUM_ID 				= 'id_parent';
	const COLUM_LABEL 			= 'label';
	const COLUM_TIME 			= 'time';
	const COLUM_ID_USER			= 'id_user';
	const COLUM_ID_ITEM			= 'id_item';
	const COLUM_ID_ARTICLE		= 'id_article';
	const COLUM_USER_NAME		= 'name';
	const COLUM_USER_SURNAME	= 'surname';
	const COLUM_USER_USERNAME	= 'username';

	/**
	 * Název SESSION s hledáním
	 * @var string
	 */
	const SEARCH_SESSION_NAME = 'search';
	const SEARCH_SESSION_IDENT = 'changes';

	/**
	 * Názvy session s jednotlivými prvky hledání
	 * @var string
	 */
	const SEARCH_LABEL 		= 'label';
	const SEARCH_USERNAME 	= 'username';
	const SEARCH_NAME 		= 'name';
	const SEARCH_SEND 		= 'send';
	
	/**
	 * Prefix vyhledávacích polí
	 * @var string
	 */
	const SEARCH_PREFIX = 'changes_search_';

	/**
	 * Url separtor zástupce/id
	 * @var string
	 */
	const DETAIL_ARTICLE_ID_SEPARATOR = '-';
	
	/**
	 * Pole s typy řezení
	 * @var array
	 */
	private $ordersArray = array("time_desc" => "timedesc", "time_asc" => "timeasc",
								 "name_desc" => "namedesc", "name_asc"=>"nameasc",
								 "label_desc" => "labeldesc", "label_asc"=>"labelasc",
								 "username_desc" => "usernamedesc", "username_asc" => "usernameasc");
	
	/**
	 * Pole s hledanými hodnotami
	 * @var array
	 */
	private $searchArray = array();
	
	
	public function mainController() {
		$this->createModel("ChangesList");
		if(isset($_SESSION[self::SEARCH_SESSION_NAME]) AND isset( $_SESSION[self::SEARCH_SESSION_NAME][self::SEARCH_SESSION_IDENT]) AND $_SESSION[self::SEARCH_SESSION_NAME][self::SEARCH_SESSION_IDENT] != self::SEARCH_SESSION_IDENT){
			unset($_SESSION[self::SEARCH_SESSION_NAME]);
		}

//		Základní select
		$sqlSelect = $this->getDb()->select();
											 
		
		//Řazení prvků
		isset($_GET[self::ORDER_SGET_NAME]) ? $order = htmlspecialchars($_GET[self::ORDER_SGET_NAME]) : $order = null;
		
		switch ($order) {
			case $this->ordersArray["time_asc"]:
				$sqlSelect = $sqlSelect->order(self::COLUM_TIME, "ASC");
				break;
			case $this->ordersArray["name_desc"]:
				$sqlSelect = $sqlSelect->order(self::COLUM_NAME, "DESC");
				break;
			case $this->ordersArray["name_asc"]:
				$sqlSelect = $sqlSelect->order(self::COLUM_NAME, "ASC");
				break;
			case $this->ordersArray["label_desc"]:
				$sqlSelect = $sqlSelect->order(self::COLUM_LABEL, "DESC");
				break;
			case $this->ordersArray["label_asc"]:
				$sqlSelect = $sqlSelect->order(self::COLUM_LABEL, "ASC");
				break;
			case $this->ordersArray["username_desc"]:
				$sqlSelect = $sqlSelect->order(self::COLUM_SURNAME, "DESC");
				break;
			case $this->ordersArray["username_asc"]:
				$sqlSelect = $sqlSelect->order(self::COLUM_SURNAME, "ASC");
				break;
			default:
				$sqlSelect = $sqlSelect->order(self::COLUM_TIME, "DESC");
				break;
		}
		
		//Vyhledávání
		if(isset($_POST[self::SEARCH_PREFIX.self::SEARCH_SEND])){
			if(!isset($_SESSION[self::SEARCH_SESSION_NAME])){
				$_SESSION[self::SEARCH_SESSION_NAME] = array();
			}
			
			if($_POST[self::SEARCH_PREFIX.self::SEARCH_LABEL] == null AND $_POST[self::SEARCH_PREFIX.self::SEARCH_NAME] == null 
				AND $_POST[self::SEARCH_PREFIX.self::SEARCH_USERNAME] == null){
				unset($_SESSION[self::SEARCH_SESSION_NAME]);
			}
			
			$_POST[self::SEARCH_PREFIX.self::SEARCH_NAME] != null ?	$this->searchArray[self::SEARCH_NAME] = htmlspecialchars($_POST[self::SEARCH_PREFIX.self::SEARCH_NAME], ENT_QUOTES) : null;
			$_POST[self::SEARCH_PREFIX.self::SEARCH_USERNAME]!= null ?	$this->searchArray[self::SEARCH_USERNAME] = htmlspecialchars($_POST[self::SEARCH_PREFIX.self::SEARCH_USERNAME], ENT_QUOTES) : null;
			$_POST[self::SEARCH_PREFIX.self::SEARCH_LABEL]!= null ?	$this->searchArray[self::SEARCH_LABEL] = htmlspecialchars($_POST[self::SEARCH_PREFIX.self::SEARCH_LABEL], ENT_QUOTES) : null;
			
			$_SESSION[self::SEARCH_SESSION_NAME]=$this->searchArray;
		}
											 
		if(isset($_SESSION[self::SEARCH_SESSION_NAME])){
			$this->searchArray = $_SESSION[self::SEARCH_SESSION_NAME];
			
			//hledání jména nebo přijmení
			if(isset($this->searchArray[self::SEARCH_NAME])){
//				->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
				$sqlSelect = $sqlSelect->where(self::COLUM_USER_NAME.' LIKE \'%'.$this->searchArray[self::SEARCH_NAME].'%\' OR '.self::COLUM_USER_SURNAME.' LIKE \'%'.$this->searchArray[self::SEARCH_NAME].'%\'');
			}
			//hledání uživ jména
			if(isset($this->searchArray[self::SEARCH_USERNAME])){
				$sqlSelect = $sqlSelect->where(self::COLUM_USER_SURNAME.' LIKE \'%'.$this->searchArray[self::SEARCH_USERNAME].'%\'');
			}
			//hledání popisu
			if(isset($this->searchArray[self::SEARCH_LABEL])){
				$sqlSelect = $sqlSelect->where("changes.".self::COLUM_LABEL.' LIKE \'%'.$this->searchArray[self::SEARCH_LABEL].'%\'');
			}
		
		}


		//		Scrolovátka
		$scroll = $this->eplugin()->scroll();
		$scroll->setCountRecordsOnPage($this->getModule()->getRecordsOnPage());
		$sqlCount = clone $sqlSelect;
		$count = $this->getDb()->fetchAssoc($sqlCount->from(array("changes"=>$this->getModule()->getDbTable()), array("count"=>"COUNT(*)"))
					  ->join(array("user"=>$this->getModule()->getDbTable(2)), "user.".self::COLUM_ID_USER." = changes.".self::COLUM_ID_USER), true);
		$scroll->setCountAllRecords($count["count"]);
		
		//Doplnění pole s novinkami do modelu
		$this->getModel()->scroll = $scroll;
		
		
//		Základní select
		$sqlSelect = $sqlSelect->from(array("changes"=>$this->getModule()->getDbTable()))
							   ->join(array("user"=>$this->getModule()->getDbTable(2)), "user.".self::COLUM_ID_USER." = changes.".self::COLUM_ID_USER)
							   ->limit($scroll->getStartRecord(), $scroll->getCountRecords());
		
//		echo $sqlSelect;
		$changesArray = $this->getDb()->fetchAssoc($sqlSelect);
		
		$this->getModel()->allChangesArray=$changesArray;
			
		$this->getModel()->changeSearchArray=$this->searchArray;
		

		
		//linky pro nastavení řazení
		$this->getModel()->changesTableOrder["time_desc"] = $this->getLink()->param("order", $this->ordersArray["time_desc"]);
		$this->getModel()->changesTableOrder["time_asc"] = $this->getLink()->param("order", $this->ordersArray["time_asc"]);
		$this->getModel()->changesTableOrder["name_desc"] = $this->getLink()->param("order", $this->ordersArray["name_desc"]);
		$this->getModel()->changesTableOrder["name_asc"] = $this->getLink()->param("order", $this->ordersArray["name_asc"]);
		$this->getModel()->changesTableOrder["username_desc"] = $this->getLink()->param("order", $this->ordersArray["username_desc"]);
		$this->getModel()->changesTableOrder["username_asc"] = $this->getLink()->param("order", $this->ordersArray["username_asc"]);
		$this->getModel()->changesTableOrder["label_desc"] = $this->getLink()->param("order", $this->ordersArray["label_desc"]);
		$this->getModel()->changesTableOrder["label_asc"] = $this->getLink()->param("order", $this->ordersArray["label_asc"]);
			
//		Načtení provedených změn
//		$this->getModel()->changes = $this->eplugin()->changes()->getChanges();
	}
	
	/**
	 * Metoda pro zobrazení detailu zástupce
	 */
	public function showController() {
		
	}
	
	/**
	 * Metoda pro úpravu
	 */
	public function editController() {
		
	}
	
	
	

}

?>