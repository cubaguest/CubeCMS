<?php
/**
 * EPlugin pro kládání mailů na které se bude odesílat
 * 
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	SendMail class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: sendmail.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída Epluginu pro práci s maily k odesílání informací
 */

class SendMail extends Eplugin {
	/**
	 * Název primární šablony s posunovátky
	 * @var string
	 */
	protected $templateFile = array('editmail.tpl','sendmail.tpl');

	/**
	 * Název databázové tabulky se změnama
	 * @var string
	 */
	const DB_TABLE_SENDMAILS = 'sendmails';
	const DB_TABLE_MAILSTEXT = 'sendmailstexts';
	
	/**
	 * Počet posledních změn zobrazených ve výstupu
	 * @var integer
	 */
	const COUNT_OF_LAST_CHANGES = 1;
	
	/**
	 * Názvy sloupců v db
	 * @var string
	 */
	const COLUM_ID				= 'id_mail';
	const COLUM_ID_ITEM			= 'id_item';
	const COLUM_MAIL			= 'mail';
	const COLUM_MAIL_TEXT		= 'text';
	const COLUM_MAIL_SUBJECT	= 'subject';
	const COLUM_MAIL_REPLAY		= 'replay_mail';
	
	/**
	 * Formulářová prvky
	 * @var string
	 */
	const FORM_PREFIX = 'sendmail_';
	const FORM_MAIL = 'mail';
	const FORM_BUTTON_SEND = 'send';
	const FORM_BUTTON_SEND_TEXT = 'send_text';
	const FORM_BUTTON_DELETE = 'delete';
	const FORM_ID = 'id';
	const FORM_MAIL_IN_DB = 'in_db';
	const FORM_MAIL_TEXT = 'text';
	const FORM_MAIL_SUBJECT = 'subject';
	const FORM_MAIL_REPLAY = 'replay_mail';
	
	/**
	 * Sekce v configu s informacemi o tabulkách
	 * @var string
	 */
	const CONFIG_TABLES_SECTIONS = 'db_tables';
	
	/**
	 * Konstanty s názvy hodnot a překladů pro překlad šablony
	 * @var string
	 */
	const MAIL_TPL_STRING_TRANS = 'string';
	const MAIL_TPL_VALUE_TRANS = 'value';
	const MAIL_TPL_LABEL_TRANS = 'label';
	
	/**
	 * Pole s maily
	 * @var array
	 */
	private $mailsArray = array();
	private static $otherMails = array();
	
	/**
	 * Pole s id modulu (items)
	 * @var array
	 */
	private $idItems = null;
	
	/**
	 * ID změny v šabloně
	 * @var ineger
	 */
	private $idSendMails = 1;
	
	/**
	 * Počet vrácených záznamů
	 * @var integer
	 */
	private $numberOfReturnRows = 0;
	private static $otherNumberOfReturnRows = array();
	
	/**
	 * Odeslaný email
	 * @var string
	 */
	private $sendMail = null;
	
	/**
	 * pole s detailem mailu
	 * @var array
	 */
	private $mailTextDetail = array();
	private static $mailTextDetailOthers = array();
	
	/**
	 * Jestli je text mailu už uložen v db
	 * @var boolean
	 */
	private $mailTextInDb = false;
	private static $mailTextinDbOthers = array();
	
	/**
	 * Proměná s překladovou tabulkou
	 * @var array
	 */
	private $mailTextTransTable = array();
	
	/**
	 * Metoda inicializace, je spuštěna pří vytvoření objektu  
	 *
	 */
	protected function init(){
//		Načtení struktury emailu z db
	}
	
	/**
	 * Metoda nastaví id šablony pro výpis
	 * @param ineger -- id šablony (jakékoliv)
	 */
	public function setIdTpl($id) {
		$this->idSendMails = $id;
	}
	
	/**
	 * Metoda kontroluje, pokud byl mail přidáván nebo mazán
	 *
	 */
	public function checkMailOperation() {
//		Mail je ukládán
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_SEND])){
			$mailHelper = new MailCtrlHelper();
			
			$this->sendMail=$_POST[self::FORM_PREFIX.self::FORM_MAIL];
			
//			Jestli není mail prázdný
			if($this->sendMail == null){
				$this->errMsg()->addMessage(_('Nebyl zadán žádný e-mail'));
			} 
//			Jestli byl zadán skutečně mail
			else if(!$mailHelper->checkMail($this->sendMail)){
				$this->errMsg()->addMessage(_('Nebyl zadán korektní e-mail'));
			} 
//			Mail je OK
			else {
				if($this->createMail($this->sendMail)){
					
					$this->infoMsg()->addMessage(_('E-mail byl uložen'));
					$this->pageReload();
				} else {
					new CoreException(_('E-mail se nepodařilo uložit'),1);
//					$this->errMsg()->addMessage(_('Mail se nepodařilo uložit'));
				}
				
			}
			unset($mailHelper);
		}
		
//		Mail je mazán
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_DELETE])){
			$id = $_POST[self::FORM_PREFIX.self::FORM_ID];
			
			if(!is_numeric($id)){
				new CoreException(_('Nebylo správně přeneseno id e-mailu'), 2);
			} else {
				if($this->deleteMail($id)){
					$this->infoMsg()->addMessage(_('E-mail byl smazán'));
					$this->pageReload();
				} else {
					new CoreException(_('E-mail se nepodařilo smazat'), 3);
				}
			}
			
		}
		
//		Je ukládán nový text mailu
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_SEND_TEXT])){
			if($_POST[self::FORM_PREFIX.self::FORM_MAIL_TEXT] == null OR
				$_POST[self::FORM_PREFIX.self::FORM_MAIL_SUBJECT] == null){
				$this->errMsg()->addMessage(_('Nebyly zadány všechny potřebné údaje'));	
			} else {
				$subject = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_MAIL_SUBJECT]);
				$text = $_POST[self::FORM_PREFIX.self::FORM_MAIL_TEXT];
				$replayMail = $_POST[self::FORM_PREFIX.self::FORM_MAIL_REPLAY];
				$inDb = (bool)htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_MAIL_IN_DB]);
				
				if($this->saveMailText($subject,$text,$replayMail, $inDb)){
					$this->infoMsg()->addMessage(_('Text e-mailu byl uložen'));
					$this->pageReload();
				} else {
					$this->errMsg()->addMessage(_('Chyba při ukládání textu e-mailu'));
				}
				
			}
		}
	}
	
	/**
	 * Metoda uloží text mailu do db
	 * @param string -- předmět
	 * @param string -- text
	 * @param string -- email pro odpověď
	 * @param boolean -- false pokud záznam v db není
	 */
	private function saveMailText($subject, $text, $replay, $inDb){
		if($inDb){
			$sqlinsert = $this->getDb()->update()->table(self::DB_TABLE_MAILSTEXT)
									   ->set(array(self::COLUM_MAIL_SUBJECT => $subject, self::COLUM_MAIL_TEXT => $text, self::COLUM_MAIL_REPLAY => $replay))
									   ->where(self::COLUM_ID_ITEM.' = '.$this->getModule()->getId());
			
		} else {
			$sqlinsert = $this->getDb()->insert()->into(self::DB_TABLE_MAILSTEXT)
									   ->colums(self::COLUM_ID_ITEM, self::COLUM_MAIL_SUBJECT, self::COLUM_MAIL_TEXT, self::COLUM_MAIL_REPLAY)
									   ->values($this->getModule()->getId(), $subject, $text,$replay);
		}
		
//		uložení
		return $this->getDb()->query($sqlinsert);
	}
	
	/**
	 * Metoda nastaví pole pro výpis nápovědyk překladové tabulce
	 * @param array -- pole s překlady
	 */
	public function setTransTable(&$transTable) {
		$this->mailTextTransTable = &$transTable;
	}
	
	/**
	 * Metoda znovunačte stránku
	 */
	private function pageReload() {
		$link = new Links();
		$link->reload();
		exit();
	}
	
	/**
	 * Metoda načte maily a id
	 * 
	 * @return array -- pole s maily a id
	 */
	public function getMails() {
		$this->getDataFromDb();
		
//		return $this->mailsArray;
	}
	
	/**
	 * Metoda načte maily
	 * 
	 * @return array -- pole pouze s maily
	 */
	public function getOnlyMails() {
		$this->getDataFromDb();
		
		$returnArray = array();
		foreach ($this->mailsArray as $mail) {
			array_push($returnArray, $mail[self::COLUM_MAIL]);
		}
		
		return $returnArray;
	}
	
	/**
	 * Metoda vrací detail o textu mailu
	 *
	 * @return array -- pole s detaily mailu
	 */
	public function getMailDetails() {
		$sqlSelect = $this->getDb()->select()->from(self::DB_TABLE_MAILSTEXT)
								   ->where(self::COLUM_ID_ITEM.' = '.$this->getModule()->getId());
								   
		$mailDetail = $this->getDb()->fetchAssoc($sqlSelect, true);
		if(!empty($mailDetail)){
			$this->mailTextDetail = $mailDetail;
			$this->mailTextInDb = true;
			return $mailDetail;
		} else {
			return false;
		}
	}
	
	
	/**
	 * Metoda vrací mail s doplněnými částmi s překládací tabulky
	 * @return string -- detail mailu
	 */
	public function getTranslateMailDetail() {
		$this->getMailDetails();
		
		$mailDetail = $this->mailTextDetail;
		
		$text = &$mailDetail[self::COLUM_MAIL_TEXT];
		
		foreach ($this->mailTextTransTable as $translation) {
			$text = str_replace($translation[self::MAIL_TPL_STRING_TRANS], $translation[self::MAIL_TPL_VALUE_TRANS], $text);
		}
		return $mailDetail;
	}
	
	
	/**
	 * Metoda uloží mail do db
	 * @param string -- mail
	 * @param integer -- (option) id item u ktré byl mail uložen
	 */
	public function createMail($mail, $idItem = null) {
		$sqlInser = $this->getDb()->insert()->into(self::DB_TABLE_SENDMAILS);
		
		if($idItem == null){
			$sqlInser = $sqlInser->colums(self::COLUM_MAIL, self::COLUM_ID_ITEM)
								->values($mail, $this->getModule()->getId());
		} else {
			$sqlInser = $sqlInser->colums(self::COLUM_MAIL, self::COLUM_ID_ITEM)
								->values($mail, $idItem);
		}
		
//		vložení záznamu
		return $this->getDb()->query($sqlInser);
	}
	
	/**
	 * Metoda vymaže mail
	 *
	 * @param integer -- id mailu
	 */
	public function deleteMail($idMail) {
		//			vymazání z db
		$sqlDel = $this->getDb()->delete()->from(self::DB_TABLE_SENDMAILS)
										  ->where(self::COLUM_ID.' = '.$idMail)
										  ->where(self::COLUM_ID_ITEM.' = '.$this->getModule()->getId());
										  
		return $this->getDb()->query($sqlDel);
	}
	
	
	/**
	 * Metoda načte data z db
	 */
	private function getDataFromDb() {
		$sqlSelect = $this->getDb()->select()->from(self::DB_TABLE_SENDMAILS, array(self::COLUM_ID, self::COLUM_MAIL))
											 ->where(self::COLUM_ID_ITEM.' = '.$this->getModule()->getId());
		
		$this->mailsArray = $this->getDb()->fetchAssoc($sqlSelect);
		$this->numberOfReturnRows = $this->getDb()->getNumRows();
	}
	
	/**
	 * Metoda vytvoří řetězec s povolenými hodnotami
	 * @return array -- pole se tagy
	 */
	private function generateTagsLabels() {
		return $this->mailTextTransTable;
	}
	
	/**
	 * Metoda obstarává přiřazení proměných do šablony
	 *
	 */
	protected function assignTpl(){
//		popisky pro editaci emailu
		$this->toTpl("MAIL_SUBJECT", _("Předmět e-mailu"));
		$this->toTpl("MAIL_TEXT", _("Text e-mailu"));
		$this->toTpl("MAIL_REPLAY_MAIL", _("Adresa pro odpověď e-mailu"));
		
//		Načtení detailu mailu
//		self::$mailTextDetailOthers[$this->idSendMails] = $this->mailTextDetail;
//		self::$mailTextinDbOthers[$this->idSendMails] = $this->mailTextInDb;
		$this->toTpl("MAIL_TEXT_DETAIL", $this->mailTextDetail);
		$this->toTpl("MAIL_TEXT_IN_DB", $this->mailTextInDb);
		
//		List se seznamem povolených hodnot
		$this->toTpl("MAIL_TAGS", $this->generateTagsLabels());
		$this->toTpl("TAGS", _('Povolené tagy'));
		
//		Popisky přidávání emailů
		$this->toTpl("SENDMAIL_LABEL_NAME", _("E-maily pro odesílání"));
		$this->toTpl("BUTTON_SENDMAIL_SEND", _("Uložit"));
		$this->toTpl("MIAL_NAME", _("E-maily"));
		$this->toTpl("BUTTON_SENDMAIL_DELETE", _("Smazat"));
		$this->toTpl("NOT_ANY_MAIL", _("Nebyl uložen žádný e-mail"));
		$this->toTpl("SEND_MAIL", $this->sendMail);
		$this->toTpl("CONFIRM_MESAGE_DELETE", _('Smazat e-mail'));

		self::$otherNumberOfReturnRows[$this->idSendMails] = $this->numberOfReturnRows;
		$this->toTpl("SENDMAILS_NUM_ROWS", self::$otherNumberOfReturnRows);
		
		$this->toTpl("SENDMAILS_ID", $this->idSendMails);
		
		self::$otherMails[$this->idSendMails] = $this->mailsArray;
		$this->toTpl("SENDMAILS_ARRAY",self::$otherMails);
		
		$this->toTplJSPlugin(new SubmitForm());
	}
}
?>