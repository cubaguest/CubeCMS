<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class TextController extends Controller {
	
	/**
	 * Názvy sloupců v databázi
	 * @var string
	 */
	const COLUM_ID = 'id_text';
	const COLUM_ID_ITEM = 'id_item';
	const COLUM_CHANGED_TIME = 'changed_time';
	const COLUM_TEXT_LANG_PRFIX = 'text_';

	/**
	 * Názvy imaginárních sloupců
	 * @var string
	 */
	const COLUM_TEXT_IMAG = 'text';
	
	/**
	 * Názvy formůlářových prvků
	 * @var string
	 */
	const FORM_PREFIX = 'text_';
	const FORM_BUTTON_SEND = 'send';
	const FORM_TEXT_PREFIX = 'text_';
	const FORM_TEXT_IN_DB = 'in_db';
	
	/**
	 * Kontroler pro zobrazení textu
	 */
	public function mainController() {
//		Kontrola práv
		$this->checkReadableRights();

//		Model pro načtení textu
		$model = new TextDetailModel();
		$this->container()->addData('text', $model->getText());
		

//		pokud má uživatel právo zápisu vytvoříme odkaz pro editaci
		if($this->getRights()->isWritable()){
			$this->container()->addLink('link_edit', $this->getLink()->action($this->getAction()->actionEdittext()));
		}
		
		
		
//		
//		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(), array(
//			self::COLUM_TEXT_IMAG =>"IFNULL(".self::COLUM_TEXT_LANG_PRFIX.Locale::getLang().",
//			".self::COLUM_TEXT_LANG_PRFIX.Locale::getDefaultLang().")"))
//				->where(self::COLUM_ID_ITEM." = ".$this->getModule()->getId());
//		
//		$this->getModel()->text=$this->getDb()->fetchObject($sqlSelect);							 
//		if($this->getModel()->text != null){
//			$this->getModel()->text=$this->getModel()->text->{self::COLUM_TEXT_IMAG};
//		}			 
//
//		if($this->getRights()->isWritable()){
//			$this->getModel()->link = $this->getLink()->action($this->getAction()->actionEdit());		
//		}
	}

	/**
	 * Kontroler pro editaci textu
	 */
	public function editController() {
		
		$this->checkWritebleRights();
//		$this->createModel("textDetail");
//
////		Uživatelské soubory
//		$files = $this->eplugin()->userfiles();
//		$files->setIdArticle($this->getModule()->getId());
//		$this->getModel()->files = $files;
//
////		Uživatelské obrázky
//		$images = $this->eplugin()->userimages();
//		$images->setIdArticle($this->getModule()->getId());
//		$this->getModel()->images = $images;
//
////		Ukládání textu
//		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_SEND])){
//			if($_POST[self::FORM_PREFIX.self::FORM_TEXT_PREFIX.Locale::getDefaultLang()] == null){
//				$this->errMsg()->addMessage(_('Nebyly zadány všechny potřebné údaje'));
//			} else {
////				Pokud se ukládá změněný text
//				if((bool)$_POST[self::FORM_PREFIX.self::FORM_TEXT_IN_DB]){
//					//				Pole pro vložení
//					$updateArray = array();
//					foreach (Locale::getAppLangs() as $lang) {
//						$_POST[self::FORM_PREFIX.self::FORM_TEXT_PREFIX.$lang] != null ? 
////							$updateArray[self::COLUM_TEXT_LANG_PRFIX.$lang] = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_TEXT_PREFIX.$lang], ENT_QUOTES)
//							$updateArray[self::COLUM_TEXT_LANG_PRFIX.$lang] = htmlspecialchars_decode($_POST[self::FORM_PREFIX.self::FORM_TEXT_PREFIX.$lang], ENT_QUOTES)
//							: $updateArray[self::COLUM_TEXT_LANG_PRFIX.$lang] = null;
//					}
//
//					//				Vložení do d
//					$sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable())
//														 ->set($updateArray)
//														 ->where(self::COLUM_ID_ITEM." = '".$this->getModule()->getId()."'");
//
//				}
////				Ukládá se nový text
//				else {
//					//Vygenerování sloupců do kterých se bude zapisovat
//					$columsArrayNames = array();
//					$columsArrayValues = array();
//					array_push($columsArrayNames, self::COLUM_ID_ITEM);
//					array_push($columsArrayValues, $this->getModule()->getId());
//					array_push($columsArrayNames, self::COLUM_CHANGED_TIME);
//					array_push($columsArrayValues, time());
//					foreach (Locale::getAppLangs() as $lang) {
//						array_push($columsArrayNames, self::COLUM_TEXT_LANG_PRFIX.$lang);
//						array_push($columsArrayValues, htmlspecialchars_decode($_POST[self::FORM_PREFIX.self::FORM_TEXT_PREFIX.$lang], ENT_QUOTES));
////						array_push($columsArrayValues, addslashes($_POST[self::FORM_PREFIX.self::FORM_TEXT_PREFIX.$lang]));
//					}
//
//					$sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable())
//											   ->colums($columsArrayNames)
//											   ->values($columsArrayValues);
//
//				}
//				
////				Vložení do db
//				if($this->getDb()->query($sqlInsert)){
//					$this->infoMsg()->addMessage(_('Text byl uložen'));
//					$this->getLink()->article()->action()->reload();
//				} else {
//					new CoreException(_('Text se nepodařilo uložit, chyba při ukládání do db'), 1);
//				}
//			}
//			
//		}
//		
//		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
//				->where(self::COLUM_ID_ITEM." = ".$this->getModule()->getId());
//				
//		$text = $this->getDb()->fetchObject($sqlSelect);		
//				
//		if($this->getDb()->getNumRows() > 0){
//			$this->getModel()->inDb = true;
//		}
//		
//	//		Podle počtu jazyků inicializujeme pole pro přidání novinky
//		foreach (Locale::getAppLangs() as $lang) {
//			if($text != null){
//				$this->getModel()->textEdit[$lang] = $text->{self::COLUM_TEXT_LANG_PRFIX.$lang};
//			} else {
//				$this->getModel()->textEdit[$lang] = null;
//			}
//			if(isset($_POST[self::FORM_PREFIX.self::FORM_TEXT_PREFIX.$lang]) AND $_POST[self::FORM_PREFIX.self::FORM_TEXT_PREFIX.$lang] != null){
//				$this->getModel()->textEdit[$lang] = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_TEXT_PREFIX.$lang], ENT_QUOTES);
//			}
//		}
	}
	
}

?>