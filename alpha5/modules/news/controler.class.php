<?php
class NewsController extends Controller {
	/**
	 * Názvy sloupců v databázi
	 * @var string
	 */
	const COLUM_NEWS_LABEL = 'label';
	const COLUM_NEWS_LABEL_LANG_PREFIX = 'label_';
	const COLUM_NEWS_TEXT = 'text';
	const COLUM_NEWS_TEXT_LANG_PREFIX = 'text_';
	const COLUM_NEWS_URLKEY = 'urlkey';
	const COLUM_NEWS_TIME = 'time';
	const COLUM_NEWS_ID_USER = 'id_user';
	const COLUM_NEWS_ID_ITEM = 'id_item';
	const COLUM_NEWS_ID_NEW = 'id_new';
	const COLUM_NEWS_DELETED = 'deleted';
	
	/**
	 * Sloupce u tabulky uživatelů
	 * @var string
	 */
	const COLUM_USER_NAME = 'username';
	
	
	/**
	 * Speciální imageinární sloupce
	 * @var string
	 */
	const COLUM_NEWS_LANG = 'lang';
	const COLUM_NEWS_EDITABLE = 'editable';
	const COLUM_NEWS_EDIT_LINK = 'editlink';
	
	/**
	 * Názvy formůlářových prvků
	 * @var string
	 */
	const FORM_PREFIX = 'news_';
	const FORM_BUTTON_SEND = 'send';
	const FORM_BUTTON_EDIT = 'edit';
	const FORM_BUTTON_DELETE = 'delete';
	const FORM_INPUT_ID = 'id';
	const FORM_INPUT_LABEL = 'label_';
	const FORM_INPUT_TEXT = 'text_';
	
	/**
	 * Kontroler pro zobrazení novinek
	 */
	public function mainController() {
	
//		Kontrola práv
		$this->checkReadableRights();
		
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_DELETE])){
			$this->deleteNews();
		}
		
		$this->createModel("NewsList");
//		print_r($this->getModel());

		
//		Scrolovátka
		$scroll = $this->eplugin()->scroll();
		$scroll->setCountRecordsOnPage($this->getModule()->getRecordsOnPage());
		$scroll->setCountAllRecords($this->getDb()->count($this->getModule()->getDbTable()));
		
		$tableUsers = $this->getSysConfig()->getOptionValue(Auth::CONFIG_USERS_TABLE_NAME, Config::SECTION_DB_TABLES);
		
		$sqlSelect = $this->getDb()->select()->from(array("news" => $this->getModule()->getDbTable()), array(self::COLUM_NEWS_LABEL => "IFNULL(".self::COLUM_NEWS_LABEL_LANG_PREFIX.Locale::getLang().", ".self::COLUM_NEWS_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
													self::COLUM_NEWS_LANG => "IF(`".self::COLUM_NEWS_LABEL_LANG_PREFIX.Locale::getLang()."` != 'NULL', '".Locale::getLang()."', '".Locale::getDefaultLang()."')",
													self::COLUM_NEWS_TEXT => "IFNULL(".self::COLUM_NEWS_TEXT_LANG_PREFIX.Locale::getLang().", ".self::COLUM_NEWS_TEXT_LANG_PREFIX.Locale::getDefaultLang().")",
													self::COLUM_NEWS_URLKEY, self::COLUM_NEWS_ID_USER, self::COLUM_NEWS_ID_NEW))
											 ->limit($scroll->getStartRecord(), $scroll->getCountRecords())
											 ->order("news.".self::COLUM_NEWS_TIME, 'desc')
											 ->where("news.".self::COLUM_NEWS_ID_ITEM." = ".$this->getModule()->getId())
											 ->where("news.".self::COLUM_NEWS_DELETED." = ".(int)false)
											 ->join(array("users" => $tableUsers), "users.".self::COLUM_NEWS_ID_USER." = news.".self::COLUM_NEWS_ID_USER, null, Auth::USER_NAME);
		$this->getModel()->allNewsArray=$this->getDb()->fetchAssoc($sqlSelect);
		
		
//		Přidání linku pro editaci a jestli je novinka editovatelná pro uživatele
		foreach ($this->getModel()->allNewsArray as $key => $news) {
			if($news[self::COLUM_NEWS_ID_USER] == $this->getRights()->getAuth()->getUserId() OR $this->getRights()->isControll()){ 
				$this->getModel()->allNewsArray[$key][self::COLUM_NEWS_EDITABLE] = true;
				$this->getModel()->allNewsArray[$key][self::COLUM_NEWS_EDIT_LINK] = $this->getLink()->article($news[self::COLUM_NEWS_URLKEY])->action($this->getAction()->actionEdit());
			} else {
				$this->getModel()->allNewsArray[$key][self::COLUM_NEWS_EDITABLE] = false;
			}
			
					
		}
		
		
		//Doplnění pole s novinkami do modelu
		$this->getModel()->scroll = $scroll;
		
//		link pro přidání novinky
		$this->getModel()->linkToAdd = $this->getLink()->action($this->getAction()->actionAdd());
		
//		Příklady
		
//		$this->infoMsg()->addMessage("pokusná zpráva");
//		$this->errMsg()->addMessage("chyba!!! nezadány všechny parametry!!");
//		new	CoreException("test", 9);
		
//		echo $this->getModule()->getDir()->getDataDir();

//		Inicializace epluginu
//		$scroll = $this->addEPlugin("scroll", "scroll");		
//		
//		echo $sel = $this->getDb()->select()->from("test")->where("id = 5", "OR")->where("id < 5");
		
//		$ins=$this->getDb()->insert()->into($this->getModule()->getDbTable())
//								->colums("id_item", "key", "id_user", "label", "text", "time")
//								->values($this->getModule()->getId(), "pokusekkkkk", 3, "Pokůůsek", "pokusná zpráva", time());
//		echo $ins."<br />";
//		echo "INSERT INTO `vypecky_news` ( `id_item` , `key` , `id_user` , `label` , `text` , `time` ) VALUES ( '5', 'pokuuuuues', '3', 'Pokusek1', 'Pokusný text 1', '123456789');";
//		$this->getDb()->query($ins);

//		echo "lastID: ".$this->getDb()->getLastInsertedId();
		
//		$del = $this->getDb()->delete()->from("news")->where("id_new = 22", "OR")->where("id_new = 23", "OR");
//		echo $del."<br />";

//		$upd = $this->getDb()->update()->table("news")->set(array("text"=>"novy text a ještě delší"))->where("id_new = 25");
//		echo $upd;
//		$this->getDb()->query($upd);
		
//		echo $this->getLink()->action()->reload();
		
//		$this->changeActionView("test");
//		echo "<pre>";
//		print_r($this->getModel()->allNewsArray);
//		echo "</pre>";
		
//		echo "<br />".$this->getLink()."<br />";
//		echo "<br />".$this->getLink()->article("article-pokus")->action($this->getAction()->actionEdit())."<br />";
//		echo "<br />s article a action".$this->getLink()->article("novinka")->action()."<br />";
//		echo "<br />s kat ".$this->getLink()->category("pokusek")."<br />";
//		echo "<br />bez article ".$this->getLink()->article()."<br />";
//		echo "<br />bez article a params ".$this->getLink()->article()->params()."<br />";
//		echo "<br />this ".$this->getLink()."<br />";
//		echo "<br />clear ".$this->getLink(true)."<br />";
//		echo "<br />Akce editace: ".$this->getLink()->article()->action($this->getAction()->actionEdit())."<br />";
//		echo "<br />Akce vytvořená dinamicky: ".$this->getLink()->article()->action($this->getAction()->actionEditnews())."<br />";
//		echo "skupina uživatele: ".$this->getAuth()->getUserName();
	}

	public function showController()
	{
		$pole = $this->getArticle()->parse('-');
		$this->getDb()->select()->from();
//		echo $pole[0]."<br />";
//		echo $pole[1];
	}

	/**
	 * Kontroler pro přidání novinky
	 */
	public function addController(){
		$this->checkWritebleRights();
		
		$this->createModel("NewsDetail");
		
//		Podle počtu jazyků inicializujeme pole pro přidání novinky
		foreach (Locale::getAppLangs() as $lang) {
			$this->getModel()->newsArray[$lang] = array();
		}
		
//		Je ukládána novinka
//		zjištění pro který jazyk jsou údaj epovinné
//		$langArr = Locale::getAppLangs();
//		$obligatoryLang = $langArr[0];
		$obligatoryLang = Locale::getDefaultLang();
		
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_SEND])){
			if($_POST[self::FORM_PREFIX.self::FORM_INPUT_LABEL.$obligatoryLang] == null OR
			   $_POST[self::FORM_PREFIX.self::FORM_INPUT_TEXT.$obligatoryLang] == null){
			   	$this->errMsg()->addMessage(_('Nebyly zadány všechny potřebné údaje'));
			   	
			   	foreach (Locale::getAppLangs() as $lang) {
			   		$this->getModel()->newsArray[$lang][self::COLUM_NEWS_LABEL] = $_POST[self::FORM_PREFIX.self::FORM_INPUT_LABEL.$lang];
			   		$this->getModel()->newsArray[$lang][self::COLUM_NEWS_TEXT] = $_POST[self::FORM_PREFIX.self::FORM_INPUT_TEXT.$lang];
			   	}
			} else {
				
//				načtení všech předchozích url klíču z db
				$sqlSelecturlKey = $this->getDb()->select()->from($this->getModule()->getDbTable(), self::COLUM_NEWS_URLKEY);
				$urlkeysArray = $this->getDb()->fetchAssoc($sqlSelecturlKey);
				
				$sFunctions = new SpecialFunctions();
				
//				vygenerování url klíče
				$urlKey = $sFunctions->createDatabaseKey($_POST[self::FORM_PREFIX.self::FORM_INPUT_LABEL.$obligatoryLang], $urlkeysArray);
				
				//Vygenerování sloupců do kterých se bude zapisovat
				$columsArrayNames = array();
				$columsArrayValues = array();
				array_push($columsArrayNames, self::COLUM_NEWS_URLKEY);
				array_push($columsArrayValues, $urlKey);
				array_push($columsArrayNames, self::COLUM_NEWS_ID_ITEM);
				array_push($columsArrayValues, $this->getModule()->getId());
				array_push($columsArrayNames, self::COLUM_NEWS_ID_USER);
				array_push($columsArrayValues, $this->getRights()->getAuth()->getUserId());
				array_push($columsArrayNames, self::COLUM_NEWS_TIME);
				array_push($columsArrayValues, time());
				foreach (Locale::getAppLangs() as $lang) {
					array_push($columsArrayNames, self::COLUM_NEWS_LABEL_LANG_PREFIX.$lang);
					array_push($columsArrayValues, htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_INPUT_LABEL.$lang], ENT_QUOTES));
					array_push($columsArrayNames, self::COLUM_NEWS_TEXT_LANG_PREFIX.$lang);
					array_push($columsArrayValues, htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_INPUT_TEXT.$lang], ENT_QUOTES));
				}
				
				$sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable())
													 ->colums($columsArrayNames)
													 ->values($columsArrayValues);
				
//				Vložení do db
				if($this->getDb()->query($sqlInsert)){
					$this->infoMsg()->addMessage(_('Novinka byla uložena'));
					$this->getLink()->article()->action()->reload();
				} else {
					new CoreException(_('Novinku se nepodařilo uložit, chyba při ukládání do db'), 1);
				}
			}
			
			
		}
		
	}
	
	/**
	 * controller pro úpravu novinky
	 */
	public function editController() {
		$this->checkWritebleRights();
		
		$this->createModel("NewsDetail");
		
		$idNews = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_INPUT_ID], ENT_QUOTES);
		
		//		načtení novinky z db
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
											 ->where(self::COLUM_NEWS_ID_ITEM." = ".$this->getModule()->getId())
											 ->where(self::COLUM_NEWS_ID_NEW." = ".$idNews)
											 ->where(self::COLUM_NEWS_URLKEY." = '".$this->getArticle()->getArticle()."'");
		
		if(!$this->getRights()->isControll()){
			$sqlSelect->where(self::COLUM_NEWS_ID_USER." = ".$this->getRights()->getAuth()->getUserId());
			
		}
											 
		$news = $this->getDb()->fetchAssoc($sqlSelect, true);
		
		
//		Kontrole jestli se neukládá
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_SEND])){
			if($_POST[self::FORM_PREFIX.self::FORM_INPUT_LABEL.Locale::getDefaultLang()] == null OR
			   $_POST[self::FORM_PREFIX.self::FORM_INPUT_TEXT.Locale::getDefaultLang()] == null){
			   	$this->errMsg()->addMessage(_('Nebyly zadány všechny potřebné údaje'));
			   	
			   	foreach (Locale::getAppLangs() as $lang) {
			   		$news[self::COLUM_NEWS_LABEL_LANG_PREFIX.$lang] = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_INPUT_LABEL.$lang], ENT_QUOTES);
			   		$news[self::COLUM_NEWS_TEXT_LANG_PREFIX.$lang] = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_INPUT_TEXT.$lang], ENT_QUOTES);
			   	}
			} else {
				
//				Pole pro vložení
				$updateArray = array();
				foreach (Locale::getAppLangs() as $lang) {
					$_POST[self::FORM_PREFIX.self::FORM_INPUT_LABEL.$lang] != null ? $label = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_INPUT_LABEL.$lang], ENT_QUOTES) 
																				   : $label = null;
					$_POST[self::FORM_PREFIX.self::FORM_INPUT_TEXT.$lang] != null ? $text = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_INPUT_TEXT.$lang], ENT_QUOTES) 
																				   : $text = null;
					
					$updateArray[self::COLUM_NEWS_LABEL_LANG_PREFIX.$lang] = $label;
					$updateArray[self::COLUM_NEWS_TEXT_LANG_PREFIX.$lang] = $text;
				}
				
//				Vložení do db
				$sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable())
													 ->set($updateArray)
													 ->where(self::COLUM_NEWS_ID_NEW." = ".$idNews)
													 ->where(self::COLUM_NEWS_URLKEY." = '".$this->getArticle()->getArticle()."'");
				
				if($this->getDb()->query($sqlInsert)){
					$this->infoMsg()->addMessage(_("Novinka byla upravena"));
					$this->getLink()->article()->action()->reload();
				} else {
					new CoreException(_("Nepodařilo se upravit novinku v db"),2);
				}

				
				
			}
		}
		
		

		
//		echo "<pre>";
//		print_r($news);
//		echo "</pre>";
		
		if(!empty($news)){
			//		Podle počtu jazyků inicializujeme pole pro přidání novinky
			foreach (Locale::getAppLangs() as $lang) {
				$this->getModel()->newsArray[$lang] = array();
				$this->getModel()->newsArray[$lang][self::COLUM_NEWS_LABEL] = $news[self::COLUM_NEWS_LABEL_LANG_PREFIX.$lang];
				$this->getModel()->newsArray[$lang][self::COLUM_NEWS_TEXT] = $news[self::COLUM_NEWS_TEXT_LANG_PREFIX.$lang];
			}
			$this->getModel()->idNews = $news[self::COLUM_NEWS_ID_NEW];
			$this->getModel()->newsDefaultLabel = $news[self::COLUM_NEWS_LABEL_LANG_PREFIX.Locale::getDefaultLang()];
			
		} else {
			$this->errMsg()->addMessage(_('Nepodařilo se načíst novinku z db. zřejmně nemáte práva pro editaci novinky'));
		}
		
//		echo "<pre>";
//		print_r($this->getModel()->newsArray);
//		echo "</pre>";
	}
	
	/**
	 * metoda pro mazání novinky
	 */
	public function deleteNews() {
		if(!is_numeric($_POST[self::FORM_PREFIX.self::FORM_INPUT_ID])){
			new CoreException(_("Bylo načteno špatné id novinky. Novinka nebyla smazána"), 3);
		} else {
//			smazání novinky
			$sqlUpdate = $this->getDb()->update()->table($this->getModule()->getDbTable())
												 ->set(array(self::COLUM_NEWS_DELETED => (int)true))
												 ->where(self::COLUM_NEWS_ID_NEW." = ".$_POST[self::FORM_PREFIX.self::FORM_INPUT_ID]);
												 
			if($this->getDb()->query($sqlUpdate)){
				$this->infoMsg()->addMessage(_('Novinka byla smazána'));
				$this->getLink()->article()->action()->params()->reload();
			} else {
				new CoreException(_('Novinku se nepodařilo smazat, zřejmně špatně přenesené id'), 4);
			}
		}
	}
	
	
}

?>