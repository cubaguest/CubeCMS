<?php
/**
 * Třída pro tvorbu a odesílání emailů
 * Třída implementuje řešení pro tvorbu emailů a připojování příloh a jeho odesílání
 * přes webserver (sendmail,...). Umí vytvářet jak textové tak html emaily
 * a připojovat neomezený počet příloh.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id$ VVE4.0.1 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro práci s emaily
 */
// jádro
require_once AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR
                      .'nonvve'.DIRECTORY_SEPARATOR."swift".DIRECTORY_SEPARATOR."swift_required.php";
class Email {

   /**
    * Jestli je vytvářen html mail
    * @var boolean
    */
   private $isHtmlMail = false;

   /**
    *
    * @var string[]
    */
   private $mailsAddresses = array();

   /**
    *
    * @var Swift_Mailer
    */
   private $mailer = null;

   private $message = null;
   
   private $iconvParams = array();

   /**
    * Konstruktor. Vytvoří objekt emailu
    * @param boolean $htmlMail -- (option) jestli se bude vytvářet html mail
    */
   public function __construct($htmlMail = false) {
      $this->isHtmlMail = $htmlMail;
      if(VVE_SMTP_SERVER != null){
         $transport = Swift_SmtpTransport::newInstance(VVE_SMTP_SERVER, VVE_SMTP_SERVER_PORT);
         if(VVE_SMTP_SERVER_USERNAME != null){
            $transport->setUsername(VVE_SMTP_SERVER_USERNAME)->setPassword(VVE_SMTP_SERVER_PASSWORD);
         }
      } else {
         $transport = Swift_MailTransport::newInstance();
      }
      $this->mailer = Swift_Mailer::newInstance($transport);
      $this->message = Swift_Message::newInstance();
      $this->message->setEncoder(Swift_Encoding::get8BitEncoding());
      switch (Locales::getLang()) {
         case 'cs': // many czech people use very old mail interface
            $this->message->setCharset('iso-8859-2');
            $this->iconvParams = array('UTF-8', 'iso-8859-2//TRANSLIT');
            break;
         default:
            break;
      }
      if(VVE_NOREPLAY_MAIL != null) {
         $this->message->setFrom(VVE_NOREPLAY_MAIL);
      } else {
         $this->message->setFrom('noreplay@'.$_SERVER['SERVER_NAME']);
      }
   }
   
   private function safeStr($str) {
      if(empty ($this->iconvParams)) return $str;
      return iconv($this->iconvParams[0], $this->iconvParams[1], $content);
   }

   /**
    * Metoda nastaví adresu pro odesílání
    * @param string $from -- adresa pro odesílání
    * @return Email -- vrací sebe
    */
   public function setFrom($address, $name=null) {
      $this->message->setFrom($address, $name);
      return $this;
   }

   /**
    * Metoda nastaví předmět emailu
    * @param string $subject -- předmět emailu
    * @return Email -- vrací sebe
    */
   public function setSubject($subject) {
      $this->message->setSubject($this->safeStr($subject));
      return $this;
   }

   /**
    * Metoda nastaví obsah mailu
    * @param string $content -- textový obsah
    * @param boolean $merge -- (option) jestli se má obsah spojit s již existujícím obsahem
    * @return Email -- vrací sebe
    */
   public function setContent($content, $merge = false) {
      $cntType = 'text/plain';
      if($this->isHtmlMail == true){
         $cntType = 'text/html';
      }
      if($merge){
         $this->message->setBody($this->message->getBody().$this->safeStr($content), $cntType);
      } else {
         $this->message->setBody($this->safeStr($content), $cntType);
      }
      return $this;
   }

   /**
    * Metoda přidá adresu nebo pole adresa do příjemců
    * @param string/array $address -- adresa nebo pole adres pro příjem
    * @return Email -- vrací sebe
    */
   public function addAddress($address, $name = null) {
      if(is_array($address)){
         $this->mailsAddresses = array_merge($this->mailsAddresses, $address);
      } else {
         if($name == null){
            array_push($this->mailsAddresses, $address);
         } else {
            $this->mailsAddresses[$address] = $name;
         }
      }
      return $this;
   }

   /**
    * Metoda přidá hlavičku k mailu
    * @param string $header -- hlavička mailu
    * @return Email -- vrací sebe
    */
   public function addHeader($header) {
      return $this;
   }

   /**
    * Metoda přidá přílohu k mailu
    * @param File $file -- objekt souboru
    * @return Email -- vrací sebe
    */
   public function addAttachment(Filesystem_File $file) {
      $this->message->attach(Swift_Attachment::fromPath($file->getName(true)));
      return $this;
   }

   /**
    * Metoda odešle email
    */
   public function sendMail() {
      $this->send();
   }

   public function send() {
      $this->message->setTo($this->mailsAddresses);
      return $this->mailer->send($this->message);
   }
   public function batchSend() {
      $this->message->setTo($this->mailsAddresses);
      return $this->mailer->batchSend($this->message);
   }
}
?>
