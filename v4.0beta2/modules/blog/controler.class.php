<?php
/**
 * Controler modulu Blogu
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class BlogController extends Controller {
	

	/**
	 * Názvy sloupců v tabulce se sekcemi (blog_sections)
	 * @var string
	 */
//	const COLUM_SECTION_LABEL = 'label';
//	const COLUM_SECTION_LABEL_LANG_PREFIX = 'label_';
//	const COLUM_SECTION_URLKEY = 'urlkey';
//	const COLUM_SECTION_TIME = 'time';
//	const COLUM_SECTION_ID_USER = 'id_user';
//	const COLUM_SECTION_ID_ITEM = 'id_item';
//	const COLUM_SECTION_ID = 'id_section';
//	const COLUM_SECTION_DELETED = 'deleted';
//	const COLUM_SECTION_DELETED_BY_ID_USER = 'deleted_by_id_user';
			

	
		
	/**
	 * Sloupce u tabulky uživatelů
	 * @var string
	 */
//	const COLUM_USER_NAME = 'username';
	
	
	/**
	 * Speciální imageinární sloupce
	 * @var string
	 */
//	const COLUM_LANG = 'lang';
//	const COLUM_EDITABLE = 'editable';
//	const COLUM_EDIT_LINK = 'editlink';
	
	/**
	 * Názvy formůlářových prvků
	 * @var string
	 */
	const FORM_PREFIX = 'blog_';
	const FORM_BUTTON_SEND = 'send';
	const FORM_BUTTON_EDIT = 'edit';
	const FORM_BUTTON_DELETE = 'delete';
	const FORM_ID = 'id';
	const FORM_LABEL = 'label_';
	const FORM_TEXT = 'text_';
	const FORM_SECTION_PREFIX = 'section_';
	const FORM_SECTION_NAME_PREFIX = 'label_';
	
	
	
	/**
	 * Konstanta s názvem session pro link back
	 * @var string
	 */
//	const LINK_BACK_SESSION = 'link_back';
	
	/**
	 * Názvy routes pro zobrazování sekcí
	 * @var string
	 */
//	const ROUTE_SHOW_SECTION = 'section';
	
	/**
	 * Proměná obsahuje klíč vybrané sekce
	 * @var string
	 */
//	private $selectedSectionKey = null;

    /**
     * Objekt parametru
     * @var UrlParam
     */
//    private $pokusparam = null;

    protected function init() {
//        $this->pokusparam = new UrlParam('pokus', '([0-9]+)');
//        if($this->pokusparam->isValue()){
//            echo "TADY.....................".$this->pokusparam->getValue()."<br>";
//        };
    }

	/**
	 * Kontroler pro obrazení seznamu blogů
	 */
	public function mainController() {
		$this->checkReadableRights();

		$blogLists = new BlogsListModel();

		$textHelper = new TextCtrlHelper();

		$scroll = new ScrollEplugin();
//		$scroll->setCountRecordsOnPage($this->getModule()->getRecordsOnPage());
		$scroll->setCountAllRecords($blogLists->getNumBlogs());
		
		$blogs = $blogLists->getBlogList($scroll->getStartRecord(), $scroll->getCountRecords());


//        parametr v url
        
//
//        $pokusParam = new UrlParam('nahoru', '^nahoru$');
//        if($pokusParam->isValue()){
//            echo "TADY.....................".$pokusParam->getValue()."<br>";
//        }
//
////        normálový parametr
//        $pokusParam2 = new UrlParam('pokusny2');
//        if($pokusParam2->isValue()){
//            echo "TADY.....................".$pokusParam2->getValue()."<br>";
//        }
		
//		foreach ($blogs as $key => $blog){
////            $this->pokusparam->setValue($blog[BlogDetailModel::COLUM_ID]);
//			$blogs[$key][BlogDetailModel::COLUM_TEXT] = $textHelper->removeHtmlTags($blog[BlogDetailModel::COLUM_TEXT]);
//			$blogs[$key][BlogDetailModel::COLUM_URLKEY] = $this->getLink()
//                ->article($blog[BlogDetailModel::COLUM_LABEL], $blog[BlogDetailModel::COLUM_ID]);
////			$blogs[$key][BlogDetailModel::COLUM_URLKEY] = $this->getLink()->article($blog[BlogDetailModel::COLUM_URLKEY], $blog[BlogDetailModel::COLUM_ID]);
//			unset ($link);
//		}

//		$this->container()->addData('BLOGS', $blogs);
//		$this->container()->addEplugin('scroll', $scroll);
//
////		Vytvoření odkazů
//		if($this->getRights()->isWritable()){
//			$this->container()->addLink('LINK_TO_ADD_SECTION', $this->getLink()->action($this->getAction()->addSection()));
//            $this->container()->addLink('LINK_TO_ADD_BLOG', $this->getLink()->action($this->getAction()->addBlog()));
//		}

//		Scrolovátka
//		$scroll = $this->eplugin()->scroll();
//		
//		//		Načtení všech blogů ze všech sekcí
//		$sqlSelectBlogs = $this->getDb()->select()->from(array('blog' => $this->getModule()->getDbTable()))
//												  ->join(array('section' => $this->getModule()->getDbTable(2)), 'blog.'.self::COLUM_ID_SECTION.' = section.'.self::COLUM_SECTION_ID, null, null)
//												  ->where('section.'.self::COLUM_SECTION_ID_ITEM.' = '.$this->getModule()->getId());
//		
//			  										 
////	  	Výpočet scrolovátek									 
//	  	$scroll->setCountRecordsOnPage($this->getModule()->getRecordsOnPage());
//		
//	  	$sqlCount = clone $sqlSelectBlogs;
//	  	$sqlCount = $sqlCount->from(array('blog' => $this->getModule()->getDbTable()), array("count"=>"COUNT(*)"));
//		
//
////	  	Pokud je vybrána sekce je omezen výběr pouze na tuto sekci
//		if($this->selectedSectionKey != null){
//			$sqlSelect->where('sec.'.self::COLUM_SECTION_URLKEY." = '$this->selectedSectionKey'");
//			$sqlCount->where('sec.'.self::COLUM_SECTION_URLKEY." = '$this->selectedSectionKey'");
//		}
//
////		Zjištění počtu záznamů									
//		//		echo $sqlCount;
//		$count = $this->getDb()->fetchObject($sqlCount);
//		$scroll->setCountAllRecords($count->count);				
//	  	
//	  	
////	  	$scroll->setCountAllRecords($this->getDb()->count($this->getModule()->getDbTable()));									 
//
////		načtení je potřebných záznamů
//	  	$sqlSelectBlogs->limit($scroll->getStartRecord(), $scroll->getCountRecords());												  
//												  
//												  
//		$this->errMsg()->addMessage($sqlCount);
//
//		if($this->getRights()->isWritable()){
//			$this->getModel()->linkToAddBlog = $this->getLink()->action($this->getAction()->actionAddblog());
//			$this->getModel()->linkToAddSection = $this->getLink()->action($this->getAction()->actionAddsection());
//		}
		
	}

	public function showController() {
//		echo "<br/><br/><br/>CLANEK<br/><br/>".$this->getArticle();
	}

	/**
	 * Kontroler pro přidání sekce
	 */
	public function addsectionController() {
		$this->checkWritebleRights();

		

//		Podle počtu jazyků inicializujeme pole pro přidání novinky
//		foreach (Locale::getAppLangs() as $lang) {
//			$this->getModel()->sectionArray[$lang] = null;
//		}
//		$obligatoryLang = Locale::getDefaultLang();
//
//
//		if(isset($_POST[self::FORM_SECTION_PREFIX.self::FORM_BUTTON_SEND])){
//			if($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.Locale::getDefaultLang()] == null){
//				$this->errMsg()->addMessage(_('Nebyl zadán povinný název sekce'));
//
//				foreach (Locale::getAppLangs() as $lang) {
//					$this->getModel()->sectionArray[$lang] = htmlspecialchars($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.$lang], ENT_QUOTES);
//				}
//			} else {
//				$sqlInsert = $this->saveNewSection();
//
//				//				Vložení do db
//				if($this->getDb()->query($sqlInsert)){
//					$this->infoMsg()->addMessage(_('Sekce byla uložena'));
//					$this->getLink()->article()->action()->reload();
//				} else {
//					new CoreException(_('Sekci se nepodařilo uložit, chyba při ukládání do db'), 3);
//				}
//			}
//		}
//		$this->getModel()->linkToBack = $this->getLink()->action();
	}
	
	/**
	 * Metoda ukládá sekci do db
	 * 
	 * @return Db -- objekt sql dotazu pro vložení sekce
	 */
	private function saveNewSection() {
		$newSectionName =  htmlspecialchars($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.Locale::getDefaultLang()], ENT_QUOTES);

		//				načtení všech klíču sekcí z db
		$sqlSectionSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(2), self::COLUM_SECTION_URLKEY);

		$sFunction = new SpecialFunctions();
		$newUrlkKey = $sFunction->createDatabaseKey($newSectionName, $this->getDb()->fetchAssoc($sqlSectionSelect));

		unset($sFunction);

		//Vygenerování sloupců do kterých se bude zapisovat
		$columsArrayNames = array();
		$columsArrayValues = array();
		array_push($columsArrayNames, self::COLUM_SECTION_URLKEY);
		array_push($columsArrayValues, $newUrlkKey);
		array_push($columsArrayNames, self::COLUM_SECTION_ID_ITEM);
		array_push($columsArrayValues, $this->getModule()->getId());
		array_push($columsArrayNames, self::COLUM_SECTION_ID_USER);
		array_push($columsArrayValues, $this->getRights()->getAuth()->getUserId());
		array_push($columsArrayNames, self::COLUM_SECTION_TIME);
		array_push($columsArrayValues, time());
		foreach (Locale::getAppLangs() as $lang) {
			array_push($columsArrayNames, self::COLUM_SECTION_LABEL_LANG_PREFIX.$lang);
			array_push($columsArrayValues, htmlspecialchars($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.$lang], ENT_QUOTES));
		}

		$sql = $sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable(2))
										  ->colums($columsArrayNames)
										  ->values($columsArrayValues);
		return $sql;
	}
	

}

?>