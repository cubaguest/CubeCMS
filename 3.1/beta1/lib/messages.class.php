<?php
/**
 * Trida pro praci se zpravami,
 * slouzi pro jejich uchovani a vypis,
 * popripade dalsi vlastnosti
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	Messages class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: messages.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída pro obsluhu hlášek
 * //TODO refaktoring
 */
class Messages {
	/**
	 * Pole se všemi zprávami
	 * @var array
	 */
	private $messages = array();

	/**
	 * Pole s uloženými zprávami
	 * @var array
	 */
	private $messagesSaved = array();
	
	/**
	 * Pole se zprávami, které se uloží
	 * @var array
	 */
	private $messagesForSave = array();
	
	/**
	 * Jestli se má zpráva ukládat a zobrazovat po obnovení stránky
	 * @var boolean
	 */
	private $defaultSaveMessages = false;
	
	/**
	 * Kam se mají zprávy ukládat (pro zobrazení zprávy po obnovení stránky)
	 * (informační zprávy)
	 * @var string;
	 */
	private $saveTarget = null;
	
	/**
	 * Název cíle pro uložení zprávy
	 * @var string
	 */
	private $saveTargetName = null;

	/**
	 * Objetk se session
	 * @var Sessions
	 */
	private $session = null;
	
	/**
	 * Konstruktor tridy
	 *
	 * @param string -- prefix pro výpis zpráv (option)
	 * @param boolean -- jestli se zpráva má ukládat a zobrazovat po obnovení stránky
	 */
	function __construct($saveTarget = 'session', $saveTargetName = 'message', $saveDefault = false){
		if($saveTarget == 'session'){	
			$this->session = new Sessions();
		}
//		Nastavení ukládání zpráv		
		$this->setSaveTarget($saveTarget, $saveTargetName);
		$this->defaultSaveMessages = $saveDefault;
		$this->getSavedMessages();
	}

	/**
	 * Destruktor - uloží zprávy pro uložení
	 *
	 */
	function __destruct() {
		$this->saveMessages();
	}
	
	
	/**
	 * Metoda vrací uložené zprávy
	 */
	private function getSavedMessages() {
		
		if($this->saveTarget == "session"){
			$this->messagesSaved = $this->session->get($this->saveTargetName);
			$this->session->remove($this->saveTargetName); 
		}
		
		if(!empty($this->messagesSaved)){
			foreach ($this->messagesSaved as $msg){
				array_push($this->messages, $msg);
			}
		}
	}
	
	
	/**
	 * Funce pridava zpravu do pole se zpravami
	 *
	 * @param String -- text zpravy
	 * @param boolean -- jesli se má zpráva uložit
	 */
	function addMessage($messageText, $save = false){
		if($this->defaultSaveMessages OR $save){
			array_push($this->messagesForSave, $messageText);
		} else {
			array_push($this->messages, $messageText);
		}
	}

	/**
	 * Funkce zjistuje jestli je pole se zpravami prazdne
	 *
	 * @return boolean -- true jestlize neni vlozena zadna zprava
	 */
	function isEmpty(){
		if (sizeof($this->message) == 0){
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Funce pro vraceni pole se zpravami
	 *
	 * @return array -- pole se zpravami
	 */
	function getMessages(){
		
		$messages = $this->messages;
		
		/**
		 * Sloučení uložených a zobrazovaných zpráv
		 */
		
		return $messages;
	}

	/**
	 * Funkce vypise pole se spravami na standartni vystup
	 *
	 */
	function getMessagesPrint(){
		foreach ($this->messages as $key => $value) {
			echo $this->messagePrefixForPrint.$value."<br>\n";
		}
	}

	/**
	 * Funkce nastavi prefix při výpisu chybové hlášky
	 *
	 * @param string -- prefix chybove zpravy (napr.: CHYBA: )
	 */
//	function setMesagePrefixForPrint($messagePrefix){
//		$this->messagePrefixForPrint = $messagePrefix;
//	}
//
//	/**
//	 * Funkce vraci prefix, ktery je vypisoven pri vystupu
//	 * pred chybovou zpravu
//	 *
//	 * @return string -- prefix chybove zpravy
//	 */
//	function getMesagePrefixForPrint(){
//		return $this->messagePrefixForPrint;
//	}

	/**
	 * Funkce ulozi pole se spravami do session¨,
	 * pro pozdejsi nacteni
	 *
	 * @param string -- nazev session
	 */
	private function saveMessages()
	{
		if($this->saveTarget == "session"){
			$this->session->add($this->saveTargetName, $this->messagesForSave);
		}
	}

	/**
	 * Funkce nastavuje cil, kde se maji ulozit zpravy,
	 * napriklad do session se jmenem
	 *
	 * @param string -- cil ulozeni
	 * @param string -- jmeno uloziste (neni povinne)
	 */
	private function setSaveTarget($target, $name = null)
	{
		if ($target == "session"){
			$this->saveTarget = $target;
			$this->saveTargetName = $name;
		}
	}

}
?>