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
	const COLUM_NEWS_ID_ITEM = 'id_item';
	const COLUM_NEWS_TEXT = 'text';
	
	const COLUM_NEWS_TEXT_LANG_PREFIX = 'text_';
	/**
	 * Názvy formůlářových prvků
	 * @var string
	 */
	const FORM_BUTTON_EDIT = 'edit';
	
	public function mainController() {
		
		//		Kontrola práv
		$this->checkReadableRights();
		
		if(isset($_POST[self::FORM_BUTTON_EDIT])){
			$this->deleteNews();
		}
		
		$this->createModel("TextDetail");
		
		if($this->getRights()->isWritable())
		$this->getModel()->link=$this->getLink()->action($this->getAction()->actionEdittext());
		
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(), array(self::COLUM_NEWS_TEXT => 
				"IFNULL(".self::COLUM_NEWS_TEXT_LANG_PREFIX.Locale::getLang().", ".self::COLUM_NEWS_TEXT_LANG_PREFIX.Locale::getDefaultLang().")"))
				->where(self::COLUM_NEWS_ID_ITEM." = ".$this->getModule()->getId());
		
				$this->getModel()->text=$this->getDb()->fetchAssoc($sqlSelect,true);							 
											 
	}

	public function edittextController() {
		
		$this->checkWritebleRights();
		
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
				->where(self::COLUM_NEWS_ID_ITEM." = ".$this->getModule()->getId());
				
		echo "editujeme";
	}
	
}

?>