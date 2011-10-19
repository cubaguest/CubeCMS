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
 * @abstract      Třída pro práci s emaily (používá SwiftMailer)
 * @todo          Předělat převod zanové sady na plugin BeforeSendListener http://swiftmailer.org/wikidocs/v3/plugindev/beforesendevent
 * @see           http://swiftmailer.org/wikidocs/
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

   /**
    * Objekt zprávy
    * @var Swift_Message
    */
   private $message = null;

   private $iconvParams = array();

   /**
    * Konstruktor. Vytvoří objekt emailu
    * @param boolean $htmlMail -- (option) jestli se bude vytvářet html mail
    */
   public function __construct($htmlMail = false)
   {
      $this->isHtmlMail = $htmlMail;
      if(VVE_SMTP_SERVER != null){
         if(defined("VVE_SMTP_SERVER_ENCRYPT") AND VVE_SMTP_SERVER_ENCRYPT != null){
            $transport = Swift_SmtpTransport::newInstance(VVE_SMTP_SERVER, VVE_SMTP_SERVER_PORT, VVE_SMTP_SERVER_ENCRYPT);
         } else {
            $transport = Swift_SmtpTransport::newInstance(VVE_SMTP_SERVER, VVE_SMTP_SERVER_PORT);
         }
         if(VVE_SMTP_SERVER_USERNAME != null){
            $transport->setUsername(VVE_SMTP_SERVER_USERNAME)->setPassword(VVE_SMTP_SERVER_PASSWORD);
         }
      } else {
         $transport = Swift_MailTransport::newInstance();
      }
      $this->mailer = Swift_Mailer::newInstance($transport);
      $this->setMessage(Swift_Message::newInstance());
   }

   public function sanitize($str)
   {
      if(empty ($this->iconvParams)) return $str;
      return iconv($this->iconvParams[0], $this->iconvParams[1], $str);
   }

   /**
    * Metoda nastaví adresu pro odesílání
    * @param string $from -- adresa pro odesílání
    * @return Email -- vrací sebe
    */
   public function setFrom($address, $name=null)
   {
      $this->message()->setSender($address, $name);
      $this->message()->setFrom($address, $name);
      return $this;
   }

   /**
    * Metoda nastaví předmět emailu
    * @param string $subject -- předmět emailu
    * @return Email -- vrací sebe
    */
   public function setSubject($subject)
   {
      $this->message()->setSubject($this->sanitize($subject));
      return $this;
   }

   /**
    * Metoda nastaví obsah mailu
    * @param string $content -- textový obsah
    * @param boolean $merge -- (option) jestli se má obsah spojit s již existujícím obsahem
    * @return Email -- vrací sebe
    */
   public function setContent($content, $merge = false)
      {
      $cntType = 'text/plain';
      if($this->isHtmlMail == true){
         $cntType = 'text/html';
      }
      if($merge){
         $this->message()->setBody($this->message()->getBody().$this->sanitize($content), $cntType);
      } else {
         $this->message()->setBody($this->sanitize($content), $cntType);
      }
      return $this;
   }

   /**
    * Metoda přidá adresu nebo pole adresa do příjemců
    * @param string/array $address -- adresa nebo pole adres pro příjem
    * @return Email -- vrací sebe
    */
   public function addAddress($address, $name = null)
      {
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
    * Metoda vrací adresy, na které bude mail odeslán
    * @return array
    */
   public function getAddresses()
   {
      return $this->mailsAddresses;
   }

   /**
    * Metoda přidá hlavičku k mailu
    * @param string $header -- hlavička mailu
    * @return Email -- vrací sebe
    */
   public function addHeader($header)
   {
      return $this;
   }

   /**
    * Metoda přidá přílohu k mailu
    * @param File $file -- objekt souboru
    * @return Email -- vrací sebe
    */
   public function addAttachment($file)
   {
      if($file instanceof Filesystem_File){
         $this->message()->attach(Swift_Attachment::fromPath($file->getName(true)));
      } else {
         $this->message()->attach(Swift_Attachment::fromPath($file));
      }
      return $this;
   }

   /**
    * Metoda odešle email
    */
   public function sendMail()
   {
      $this->send();
   }

   public function send(&$failures = null)
   {
      $this->message()->setTo($this->mailsAddresses);
      return $this->mailer->send($this->message(), $failures);
   }
   public function batchSend(&$failures = null)
   {
      $this->message()->setTo($this->mailsAddresses);
      return $this->mailer->batchSend($this->message(), $failures);
   }

   /**
    * metoda vrací objekt zprávy
    * @return Swift_Message
    */
   public function getMessage()
   {
      return $this->message();
   }
   
   /**
    * metoda vrací objekt zprávy
    * @return Swift_Message
    */
   public function message()
   {
      return $this->message;
   }

   /**
    * Metoda nastaví objekt zprávy (např z db/cache)
    * @param Swift_Message $message
    */
   public function setMessage(Swift_Message $message)
   {
      $this->message = $message;
      $this->message()->setEncoder(Swift_Encoding::get8BitEncoding());
      switch (Locales::getLang()) {
         case 'cs': // many czech people use very old mail interface
            $this->message()->setCharset('iso-8859-2');
            $this->iconvParams = array(0 => 'UTF-8', 1 => 'iso-8859-2//TRANSLIT');
            break;
         default:
            break;
      }
      if(VVE_NOREPLAY_MAIL != null) {
         $this->message()->setFrom(VVE_NOREPLAY_MAIL);
      } else {
         $this->message()->setFrom('noreplay@'.$_SERVER['SERVER_NAME']);
      }
   }
}
?>
