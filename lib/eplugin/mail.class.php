<?php
/**
 * Třída EPluginu pro práci s maily, kterým se budou odesílat infomace ze stránky
 * Třída slouží pro správu emailových adres a úpravu samotného emailu, který
 * se bude odesílat a plnit daty z modulu (například formulář)
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE3.9.5 $Revision: $
 * @author        $Author: $ $Date:$
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída Epluginu pro práci s maily k odesílání informací
 * @todo          Dodělat podporu pro více druhů výrazů (ne jenom %vyraz%,
 * %vyraz[true/false]%) u tvorby rozhraní emailu
 */

class MailEplugin extends Eplugin {
   /**
    * Název primární šablony s posunovátky
    * @var string
    */
   protected $templateFile = array('mail.tpl');

   /**
    * Název databázové tabulky se změnama
    * @var string
    */
   const DB_TABLE_SENDMAILS = 'eplugin_sendmails';
   const DB_TABLE_MAILSTEXT = 'eplugin_sendmailstexts';

   /**
    * Počet posledních změn zobrazených ve výstupu
    * @var integer
    */
   const COUNT_OF_LAST_CHANGES = 1;

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID             = 'id_mail';
   const COLUMN_ID_ITEM        = 'id_item';
   const COLUMN_ID_ARTICLE     = 'id_article';
   const COLUMN_ID_MAIL_TEXT   = 'id_text';
   const COLUMN_MAIL           = 'mail';
   const COLUMN_MAIL_TEXT      = 'text';
   const COLUMN_MAIL_SUBJECT	 = 'subject';
   const COLUMN_MAIL_REPLAY	 = 'replay_mail';

   /**
    * Formulářová prvky
    * @var string
    */
   const FORM_PREFIX          = 'sendmail_';
   const FORM_PREFIX_CONTENT  = 'content_';
   const FORM_MAIL            = 'newmail';
   const FORM_BUTTON_SEND     = 'newmailsend';

   const FORM_BUTTON_SEND_TEXT= 'send';
   const FORM_BUTTON_DELETE   = 'delete';
   const FORM_ID              = 'id';
   const FORM_MAIL_TEXT       = 'text';
   const FORM_MAIL_SUBJECT    = 'subject';
   //	const FORM_MAIL_REPLAY = 'replay_mail';

   /**
    * Typ hodnoty v  překladové tabulce (normální)
    */
   const TRANSLATE_TYPE_NORMAL = 1;

   /**
    * Typ hodnoty v  překladové tabulce (boolean)
    */
   const TRANSLATE_TYPE_BOOLEAN = 2;

   /**
    * Název parametru pro konstruktor s id item
    */
   const CONSTRUCT_PARAM_ID_ITEM = 'idItem';

   /**
    * Pole s maily
    * @var array
    */
   private $mailsArray = array();
   private static $otherMails = array();

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
    * pole s detailem mailu
    * @var array
    */
   private $mailTextDetail = array();
   private static $mailTextDetailOthers = array();

   /**
    * Proměná s překladovou tabulkou
    * @var array
    */
   private $mailTextTransTable = array();
   private static $mailTextTransTableOthers = array();

   /**
    * Id článku pokud je zadáno
    * @var integer
    */
   private $idArticle = null;

   /**
    * Jestli se dá upravovat text
    * @var boolean
    */
   private $isTexts = true;
   private static $isTextsOthers = array();

   /**
    * Id item
    * @var integer 
    */
   private $idItem = false;

   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    */
   protected function init($paramsArr = array()){
      if(isset ($paramsArr[self::CONSTRUCT_PARAM_ID_ITEM])){
         $this->idItem = $paramsArr[self::CONSTRUCT_PARAM_ID_ITEM];
      }

      $this->checkMailOperation();
      //Načtení dat
      $this->getMailsFromDb();
      $this->getMailDetails();
      // Vytvoření základní překladové tabulky
      $this->createBasicTranslateValues();

   }

   /**
    * Metoda kontroluje, pokud byl mail přidáván nebo mazán
    */
   private function checkMailOperation() {
      $newMailForm = new Form(self::FORM_PREFIX);
      $newMailForm->crInputText(self::FORM_MAIL, true, false, Form::VALIDATE_EMAIL)
      ->crSubmit(self::FORM_BUTTON_SEND);

      if($newMailForm->checkForm()){
         if(!$this->saveNewMail($newMailForm->getValue(self::FORM_MAIL))){
            throw new UnexpectedValueException(_('Chyba při ukládání nového e-mailu'),1);
         }
         $this->infoMsg()->addMessage(_('E-mail byl uložen'));
         $this->getLinks()->reload();
      }

      //		Mail je ukládán
      $deleteMailForm = new Form(self::FORM_PREFIX);
      $deleteMailForm->crInputHidden(self::FORM_ID, true, 'is_numeric')
      ->crSubmit(self::FORM_BUTTON_DELETE);

      if($deleteMailForm->checkForm()){
         if(!$this->deleteMail($deleteMailForm->getValue(self::FORM_ID))){
            throw new UnexpectedValueException(_('E-mail se nepodařilo vymazat'),2);
         }
         $this->infoMsg()->addMessage(_('E-mail byl smazán'));
         $this->getLinks()->reload();
      }

      //		Je ukládán nový text mailu
      $contentForm =  new Form(self::FORM_PREFIX.self::FORM_PREFIX_CONTENT);
      $contentForm->crSubmit(self::FORM_BUTTON_SEND_TEXT)
      ->crInputText(self::FORM_MAIL_SUBJECT, true)
      ->crTextArea(self::FORM_MAIL_TEXT, true);

      if($contentForm->checkForm()){
         if(!$this->saveMailContent($contentForm->getValue(self::FORM_MAIL_SUBJECT),
               $contentForm->getValue(self::FORM_MAIL_TEXT))){
            throw new CoreException(_('Text E-mailu se nepodařilo uložit'),3);
         }
         $this->infoMsg()->addMessage(_('Text e-mailu byl uložen'));
         $this->getLinks()->reload();
      }
   }

   /**
    * Metoda uloží text mailu do db
    * @param string -- předmět
    * @param string -- text
    */
   private function saveMailContent($subject, $text){
      if($this->contentRecordExist()){
         $sqlinsert = $this->getDb()->update()->table(self::DB_TABLE_MAILSTEXT)
         ->set(array(self::COLUMN_MAIL_SUBJECT => $subject,
               self::COLUMN_MAIL_TEXT => $text, self::COLUMN_ID_ARTICLE => $this->idArticle))
         ->where(self::COLUMN_ID_ITEM, $this->getModule()->getId());
      } else {
         $sqlinsert = $this->getDb()->insert()
         ->table(self::DB_TABLE_MAILSTEXT, true)
         ->colums(self::COLUMN_ID_ITEM, self::COLUMN_MAIL_SUBJECT, self::COLUMN_MAIL_TEXT,
            self::COLUMN_ID_ARTICLE)
         ->values($this->getModule()->getId(), $subject, $text, $this->idArticle);
      }
      //		uložení
      return $this->getDb()->query($sqlinsert);
   }

   /**
    * Metoda zjistí jesli byl text majlu už uložen a existuje záznam v DB
    */
   private function contentRecordExist() {
      $sql = $this->getDb()->select()->table(self::DB_TABLE_MAILSTEXT)
      ->colums(array('count' => 'COUNT(*)'))
      ->where(self::COLUMN_ID_ITEM, $this->getModule()->getId());

      if($this->idArticle != null){
         $sql->where(self::COLUMN_ID_ARTICLE, $this->idArticle);
      }
      $count = $this->getDb()->fetchObject($sql);
      if($count->count > 0){
         return true;
      }
      return false;
   }

   /**
    * Metoda vrací detail o textu mailu
    */
   public function getMailDetails() {
      $sqlSelect = $this->getDb()->select()
      ->table(self::DB_TABLE_MAILSTEXT)
      ->where(self::COLUMN_ID_ITEM, $this->getModule()->getId());

      if($this->idArticle != null){
         $sqlSelect->where(self::COLUMN_ID_ARTICLE, $this->idArticle);
      }

      $mailDetail = $this->getDb()->fetchAssoc($sqlSelect, true);
      if(!empty($mailDetail)){
         $this->mailTextDetail = $mailDetail;
      } else {
         $this->mailTextDetail = null;
      }
   }

   /**
    * Metoda vrací seznam registrovaných adres
    * @return array -- pole adres
    */
   public function getMailAddress() {
      if(empty ($this->mailsArray)){
         $this->getMailsFromDb();
      }
      $mails = array();
      foreach ($this->mailsArray as $mail) {
         array_push($mails, $mail[self::COLUMN_MAIL]);
      }
      return $mails;
   }

   /**
    * Metoda uloží mail do db
    * @param string -- mail
    * @param integer -- (option) id item u ktré byl mail uložen
    */
   private function saveNewMail($mail) {
      $sqlInser = $this->getDb()->insert()
      ->table(self::DB_TABLE_SENDMAILS);
      
      if($this->idItem == false){
         if($this->idArticle == null){
            $sqlInser->colums(array(self::COLUMN_MAIL, self::COLUMN_ID_ITEM))
            ->values($mail, $this->getModule()->getId());
         } else {
            $sqlInser->colums(array(self::COLUMN_MAIL, self::COLUMN_ID_ITEM, self::COLUMN_ID_ARTICLE))
            ->values($mail, $this->getModule()->getId(), $this->idArticle);
         }
      } else {
         if($this->idArticle == null){
            $sqlInser->colums(array(self::COLUMN_MAIL, self::COLUMN_ID_ITEM))
            ->values($mail, $this->idItem);
         } else {
            $sqlInser->colums(array(self::COLUMN_MAIL, self::COLUMN_ID_ITEM, self::COLUMN_ID_ARTICLE))
            ->values($mail, $this->idItem, $this->idArticle);
         }
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
      $sqlDel = $this->getDb()->delete()
      ->table(self::DB_TABLE_SENDMAILS)
      ->where(self::COLUMN_ID, $idMail);

      return $this->getDb()->query($sqlDel);
   }

   /**
    * Metoda odešle mail na všechny přiřazené emaily
    */
   public function sendMails() {
      //	vytvoření zprávy
      $mailText = $this->translateMailText();
      $mailHelper = new MailHelper();
      //	odeslání na registrované adresy
      foreach ($this->mailsArray as $mail) {
         $mailHelper->sendMail($mail[self::COLUMN_MAIL], $this->mailTextDetail[self::COLUMN_MAIL_SUBJECT],
            $mailText);
      }
   }

   /**
    * Metoda nastaví jestli se u mailu používají texty nebo ne
    * @param boolean $isText -- true pokud používají
    */
   public function setIsMailText($isText = true) {
      $this->isTexts = $isText;
   }

   /**
    * Metoda nastaví id itemu pokud má být odlišné od id modulu. Pokud je id 0 tak
    * jsou mejly zobrazenu u všech modulů používající tento plugin
    * @param int $id -- id modulu
    */
   public function setIdItem($id = false) {
      $this->idItem = $id;
   }

   /**
    * Metoda doplní hodnoty do textu mailu a vrátí jej
    * @return string -- text mailu
    */
   private function translateMailText() {
      $mail = $this->mailTextDetail[self::COLUMN_MAIL_TEXT];

      foreach ($this->mailTextTransTable as $transName => $transValue) {
         // Pokud je možnost vybírat ze dvou hodnot %vyraz[true/false]% je vygenerována hodnota
         if($transValue['type'] == 2 ){
            //$array = array ();
            if($transValue['value']){
               $mail = preg_replace('/%+'.$transName.'\[+([^\/]{1,})\/+([^\/\]]{1,})\]+%+/','\\1', $mail);
            }else{
               $mail = preg_replace('/%+'.$transName.'\[+([^\/]{1,})\/+([^\/\]]{1,})\]+%+/','\\2', $mail);
            }
         }
         // Jedná-li se o normální výraz
         else {
            $mail = str_replace('%'.$transName.'%', $transValue['value'], $mail);
         }
      }
      return $mail;
   }

   /**
    * Metoda načte maily z db
    */
   private function getMailsFromDb() {
      $sqlSelect = $this->getDb()->select()
      ->table(self::DB_TABLE_SENDMAILS)
      ->colums(array(self::COLUMN_ID, self::COLUMN_MAIL))
      ->where(self::COLUMN_ID_ITEM, $this->getModule()->getId(),'=',Db::COND_OPERATOR_OR)
      ->where(self::COLUMN_ID_ITEM, '0');

      if($this->idArticle != null){
         $sqlSelect->where(self::COLUMN_ID_ARTICLE, $this->idArticle);
      }
      $this->mailsArray = $this->getDb()->fetchAll($sqlSelect);
      $this->numberOfReturnRows = $this->getDb()->getNumRows();
   }

   /*
    * Metody pro tvorbu a práci s překladovou tabulkou
    */

   /**
    * Metoda vytvoří prvek v translate tabulce, kterou lze editovat v textu emailu
    * @param string $name -- název hodnoty
    * @param string $label -- (option) popis hodnoty
    * @param int $type -- (option) typ hodnoty - konstanta TRANSLATE_TYPE_XXX
    * @return MailEplugin -- vrací samu sebe
    */
   public function crTrValue($name, $label = null, $type = self::TRANSLATE_TYPE_NORMAL) {
      $this->mailTextTransTable[$name]['label'] = $label;
      $this->mailTextTransTable[$name]['type'] = $type;
      return $this;
   }

   /**
    * Metoda nastavuje hodnotu danému prvku v translate tabulce
    * @param string $name -- název prvku
    * @param string/integer $value -- (option) hodnota prvku default: null
    * @return MailEplugin -- vrací samu sebe
    */
   public function setTrValue($name, $value = null) {
      if(!isset ($this->mailTextTransTable[$name])){
         throw new UnexpectedValueException(_('Požadovaná hodnota neexistuje'), 4);
      }
      $this->mailTextTransTable[$name]['value'] = $value;
      return $this;
   }

   /**
    * Metoda vytvoří zákldaní prvky v překladové tabulce. Momentálně pouze
    * IP adresa klienta a čas odesílání emailu
    */
   private function createBasicTranslateValues() {
      // prvek s ip adresou
      $this->crTrValue('ipaddress', _('IP adresa odesílatele'))
      ->setTrValue('ipaddress', $_SERVER['REMOTE_ADDR']);
      // čas odeslání mailu
      $this->crTrValue('sendtime', _('Čas odeslání emailu'))
      ->setTrValue('sendtime', time());
   }

   /**
    * Metoda obstarává přiřazení proměných do šablony
    *
    */
   protected function assignTpl(){
      //		Načtení detailu mailu
      self::$mailTextDetailOthers[$this->getIdTpl()] = $this->mailTextDetail;
      //		self::$mailTextinDbOthers[$this->idSendMails] = $this->mailTextInDb;
      $this->toTpl("MAIL_TEXT_DETAIL", self::$mailTextDetailOthers);

      //		List s povolenými texty
      self::$isTextsOthers[$this->getIdTpl()] = $this->isTexts;
      $this->toTpl("IS_MAIL_TEXT", self::$isTextsOthers);

      //		List se seznamem povolených hodnot
      self::$mailTextTransTableOthers[$this->getIdTpl()] = $this->mailTextTransTable;
      $this->toTpl("MAIL_TAGS", self::$mailTextTransTableOthers);
      $this->toTpl("TAGS", _('Povolené tagy'));

      //		Popisky přidávání emailů
      $this->toTpl("MAILS_LABEL", _("E-mailové adresy"));
      $this->toTpl("EDIT_MAIL_LABEL", _("Úprava e-mailu"));
      $this->toTpl("MAIL_FORM_SHOW", _("Detaily e-mailu"));
      $this->toTpl("MAIL_FORM_SHOW_SUBNAME", $this->getTplSubName());

      $this->toTpl("NOT_ANY_MAIL", _("Nebyl uložen žádný e-mail"));

      // prvky formulářů
      $this->toTpl("MAIL_NAME", _("E-mail"));
      $this->toTpl("BUTTON_SENDMAIL_SEND", _("Uložit"));
      $this->toTpl("BUTTON_SENDMAIL_DELETE", _("Smazat"));
      $this->toTpl("CONFIRM_MESAGE_DELETE_MAIL", _('Smazat e-mail'));

      //	popisky pro editaci emailu
      $this->toTpl("MAIL_SUBJECT", _("Předmět e-mailu"));
      $this->toTpl("MAIL_CONTENT", _("Text e-mailu"));
      //		$this->toTpl("MAIL_REPLAY_MAIL", _("Adresa pro odpověď e-mailu"));


      self::$otherNumberOfReturnRows[$this->getIdTpl()] = $this->numberOfReturnRows;
      $this->toTpl("SENDMAILS_NUM_ROWS", self::$otherNumberOfReturnRows);

      $this->toTpl("SENDMAILS_ID", $this->idSendMails);

      self::$otherMails[$this->getIdTpl()] = $this->mailsArray;
      $this->toTpl("SENDMAILS_ARRAY",self::$otherMails);

      $jQuery = new JQuery();
      $jQuery->addWidgentTabs();
      $this->toTplJSPlugin($jQuery);

      $this->toTplJSPlugin(new SubmitForm());
   }
}
?>
