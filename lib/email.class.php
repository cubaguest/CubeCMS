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
class Email {

   /**
    * Jestli je vytvářen html mail
    * @var boolean
    */
   private $isHtmlMail = false;

   /**
    * Předmět zprávy
    * @var string
    */
   private $mailSubject = 'php mail';

   /**
    * Kontrolní řetězec mailu
    * @var string
    */
   private $mimeBoundary = null;

   /**
    * Pole s adresami pro odeslání
    * @var array
    */
   private $mailAddress = array();

   /**
    * Název schránky ze které se odesílá
    * @var string
    */
   private $mailFrom = null;

   /**
    * Název odesílatele mailu
    * @var string
    */
   private $mailFromName = null;

   /**
    * Samotný obsah mailu
    * @var string
    */
   private $mailContent = null;

   /**
    * Pole souborů pro připojení
    * @var array
    */
   private $mailAttachments = array();

   /**
    * Kódování zprávy
    * @var string
    */
   private $mailCharset = 'utf-8';

   /**
    * Základní hlavičky pro odeslání emailu
    * @var array
    */
   private $mailHeaders = array();

   /**
    * Konstruktor. Vytvoří objekt emailu
    * @param boolean $htmlMail -- (option) jestli se bude vytvářet html mail
    */
   public function __construct($htmlMail = false) {
      $this->isHtmlMail = $htmlMail;
      $semi_rand = md5(time());
      $this->mimeBoundary = "==Multipart_Boundary_x{$semi_rand}x";
      $this->mailFrom = 'noreplay@'.$_SERVER['SERVER_NAME'];
   }

   /**
    * Metoda nastaví adresu pro odesílání
    * @param string $from -- adresa pro odesílání
    * @return Email -- vrací sebe
    */
   public function setFrom($from) {
      $this->mailFrom = $from;
      return $this;
   }

   /**
    * Metoda nastaví název odesílatele pro odesílání
    * @param string $fromName -- název odesílatele
    * @return Email -- vrací sebe
    */
   public function setFromName($fromName) {
      $this->mailFromName = $fromName;
      return $this;
   }

   /**
    * Metoda nastaví předmět emailu
    * @param string $subject -- předmět emailu
    * @return Email -- vrací sebe
    */
   public function setSubject($subject) {
      $this->mailSubject = $subject;
      return $this;
   }

   /**
    * Metoda nastaví obsah mailu
    * @param string $content -- textový obsah
    * @param boolean $merge -- (option) jestli se má obsah spojit s již existujícím obsahem
    * @return Email -- vrací sebe
    */
   public function setContent($content, $merge = false) {
      if($merge){
         $this->mailContent .= $content;
      } else {
         $this->mailContent = $content;
      }
      return $this;
   }

   /**
    * Metoda nastaví kódování zprávy
    * @param string $charset -- (option) kódování zprávy výchozí je 'utf-8'
    */
   public function setCharset($charset = 'utf-8') {
      $this->mailCharset = $charset;
   }

   /**
    * Metoda přidá adresu nebo pole adresa do příjemců
    * @param string/array $address -- adresa nebo pole adres pro příjem
    * @return Email -- vrací sebe
    */
   public function addAddress($address) {
      if(is_array($address)){
         $this->mailAddress = array_merge($this->mailAddress, $address);
      } else {
         array_push($this->mailAddress, $address);
      }
      return $this;
   }

   /**
    * Metoda přidá hlavičku k mailu
    * @param string $header -- hlavička mailu
    * @return Email -- vrací sebe
    */
   public function addHeader($header) {
      array_push($this->mailHeaders, $header);
      return $this;
   }

   /**
    * Metoda přidá přílohu k mailu
    * @param File $file -- objekt souboru
    * @return Email -- vrací sebe
    */
   public function addAttachment(Filesystem_File $file) {
      array_push($this->mailAttachments, $file);
      return $this;
   }

   /**
    * Metoda odešle email
    */
   public function sendMail() {
      // předmět
      $encodedSubject="=?$this->mailCharset?B?".base64_encode($this->mailSubject)."?=\n";
      // tělo emailu
      $mailContent = $this->createMail();
      // hlavičky
      $headers = $this->createHeaders();

      foreach ($this->mailAddress as $address) {
         if(!mail($address, $encodedSubject, $mailContent, $headers)){
            throw new CoreException(sprintf(_('Chyba při odesílání emailu na adresu %s'),$address));
         }
      }
      return true;
   }

   /**
    * Privátní metody pro tvorbu mailu
    * @return string -- tělo mailu
    */
   private function createMail() {
      $mail = null;

      $mail = "\nThis is a multi-part message in MIME format.\n\n" . "--{$this->mimeBoundary}\n";
      // jestli je html
      if($this->isHtmlMail){
         $mail .= "Content-Type: text/html; charset=\"{$this->mailCharset}\"\n";
      } else {
         $mail .= "Content-Type: text/plain; charset=\"{$this->mailCharset}\"\n";
      }
      $mail .= "Content-Transfer-Encoding: 8bit\n\n";
//      $mail . "\n\n";

      // Tělo zprávy e-mailu
//      $mail .= "\n".$this->mailContent."\n\n";
      $mail .= $this->mailContent."\r\n";

      // přidání příloh
      foreach ($this->mailAttachments as $attachment) {
//         $attachment = new File();
//         $fileatt = "priloha/$name.$pripona"; // Cesta k souboru
//         $fileatt_type = "$contentType"; // Typ souboru
//         $fileatt_name = $_FILES["priloha"]["name"]; // Název souboru připojeného k e-mailu v příloze

         $file = fopen($attachment->getNameInput(true),'rb');
         $data = fread($file,$attachment->getFileSize());
         fclose($file);
         $data = chunk_split(base64_encode($data));

         $mail .= "--{$this->mimeBoundary}\n" .
                  "Content-Type: {$attachment->getMimeType()};\n" .
                  " name=\"{$attachment->getName()}\"\n" .
                  "Content-Disposition: attachment;\n" .
                  " filename=\"{$attachment->getName()}\"\n" .
                  "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
      }
      // konec zprávy
      $mail .= "--{$this->mimeBoundary}--\n";
      return $mail;
   }

   /**
    * Metoda vytvoří řetězec s hlavičkami mailu
    * @return string -- hlavičky
    */
   private function createHeaders() {
      $headers = null;
      if($this->mailFromName != null){
         $headers = "From: " . $this->mailFromName . "<" . $this->mailFrom . ">\n";
      } else {
         $headers = "From: <" . $this->mailFrom . ">\n";
      }

      $headers .= "MIME-Version: 1.0\n" .
                  "Content-Type: multipart/mixed;\n" .
                  " boundary=\"{$this->mimeBoundary}\"\n";
      $headers .= "X-Sender: ".$this->mailFrom."\n";
      $headers .= "Reply-To: ".$this->mailFrom."\n";
      $headers .= "X-Mailer: PHP\n";
      $headers .= "X-Priority: 3";

      // přidání uživatelských hlaviček
      foreach ($this->mailHeaders as $header) {
         $headers .= $header;
      }

      return $headers;
   }

}
?>
