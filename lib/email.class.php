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
    * Řetězec zprávy
    * @var string 
    */
   protected $msgContent = null;

   /**
    * Objekt zprávy
    * @var Swift_Message
    */
   private $message = null;

   private $iconvParams = array();
   
   private $logger = null;
   
   protected $replacements = array();
   
   protected $decoratorVars = array();

   protected $sendFromAddress = null;
   protected $sendFromName = null;
   
   protected $files = array();

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
      if(VVE_DEBUG_LEVEL > 0){
         // To use the ArrayLogger
         $this->logger = new Swift_Plugins_Loggers_ArrayLogger();
         $this->mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($this->logger));
      }
   }

   public function sanitize($str)
   {
      if($str == null){
         return null;
      }
      if(empty ($this->iconvParams)) return $str;
//      return iconv($this->iconvParams[0], $this->iconvParams[1], $str);
      return $str;
   }

   /**
    * Metoda nastaví adresu pro odesílání
    * @param string $from -- adresa pro odesílání
    * @return Email -- vrací sebe
    */
   public function setFrom($address, $name=null)
   {
      $this->sendFromAddress = $address;
      $this->sendFromName = $name;
      return $this;
   }

   /**
    * Metoda nastaví předmět emailu
    * @param string $subject -- předmět emailu
    * @return Email -- vrací sebe
    */
   public function setSubject($subject)
   {
      $this->message()->setSubject(iconv($this->iconvParams[0], $this->iconvParams[1], $subject));
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
      if($merge){
         $this->msgContent .= $content;
      } else {
         $this->msgContent = $content;
      }
      return $this;
   }

   /**
    * Metoda přidá adresu nebo pole adres do příjemců
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
    * Metoda nastaví adresu nebo pole adres do příjemců
    * @param string/array $address -- adresa nebo pole adres pro příjem
    * @return Email -- vrací sebe
    */
   public function setAddress($address, $name = null)
   {
      if(is_array($address)){
         $this->mailsAddresses = $address;
      } else {
         $this->mailsAddresses = array();
         if($name == null){
            $this->mailsAddresses[] = $address;
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
         $file = $file->getName(true);
      }
      $this->files[] = (string)$file;
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
      $this->prepareMessage();
      
      $this->message()->setTo($this->mailsAddresses);
      $ret = $this->mailer->send($this->message(), $failures);
      if($this->logger instanceof Swift_Plugins_Loggers_ArrayLogger && !empty($failures)){
         new CoreErrors(new Swift_SwiftException($this->logger->dump()));
      }
      return $ret;
   }
   public function batchSend(&$failures = null)
   {
      $this->prepareMessage();
      $numSent = 0;
      foreach ($this->mailsAddresses as $address => $name)
      {
         if (is_int($address)) {
            $this->message()->setTo($name);
         } else {
            $this->message()->setTo(array($address => $name));
         }
         $numSent += $this->mailer->send($this->message(), $failures);
         
      }
      if($this->logger instanceof Swift_Plugins_Loggers_ArrayLogger && !empty($failures)){
         new CoreErrors(new Swift_SwiftException($this->logger->dump()));
      }
      return $numSent;
   }
   
   protected function prepareMessage()
   {
      $cnt = $this->msgContent;
      if(!empty($this->decoratorVars )){
         //sanitize decorators
         foreach ($this->decoratorVars as $mail => $values) {
            foreach ($values as $k => $v) {
               $this->decoratorVars[$mail][$k] = $this->sanitize($v);
            }
         }
         $decorator = new Swift_Plugins_DecoratorPlugin($this->decoratorVars);
         $this->mailer->registerPlugin($decorator);
      }
      if(!empty($this->replacements )){
         $cnt = str_replace(array_keys($this->replacements), array_values($this->replacements), $cnt);
      }
      if($this->sendFromAddress != null){
         $this->message()->setSender($this->sendFromAddress, $this->sanitize($this->sendFromName));
         $this->message()->setFrom($this->sendFromAddress, $this->sanitize($this->sendFromName));
      } else {
         $this->message()->setSender(VVE_NOREPLAY_MAIL, $this->sanitize(VVE_WEB_NAME));
         $this->message()->setFrom(VVE_NOREPLAY_MAIL, $this->sanitize(VVE_WEB_NAME));
      }
      
      if(!empty($this->files)){
         foreach ($this->files as $file) {
            $this->message()->attach(Swift_Attachment::fromPath($file));
         }
      }
      
      $this->message()->setBody($this->sanitize($cnt), $this->isHtmlMail == true ? 'text/html' : 'text/plain');
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
      if($this->message == null){
         $this->setMessage(Swift_Message::newInstance());
      }
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
         $this->message()->setFrom(VVE_NOREPLAY_MAIL, $this->sanitize(VVE_WEB_NAME) );
      } else {
         $this->message()->setFrom('noreplay@'.$_SERVER['SERVER_NAME']);
      }
   }
   
   /**
    * Metoda nastaví proměnné emailu
    * @param array $replacements -- pole s proměnnými ('název' => 'hodnota')
    * @param bool $merge -- jestli se ma sloučit s nastavenými
    * @return Email
    */
   public function setReplacements($replacements, $merge = true)
   {
      if($merge){
         $this->replacements = array_merge($this->replacements, $replacements);
      } else {
         $this->replacements = $replacements;
      }
      return $this;
   }
   
   /**
    * Metoda nastaví proměnné emailu podle příjemců
    * @param array $replacements -- pole s proměnnými array( 'mail@mail.cz' => array( 'název' => 'hodnota') )
    */
   public function setRecipientReplacements($replacements)
   {
      $this->decoratorVars = $replacements;
   }

   /**
    * Metoda vrací aktuální podobu HTML mailu. Ovnitř je přepsána konstanta {CONTENT} na zadaný kontent
    * @param string $content -- obsah e-mailu
    * @return string
    */
   public static function getBaseHtmlMail($content) 
   {
      $fileLang = Face::getCurrent()->getDir()."mail".DIRECTORY_SEPARATOR."mail_base_".Locales::getLang().".html";
      $file = Face::getCurrent()->getDir()."mail".DIRECTORY_SEPARATOR."mail_base.html";
      $fileCore = AppCore::getAppLibDir().AppCore::ENGINE_TEMPLATE_DIR.DIRECTORY_SEPARATOR."mail".DIRECTORY_SEPARATOR."mail_base.html";
      
      if(is_file($fileLang)){
         $cnt = file_get_contents($fileLang);
      } else if(is_file($file)){
         $cnt = file_get_contents($file);
      } else if(is_file($fileCore)){
         $cnt = file_get_contents($fileCore);
      } else {
         $cnt =
         '<html>'
         . ' <head>'
         .'<style>
         body { font-family: Verdana, sans-serif; font-size: 0.8em; color:#484848; }
         h1, h2, h3 { font-family: "Trebuchet MS", Verdana, sans-serif; margin: 10px 0; }
         h1 { font-size: 2em; }
         h2 { font-size: 1.5em; }
         h3 { font-size: 1.3em; }
         a, a:link, a:visited { color: #2A5685;}
         a:hover, a:active { color: #c61a1a; }
         hr { width: 100%; height: 1px; background: #ccc; border: 0; }
         table.border { border: 1px solid #2A5685;}
         table th {text-align: left;}
         table th, table td {padding: 2px 3px; }
         .footer { font-size: 0.8em; font-style: italic; }
         </style>'
         . '</head>'
         . ' <body>{CONTENT}'
         . '<p class="footer">Odesláno {DATETIME} ze stránek {WEBSITE}, odeslal klient: {CLIENTINFO}.</p>'
         . '<p class="footer"><strong><em>Tento e-mail je generován automaticky.</em></strong></p>'
         . ' </body>'
         . '</html>';
      }
      
      $cnt = str_replace(
            array(
                  '{CONTENT}',
                  '{DATETIME}',
                  '{WEBSITE}',
                  '{CLIENTINFO}',
                  ), 
            array(
                  $content,
                  vve_date("%X %x"),
                  '<a href="'.Url_Link::getMainWebDir().'" title="'.VVE_WEB_NAME.'">'.VVE_WEB_NAME.'</a>',
                  ( ( isset( $_SERVER['REMOTE_HOST']) && $_SERVER['REMOTE_HOST'] != $_SERVER['REMOTE_ADDRESS'] ) ? 
                        $_SERVER['REMOTE_HOST']." (".$_SERVER['REMOTE_ADDR'].")" : $_SERVER['REMOTE_ADDR']),
                  ), $cnt);
      
      return $cnt;
   }

   /**
    * Metoda vrátí všechny emailové adresy z řetězce
    * @param $string
    * @return array/false
    */
   public static function getEmailsFromString($string){
      preg_match_all("/[\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+/i", $string, $matches);
      return $matches[0];
   }
}
?>
