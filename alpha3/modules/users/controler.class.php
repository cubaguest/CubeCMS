<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class UsersController extends Controller {
	/**
	 * Název $_GET kterým se přenáší řazení
	 * @var string
	 */
	const ORDER_SGET_NAME = 'order';
	
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var string
	 */
	const COLUM_ID 			= 'id_user';
	const COLUM_ID_GROUP	= 'id_group';
	const COLUM_GROUP_NAME 	= 'group_name';
	const COLUM_NAME 		= 'name';
	const COLUM_SURNAME 	= 'surname';
	const COLUM_USERNAME 	= 'username';
	const COLUM_PASSWORD 	= 'password';
	const COLUM_MAIL 		= 'mail';
	const COLUM_NOTE 		= 'note';
	const COLUM_BLOCKED		= 'blocked';
	const COLUM_DELETE 		= 'deleted';
	const COLUM_PROTECTED 	= 'protected';
	const COLUM_USED 		= 'used';
	const COLUM_LABEL 		= 'label';


	/**
	 * Název SESSION s hledáním
	 * @var string
	 */
	const SEARCH_SESSION_NAME = 'search';
	const SEARCH_SESSION_IDENT = 'user';

	/**
	 * Název session s linkem zpět
	 * @var string
	 */
	const LINK_BACK_SESSION_NAME = 'link_back';
	
	
	/**
	 * Názvy session s jednotlivými prvky hledání
	 * @var string
	 */
	const SEARCH_ID 		= 'id';
	const SEARCH_NAME 		= 'name';
	const SEARCH_SURNAME 	= 'surname';
	const SEARCH_USERNAME 		= 'username';
	
	/**
	 * Prefix vyhledávacích polí
	 * @var string
	 */
	const SEARCH_PREFIX = 'userss_search_';

	/**
	 * Url separtor zástupce/id
	 * @var string
	 */
	const DETAIL_ARTICLE_ID_SEPARATOR = '-';
	
	/**
	 * Prefix pro název zástupce (article)
	 * @var string
	 */
	const DETAIL_ARTICLE_PREFIX = 'iduser';

	/**
	 * prefix názvu prvků formůláře
	 * @var string
	 */
	const FORM_PREFIX = 'user_';
	const FORM_SEARCH_SEND = 'search_send';
	const FORM_SEARCH_ID = 'search_id';
	const FORM_SEARCH_NAME = 'search_name';
	const FORM_SEARCH_SURNAME = 'search_surname';
	const FORM_SEARCH_USERNAME = 'search_username';
	
	const FORM_SEND		= 'send';
	const FORM_DELETE	= 'delete';
	const FORM_ID		= 'id';
	const FORM_NAME		= 'name';
	const FORM_SURNAME	= 'surname';
	const FORM_USERNAME	= 'username';
	const FORM_PASSWORD	= 'password';
	const FORM_PASSWORD2= 'password2';
	const FORM_MAIL		= 'mail';
	const FORM_NOTE		= 'note';
	const FORM_GROUP	= 'group';
	
	/**
	 * Minimální délka hesla
	 * @var integer
	 */
	const PASSWORD_MIN_LENGHT = 5;
	
	/**
	 * Pole s typy řezení
	 * @var array
	 */
	private $ordersArray = array("id_desc" => "iddesc", "id_asc" => "idasc",
								 "name_desc" => "namedesc", "name_asc"=>"nameasc",
								 "surname_desc" => "surndesc", "surname_asc" => "surnasc",
								 "username_desc" => "usernamedesc", "username_asc" => "usernameasc",
								 "groupname_desc" => "groupnamedesc", "groupname_asc" => "groupnameasc");
	
	/**
	 * Pole s hledanými hodnotami
	 * @var array
	 */
	private $searchArray = array();
	
	
	public function mainController() {
		$this->createModel("UsersList");

//		kontrole jesli uživatel má dostatečná práva
		if(!$this->getRights()->isControll()){
			$this->getLink()->category()->action()->article()->params()->reload();
		} else {
			//		odkaz pro přidání
			$this->getModel()->linkToAdd = $this->getLink()->action($this->getAction()->actionAdd());
			//nastavení že je kategorie pod kontrolou
			$this->getModel()->isControll = $this->getRights()->isControll();
		}

					
		if(isset($_SESSION[self::SEARCH_SESSION_NAME]) AND isset( $_SESSION[self::SEARCH_SESSION_NAME][self::SEARCH_SESSION_IDENT]) AND $_SESSION[self::SEARCH_SESSION_NAME][self::SEARCH_SESSION_IDENT] != self::SEARCH_SESSION_IDENT){
			unset($_SESSION[self::SEARCH_SESSION_NAME]);
		}

		
		//		Scrolovátka
		$scroll = $this->eplugin()->scroll();
		$scroll->setCountRecordsOnPage($this->getModule()->getRecordsOnPage());
		
		$scroll->setCountAllRecords($this->getDb()->count($this->getModule()->getDbTable()));

//		Základní select
		$sqlSelect = $this->getDb()->select()->from(array("usr"=>$this->getModule()->getDbTable()))
											 ->limit($scroll->getStartRecord(), $scroll->getCountRecords())
											 ->join(array("grp"=>$this->getModule()->getDbTable(2)), "grp.".self::COLUM_ID_GROUP." = usr.".self::COLUM_ID_GROUP, null, array(self::COLUM_GROUP_NAME=>"label"))
											 ->where("usr.".self::COLUM_DELETE." = ".(int)false)
											 ->where("grp.".self::COLUM_USED." = ".(int)true);
		
		//Řazení prvků
		isset($_GET[self::ORDER_SGET_NAME]) ? $order = htmlspecialchars($_GET[self::ORDER_SGET_NAME]) : $order = null;
		
		switch ($order) {
			case $this->ordersArray["id_desc"]:
				$sqlSelect = $sqlSelect->order("usr.".self::COLUM_ID, "DESC");
				break;
			case $this->ordersArray["name_desc"]:
				$sqlSelect = $sqlSelect->order("usr.".self::COLUM_NAME, "DESC");
				break;
			case $this->ordersArray["name_asc"]:
				$sqlSelect = $sqlSelect->order("usr.".self::COLUM_NAME, "ASC");
				break;
			case $this->ordersArray["surname_desc"]:
				$sqlSelect = $sqlSelect->order("usr.".self::COLUM_SURNAME, "DESC");
				break;
			case $this->ordersArray["surname_asc"]:
				$sqlSelect = $sqlSelect->order("usr.".self::COLUM_SURNAME, "ASC");
				break;
			case $this->ordersArray["username_desc"]:
				$sqlSelect = $sqlSelect->order("usr.".self::COLUM_USERNAME, "DESC");
				break;
			case $this->ordersArray["username_asc"]:
				$sqlSelect = $sqlSelect->order("usr.".self::COLUM_USERNAME, "ASC");
				break;
			case $this->ordersArray["groupname_desc"]:
				$sqlSelect = $sqlSelect->order(self::COLUM_GROUP_NAME, "DESC");
				break;
			case $this->ordersArray["groupname_asc"]:
				$sqlSelect = $sqlSelect->order(self::COLUM_GROUP_NAME, "ASC");
				break;
			default:
				$sqlSelect = $sqlSelect->order("usr.".self::COLUM_ID, "ASC");
				break;
		}
		
		//Vyhledávání
		if(isset($_POST[self::FORM_PREFIX.self::FORM_SEARCH_SEND])){
			if(!isset($_SESSION[self::SEARCH_SESSION_NAME])){
				$_SESSION[self::SEARCH_SESSION_NAME] = array();
			}
			
			if($_POST[self::FORM_PREFIX.self::FORM_SEARCH_ID] == null AND $_POST[self::FORM_PREFIX.self::FORM_SEARCH_NAME] == null 
				AND $_POST[self::FORM_PREFIX.self::FORM_SEARCH_SURNAME] == null AND $_POST[self::FORM_PREFIX.self::FORM_SEARCH_USERNAME] == null){
				unset($_SESSION[self::SEARCH_SESSION_NAME]);
			}
			
			$_POST[self::SEARCH_PREFIX.self::SEARCH_ID] != null ?	$this->searchArray[self::SEARCH_ID] = htmlspecialchars($_POST[self::SEARCH_PREFIX.self::SEARCH_ID], ENT_QUOTES) : null;
			$_POST[self::SEARCH_PREFIX.self::SEARCH_NAME] != null ?	$this->searchArray[self::SEARCH_NAME] = htmlspecialchars($_POST[self::SEARCH_PREFIX.self::SEARCH_NAME], ENT_QUOTES) : null;
			$_POST[self::SEARCH_PREFIX.self::SEARCH_SURNAME]!= null ?	$this->searchArray[self::SEARCH_SURNAME] = htmlspecialchars($_POST[self::SEARCH_PREFIX.self::SEARCH_SURNAME], ENT_QUOTES) : null;
			$_POST[self::SEARCH_PREFIX.self::SEARCH_USERNAME]!= null ?	$this->searchArray[self::SEARCH_USERNAME] = htmlspecialchars($_POST[self::SEARCH_PREFIX.self::SEARCH_USERNAME], ENT_QUOTES) : null;
			
			$_SESSION[self::SEARCH_SESSION_NAME]=$this->searchArray;
		}
											 
		if(isset($_SESSION[self::SEARCH_SESSION_NAME])){
			$this->searchArray = $_SESSION[self::SEARCH_SESSION_NAME];
			
			//hledání id
			if(isset($this->searchArray[self::SEARCH_ID])){
				$sqlSelect = $sqlSelect->where("usr.".self::COLUM_ID.' = \''.$this->searchArray[self::SEARCH_ID].'\'');
			}
			//hledání jména
			if(isset($this->searchArray[self::SEARCH_NAME])){
//				->where("item.".Rights::RIGHTS_GROUPS_TABLE_PREFIX.$userNameGroup." LIKE \"r__\"")
				$sqlSelect = $sqlSelect->where("usr.".self::COLUM_NAME.' LIKE \'%'.$this->searchArray[self::SEARCH_NAME].'%\'');
			}
			//hledání přijmení
			if(isset($this->searchArray[self::SEARCH_SURNAME])){
				$sqlSelect = $sqlSelect->where("usr.".self::COLUM_SURNAME.' LIKE \'%'.$this->searchArray[self::SEARCH_SURNAME].'%\'');
			}
			//hledání adresy
			if(isset($this->searchArray[self::SEARCH_ADDRESS])){
				$sqlSelect = $sqlSelect->where("usr.".self::COLUM_USERNAME.' LIKE \'%'.$this->searchArray[self::SEARCH_USERNAME].'%\'');
			}
			
		}

//		echo $sqlSelect;
											 
		$usersArray = $this->getDb()->fetchAssoc($sqlSelect);
		
		foreach ($usersArray as $key => $value) {
			$usersArray[$key]["detail_link"] = $this->getLink()->article(self::DETAIL_ARTICLE_PREFIX.self::DETAIL_ARTICLE_ID_SEPARATOR.$value[self::COLUM_ID]);
		}
			
		$this->getModel()->allUsersArray=$usersArray;
			
		$this->getModel()->userSearchArray=$this->searchArray;
		
		//Doplnění pole s novinkami do modelu
		$this->getModel()->scroll = $scroll;
		
		//linky pro nastavení řazení
		$this->getModel()->usersTableOrder["id_desc"] = $this->getLink()->param("order", $this->ordersArray["id_desc"]);
		$this->getModel()->usersTableOrder["id_asc"] = $this->getLink()->param("order", $this->ordersArray["id_asc"]);
		$this->getModel()->usersTableOrder["name_desc"] = $this->getLink()->param("order", $this->ordersArray["name_desc"]);
		$this->getModel()->usersTableOrder["name_asc"] = $this->getLink()->param("order", $this->ordersArray["name_asc"]);
		$this->getModel()->usersTableOrder["surname_desc"] = $this->getLink()->param("order", $this->ordersArray["surname_desc"]);
		$this->getModel()->usersTableOrder["surname_asc"] = $this->getLink()->param("order", $this->ordersArray["surname_asc"]);
		$this->getModel()->usersTableOrder["username_desc"] = $this->getLink()->param("order", $this->ordersArray["username_desc"]);
		$this->getModel()->usersTableOrder["username_asc"] = $this->getLink()->param("order", $this->ordersArray["username_asc"]);

	}
	
	/**
	 * Metoda pro zobrazení detailu zástupce
	 */
	public function showController() {
//		Parsování článku na název(nedůležité) a id uživatele
		$user = $this->getArticle()->parse(self::DETAIL_ARTICLE_ID_SEPARATOR);
//		první je název pro přenos, druhé je id uživatele
		$idUser = (int)$user[1];
		
//		Mazání záznamu
		if(isset($_POST[self::FORM_PREFIX.self::FORM_DELETE]) AND is_numeric($_POST[self::FORM_PREFIX.self::FORM_ID])){
			$sqlUpdateDel = $this->getDb()->update()->table($this->getModule()->getDbTable())
													->set(array(self::COLUM_DELETE =>(int)true))
													->where(self::COLUM_ID." = ".$idUser);
													
			$this->getDb()->query($sqlUpdateDel);
			
			$this->infoMsg()->addMessage(_("Uživatel byl smazán"));
			$this->getLink()->action()->article()->reload();
		}
		
		
		$this->createModel("userDetail");
		
		$sqlSelect = $this->getDb()->select()->from(array("usr"=>$this->getModule()->getDbTable()))
											 ->where(self::COLUM_ID." = '$idUser'")
											 ->where(self::COLUM_DELETE." = ".(int)false)
											 ->join(array("grp"=>$this->getModule()->getDbTable(2)),"grp.".self::COLUM_ID_GROUP." = usr.".self::COLUM_ID_GROUP, null, array(self::COLUM_GROUP_NAME=>self::COLUM_LABEL));
		
		$this->getModel()->userDetailArray = $this->getDb()->fetchAssoc($sqlSelect, true);
		
		if($this->getModel()->userDetailArray == null){
			new CoreException(_("Nepodařilo se načíst uživatele z Db, zřejmně špatně zadané id", 1));
		}
		
//		Link pro editaci
		$this->getModel()->linkToEdit = $this->getLink()->action($this->getAction()->actionEdit());
		
		$this->getModel()->linkToBack = $this->getLink()->article();
		
		//vymazání session pro návrat
		unset($_SESSION[self::LINK_BACK_SESSION_NAME]);

	}
	
	/**
	 * Metoda pro úpravu
	 */
	public function editController() {
		$this->createModel("userDetail");

		//		Parsování článku na název(nedůležité) a id zástupce
		$user = $this->getArticle()->parse(self::DETAIL_ARTICLE_ID_SEPARATOR);
//		první je název pro přenos, druhé je id rodiče
		$idUser = (int)$user[1];
		
		//uložení do db
		if(isset($_POST[self::FORM_PREFIX.self::FORM_SEND])){
			
			if($this->checkEditUserParams()){
				$user = array();
				$user[self::COLUM_NAME] = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_NAME], ENT_QUOTES);
				$user[self::COLUM_SURNAME] = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_SURNAME], ENT_QUOTES);
				$user[self::COLUM_USERNAME] = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_USERNAME], ENT_QUOTES);
				
				if($_POST[self::FORM_PREFIX.self::FORM_PASSWORD] != null){
					$user[self::COLUM_PASSWORD] = md5(htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_PASSWORD], ENT_QUOTES));
				}
				
				$user[self::COLUM_MAIL] = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_MAIL], ENT_QUOTES);
				$user[self::COLUM_NOTE] = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_NOTE], ENT_QUOTES);
				$user[self::COLUM_ID_GROUP] = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_GROUP], ENT_QUOTES);
				
				$sqlUpd = $this->getDb()->update()->table($this->getModule()->getDbTable())
										->set($user)->where(self::COLUM_ID." = ". $idUser);


				if(!$this->getDb()->query($sqlUpd)){
					$this->errMsg()->addMessage(_("Údaje se nepodařilo uložit"));			
				} else {
					$this->infoMsg()->addMessage(_("Údaje byly změněny"));


					$this->getLink()->action()->reload();
				}
			}
		}
		

		
		$sqlSelect = $this->getDb()->select()->from(array("usr"=>$this->getModule()->getDbTable()))
											 ->where(self::COLUM_ID." = '$idUser'")
											 ->where(self::COLUM_DELETE." = ".(int)false)
											 ->join(array("grp"=>$this->getModule()->getDbTable(2)),"grp.".self::COLUM_ID_GROUP." = usr.".self::COLUM_ID_GROUP, null, array(self::COLUM_GROUP_NAME=>self::COLUM_LABEL, self::COLUM_ID_GROUP));
		
		
		$this->getModel()->userDetailArray = $this->getDb()->fetchAssoc($sqlSelect, true);
		
		if($this->getModel()->userDetailArray == null){
			new CoreException(_("Nepodařilo se načíst uživatele z Db, zřejmně špatně zadané id", 1));
		};
		
		$this->getModel()->linkToBack = $this->getLink()->action();
		
		//		načtení skupin z db
		$sqlGroupSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(2))
										->where(self::COLUM_USED." = ".(int)true);
		
		$this->getModel()->groupsArray = $this->getDb()->fetchAssoc($sqlGroupSelect);
		
	}
	
	/**
	 * Metoda pro přidávání uživatelů
	 */
	public function addController() {
		$this->createModel("UserDetail");
		
//		kontrole jesli uživatel má dostatečná práva jinak přesměruj jinam
		if(!$this->getRights()->isControll()){
			$this->getLink()->category()->action()->article()->params()->reload();
		}
		
//		Je-li uživatel uložen
		if(isset($_POST[self::FORM_PREFIX.self::FORM_SEND])){
//			Jsou li vyplněny všechny povinné údaje

			if($this->checkNewUserParams()){	
				$name = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_NAME], ENT_QUOTES);
				$surname = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_SURNAME], ENT_QUOTES);
				$username = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_USERNAME], ENT_QUOTES);
				$password = md5(htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_PASSWORD], ENT_QUOTES));
				$mail = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_MAIL], ENT_QUOTES);
				$note = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_NOTE], ENT_QUOTES);
				$groupId = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_GROUP], ENT_QUOTES);
				
				
				$sqlInserUser = $this->getDb()->insert()->into($this->getModule()->getDbTable())
											  ->colums(self::COLUM_NAME, self::COLUM_SURNAME, self::COLUM_USERNAME, self::COLUM_PASSWORD, self::COLUM_MAIL,
											  		   self::COLUM_ID_GROUP, self::COLUM_NOTE)
											  ->values($name, $surname, $username, $password, $mail, $groupId, $note);
											  
				$this->getDb()->query($sqlInserUser);
				
				$this->infoMsg()->addMessage(_("Uživatel byl uložen"));
				$this->getLink()->action()->article()->reload();
				
			}
			
			
		}
		
		
//		načtení skupin z db
		$sqlGroupSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(2))
										->where(self::COLUM_USED." = ".(int)true);
		
		$this->getModel()->groupsArray = $this->getDb()->fetchAssoc($sqlGroupSelect);
		
	}
	
	/**
	 * Metoda kontroluje zadání všech parametrů
	 * @return boolean -- jesli byly zadány všechny parametry
	 */
	private function checkNewUserParams() {
		if($_POST[self::FORM_PREFIX.self::FORM_USERNAME] == null OR $_POST[self::FORM_PREFIX.self::FORM_PASSWORD] == null OR 
			$_POST[self::FORM_PREFIX.self::FORM_PASSWORD2] == null OR $_POST[self::FORM_PREFIX.self::FORM_GROUP] == null){
			$this->errMsg()->addMessage(_("Nebyly zadány všechny potřebné údaje"));		
		} else if(strlen($_POST[self::FORM_PREFIX.self::FORM_PASSWORD]) < self::PASSWORD_MIN_LENGHT){
			$this->errMsg()->addMessage(_("Zadané heslo je příliš krátké. Minimální délka je ").self::PASSWORD_MIN_LENGHT._(" znaků"));
		} else if($_POST[self::FORM_PREFIX.self::FORM_PASSWORD] != $_POST[self::FORM_PREFIX.self::FORM_PASSWORD2]){
			$this->errMsg()->addMessage(_("Heslo a kontrolní heslo nejsou totožné."));
		} else {
			return true;
		}
		
		return false;
	}
	private function checkEditUserParams() {
		if($_POST[self::FORM_PREFIX.self::FORM_USERNAME] == null OR $_POST[self::FORM_PREFIX.self::FORM_GROUP] == null){
			$this->errMsg()->addMessage(_("Nebyly zadány všechny potřebné údaje"));		
		} else if($_POST[self::FORM_PREFIX.self::FORM_PASSWORD] != null AND strlen($_POST[self::FORM_PREFIX.self::FORM_PASSWORD]) < self::PASSWORD_MIN_LENGHT){
			$this->errMsg()->addMessage(_("Zadané heslo je příliš krátké. Minimální délka je ").self::PASSWORD_MIN_LENGHT._(" znaků"));
		} else if($_POST[self::FORM_PREFIX.self::FORM_PASSWORD] != null AND $_POST[self::FORM_PREFIX.self::FORM_PASSWORD] != $_POST[self::FORM_PREFIX.self::FORM_PASSWORD2]){
			$this->errMsg()->addMessage(_("Heslo a kontrolní heslo nejsou totožné."));
		} else {
			return true;
		}
		
		return false;
	}
	

}

?>