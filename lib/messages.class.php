<?php
/**
 * Trida pro práci se zprávami.
 * Metoda slouží pro shromažďování zpráv, jak chbových tak informačních. Tyto
 * zpráva jsou většinou vypisovány na výstup nebo ukládány.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro obsluhu hlášek
 *
 * @todo refaktoring
 * @todo implementovat ukládání do souboru
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
    * @var string
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
   function addMessage($messageText, $save = null){
      if($save === true){
         if(!in_array($messageText, $this->messagesForSave)){
            array_push($this->messagesForSave, $messageText);
         }
      } else if($save === false){
         if(!in_array($messageText, $this->messages)){
            array_push($this->messages, $messageText);
         }
      } else {
         if($this->defaultSaveMessages){
            if(!in_array($messageText, $this->messagesForSave)){
               array_push($this->messagesForSave, $messageText);
            }
         } else {
            if(!in_array($messageText, $this->messages)){
               array_push($this->messages, $messageText);
            }
         }
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
      }
      return false;
   }

   /**
    * Funce pro vraceni pole se zpravami
    *
    * @return array -- pole se zpravami
    */
   function getMessages(){
      $messages = $this->messages;
      return $messages;
   }

   /**
    * Funkce vypise pole se spravami na standartni vystup
    */
   function getMessagesPrint(){
      foreach ($this->messages as $key => $value) {
         echo $this->messagePrefixForPrint.$value."<br>\n";
      }
   }

   /**
    * Funkce ulozi pole se spravami do session¨,
    * pro pozdejsi nacteni
    *
    * @param string -- nazev session
    */
   private function saveMessages(){
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
   private function setSaveTarget($target, $name = null){
      if ($target == "session"){
         $this->saveTarget = $target;
         $this->saveTargetName = $name;
      }
   }
}
?>