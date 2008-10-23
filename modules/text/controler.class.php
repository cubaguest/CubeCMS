<?php
/* SVN FILE: $Id$ */
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id$: controller.class.php 3.0.55 27.9.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro obsluhu akcí a kontrolerů modulu
 * 
 * @author $Author$
 * @copyright $Copyright$
 * @version $Revision$
 * @lastrevision $Date$
 * @modifiedby $LastChangedBy$
 * @lastmodified $LastChangedDate$
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
		$this->container()->text = $model->getText();

//		pokud má uživatel právo zápisu vytvoříme odkaz pro editaci
		if($this->getRights()->isWritable()){
			$this->container()->addLink('link_edit', $this->getLink()->action($this->getAction()->actionEdit()));
		}
	}

	/**
	 * Kontroler pro editaci textu
	 */
	public function editController() {
		
		$this->checkWritebleRights();

//		Uživatelské soubory
		$files = $this->eplugin()->userfiles();
		$files->setIdArticle($this->getModule()->getId());
		$this->container()->addEplugin('files', $files);

//		Uživatelské obrázky
		$images = $this->eplugin()->userimages();
		$images->setIdArticle($this->getModule()->getId());
		$this->container()->addEplugin('images', $images);

//		Pole s odeslanými prvky
		$sendArray = array();
		
//		Ukládání textu
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_SEND])){
			if($_POST[self::FORM_PREFIX.self::FORM_TEXT_PREFIX.Locale::getDefaultLang()] == null){
				$this->errMsg()->addMessage(_('Nebyly zadány všechny potřebné údaje'));
			} else {
				$localeHelper = new LocaleCtrlHelper();
				$sendArray = $localeHelper->postsToArray(array(self::FORM_TEXT_PREFIX), self::FORM_PREFIX, LocaleCtrlHelper::SP_CHARS_DECODE);
				unset($localeHelper);
				
				$saveModel = new SaveTextsDetailModel();
				$isSaved = false;
				
				//				Pokud se ukládá změněný text
				if((bool)$_POST[self::FORM_PREFIX.self::FORM_TEXT_IN_DB]){
					$isSaved = $saveModel->saveTextToDb($sendArray, true);
				}
//				Ukládá se nový text
				else {
					$isSaved = $saveModel->saveTextToDb($sendArray, false);
				}
				
//				Vložení do db
				if($isSaved){
					$this->infoMsg()->addMessage(_('Text byl uložen'));
					$this->getLink()->article()->action()->reload();
				} else {
					new CoreException(_('Text se nepodařilo uložit, chyba při ukládání do db'), 1);
				}
			}
		}
		
		//		Model pro načtení textu
		$model = new TextDetailModel();
		$this->container()->addData('text', $model->getAllLangText($sendArray));
		$this->container()->addData('indb', $model->inDb());
	}
}

?>