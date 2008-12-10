<?php
class NewsController extends Controller {
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
	
	
	/**
	 * Speciální imageinární sloupce
	 * @var string
	 */
	const COLUMN_NEWS_LANG = 'lang';
	const COLUMN_NEWS_EDITABLE = 'editable';
	const COLUMN_NEWS_EDIT_LINK = 'editlink';
	const COLUMN_NEWS_SHOW_LINK = 'showlink';
	
	/**
	 * Názvy formůlářových prvků
	 * @var string
	 */
	const FORM_PREFIX = 'news_';
	const FORM_BUTTON_SEND = 'send';
	const FORM_BUTTON_EDIT = 'edit';
	const FORM_BUTTON_DELETE = 'delete';
	const FORM_INPUT_ID = 'id';
	const FORM_INPUT_LABEL = 'label';
	const FORM_INPUT_LABEL_PREFIX = 'label_';
	const FORM_INPUT_TEXT = 'text';
	const FORM_INPUT_TEXT_PREFIX = 'text_';
	
	/**
	 * Název $_GET s počtem zobrazených novinek
	 * @var string
	 */
	const GET_NUM_NEWS = 'numnews';
	
	/**
	 * Proměná pro zobrazení všech novinek
	 * @var string
	 */
	const GET_ALL_NEWS = 'all';
	
	/**
	 * Pole s počty zobrazených novinek
	 * @var array
	 */
	private $getNumShowNews = array(5,10,20,50);
	
	
	/**
	 * Kontroler pro zobrazení novinek
	 */
	public function mainController() {
	
//		Kontrola práv
		$this->checkReadableRights();
		
//		Vytvoření modelu
		$listNews = new NewsListModel();
		
//		Scrolovátka
		$scroll = new Scroll($this->errMsg(), $this->infoMsg(), $this->getRights());
		
		if(isset($_GET[self::GET_NUM_NEWS]) AND (is_numeric($_GET[self::GET_NUM_NEWS]) OR $_GET[self::GET_NUM_NEWS] == self::GET_ALL_NEWS)){
			if(is_numeric($_GET[self::GET_NUM_NEWS])){
				$scroll->setCountRecordsOnPage((int)$_GET[self::GET_NUM_NEWS]);
			} else if($_GET[self::GET_NUM_NEWS] == self::GET_ALL_NEWS){
				$scroll->setCountRecordsOnPage($listNews->getCountNews());
			}
		} else {
			$scroll->setCountRecordsOnPage($this->getModule()->getRecordsOnPage());
		}
				
		$scroll->setCountAllRecords($listNews->getCountNews());
		
//		Vybrání novinek
		$newsArray = $listNews->getSelectedListNews($scroll->getStartRecord(), $scroll->getCountRecords());
		
		
//		Přidání linku pro editaci a jestli se dá editovat
		foreach ($newsArray as $key => $news) {
			if($news[self::COLUMN_NEWS_ID_USER] == $this->getRights()->getAuth()->getUserId() OR $this->getRights()->isControll()){ 
				$newsArray[$key][self::COLUMN_NEWS_EDITABLE] = true;
				$newsArray[$key][self::COLUMN_NEWS_EDIT_LINK] = $this->getLink()->article($news[self::COLUMN_NEWS_URLKEY])->action($this->getAction()->actionEdit());
			} else {
				$newsArray[$key][self::COLUMN_NEWS_EDITABLE] = false;
			}
//			Link pro zobrazení
			$newsArray[$key][self::COLUMN_NEWS_SHOW_LINK] = $this->getLink()->article($news[self::COLUMN_NEWS_URLKEY]);
			
		}
		
//		Přenos do viewru
		$this->container()->addEplugin('scroll',$scroll);
		
//		Link pro přidání
		if($this->getRights()->isWritable()){
			$this->container()->addLink('add_new',$this->getLink()->action($this->getAction()->actionAdd()));
		}
		
		$this->container()->addData('news_list', $newsArray);
		
//		linky pro zobrazení určitého počtu novinek
		foreach ($this->getNumShowNews as $num) {
			$numNewsArray[$num] = null;
			if($listNews->getCountNews() >= $num){
				$numNewsArray[$num] = $this->getLink()->params()->param(self::GET_NUM_NEWS, $num);
//				$this->container()->addLink('show_'.$num,$this->getLink()->param(self::GET_NUM_NEWS, $num));
			};
		}
		
		$this->container()->addData('num_news', $numNewsArray);
				
		$this->container()->addLink('all_news',$this->getLink()->params()->param(self::GET_NUM_NEWS, self::GET_ALL_NEWS));
		
	}

	public function showController()
	{
//		mazání novinky
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_DELETE])){
			$this->deleteNews();
		}
		
		$newsDetail = new NewsDetailModel();
		
		$new = $newsDetail->getNewsDetailByUrlkeySelLang($this->getArticle()->getArticle());
		
		$this->container()->addData('new', $new);
		$this->container()->addData('new_name', $new[self::COLUMN_NEWS_LABEL]);

		if($this->getRights()->isControll() OR $new[self::COLUMN_NEWS_ID_USER] == $this->getRights()->getAuth()->getUserId()){
			$this->container()->addLink('edit_link', $this->getLink()->action($this->getAction()->actionEdit()));
			$this->container()->addData('editable', true);
			$this->container()->addLink('add_new',$this->getLink()->action($this->getAction()->actionAdd())->article());
		}
		
		$this->container()->addLink('link_back', $this->getLink()->action()->article());
		
	}

	/**
	 * Kontroler pro přidání novinky
	 */
	public function addController(){
		$this->checkWritebleRights();

//		Helpre pro práci s jazykovými poli
		$localeHelper = new LocaleCtrlHelper();
		
//		Odeslané pole
		$sendArray = array();
		
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_SEND])){
			
			$sendArray = $localeHelper->postsToArray(array(self::FORM_INPUT_LABEL_PREFIX, self::FORM_INPUT_TEXT_PREFIX), self::FORM_PREFIX);
			
			if($_POST[self::FORM_PREFIX.self::FORM_INPUT_LABEL_PREFIX.Locale::getDefaultLang()] == null OR
			   $_POST[self::FORM_PREFIX.self::FORM_INPUT_TEXT_PREFIX.Locale::getDefaultLang()] == null){
			   	$this->errMsg()->addMessage(_('Nebyly zadány všechny potřebné údaje'));
			} else {
				
				$mainLang = Locale::getDefaultLang();
				$mainLabel = $sendArray[self::FORM_INPUT_LABEL_PREFIX.$mainLang];
				
				$newDetail = new NewsDetailModel();
				
				$saved = $newDetail->saveNewNews($sendArray,$mainLabel,$this->getRights()->getAuth()->getUserId());

//				Vložení do db
				if($saved){
					$this->infoMsg()->addMessage(_('Novinka byla uložena'));
					$this->getLink()->article()->action()->params()->reload();
				} else {
					new CoreException(_('Novinku se nepodařilo uložit, chyba při ukládání do db'), 1);
				}
			}
		}

		$lArray = $localeHelper->generateArray(array(self::FORM_INPUT_LABEL, self::FORM_INPUT_TEXT),$sendArray);
		
		$this->container()->addData('new_data', $lArray);
		
//		Odkaz zpět
		$this->container()->addLink('link_back', $this->getLink()->action()->article());
	}
	
	/**
	 * controller pro úpravu novinky
	 */
	public function editController() {
		$this->checkWritebleRights();
		
		$newsModel = new NewsDetailModel();
		
		$new = $newsModel->getNewsDetailByUrlkey($this->getArticle());
		
		if(empty($new)){
			$this->errMsg()->addMessage(_('Požadovaná novinky neexistuje'));
			exit();
		}
		
		if($new[self::COLUMN_NEWS_LABEL_LANG_PREFIX.Locale::getLang()] != null){
			$this->container()->addData('news_label', $new[self::COLUMN_NEWS_LABEL_LANG_PREFIX.Locale::getLang()]);
		} else {
			$this->container()->addData('news_label', $new[self::COLUMN_NEWS_LABEL_LANG_PREFIX.Locale::getDefaultLang()]);
		}
		//		Helpre pro práci s jazykovými poli
		$localeHelper = new LocaleCtrlHelper();
		
		$sendArray = array();
		
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_SEND])){
			
			$sendArray = $localeHelper->postsToArray(array(self::FORM_INPUT_LABEL_PREFIX, self::FORM_INPUT_TEXT_PREFIX), self::FORM_PREFIX);
						
			if($_POST[self::FORM_PREFIX.self::FORM_INPUT_LABEL_PREFIX.Locale::getDefaultLang()] == null OR
			   $_POST[self::FORM_PREFIX.self::FORM_INPUT_TEXT_PREFIX.Locale::getDefaultLang()] == null){
			   	$this->errMsg()->addMessage(_('Nebyly zadány všechny potřebné údaje'));
			} else {
				
				$updated = $newsModel->saveEditNews($sendArray, $this->getArticle());
				
				if($updated){
					$this->infoMsg()->addMessage(_("Novinka byla upravena"));
					$this->getLink()->action()->reload();
				} else {
					new CoreException(_("Nepodařilo se upravit novinku v db"),2);
				}
			}
		}
		
		$newArray = $localeHelper->generateArray(array(self::FORM_INPUT_LABEL, self::FORM_INPUT_TEXT), $sendArray, $new);
		$this->container()->addData('new_data', $newArray);
		
		//		Odkaz zpět
		$this->container()->addLink('link_back', $this->getLink()->action());
	}
	
	/**
	 * kontroler pro mazání novinky
	 */
	public function deleteNews() {
		if(!is_numeric($_POST[self::FORM_PREFIX.self::FORM_INPUT_ID])){
			new CoreException(_("Bylo načteno špatné id novinky. Novinka nebyla smazána"), 3);
		} else {
			$newDetail = new NewsDetailModel();
			
			$deleted = $newDetail->deleteNews((int)$_POST[self::FORM_PREFIX.self::FORM_INPUT_ID]);
			
			if($deleted){
				$this->infoMsg()->addMessage(_('Novinka byla smazána'));
				$this->getLink()->article()->action()->params()->reload();
			} else {
				new CoreException(_('Novinku se nepodařilo smazat, zřejmně špatně přenesené id'), 4);
			}
		}
	}
}

?>