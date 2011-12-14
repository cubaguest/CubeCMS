<?php
/**
 * Třída pro obsluhu souborů.
 * Třída poskytuje základní metody pro práci se soubory,
 * zjišťování mime typu, ukládání do filesystému, kopírování, mazání.
 *
 * @copyright  	Copyright (c) 2011 Jakub Matas
 * @version    	$Id$ CubeCMS 7.7 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu souborů
 */

class File extends TrObject implements File_Interface {
   /**
    * Název souboru
    * @var string
    */
   protected $name = null;
   
   /**
    * Cesta souboru
    * @var FS_Dir
    */
   protected $path = null;

   /**
    * MIME typ soubou
    * @var string
    */
   protected $mimeType = null;

   /**
    * Pole s MIME typy
    * @var array
    */
   protected static $mimeTypes = array(

       'txt' => 'text/plain',
       'htm' => 'text/html',
       'html' => 'text/html',
       'php' => 'text/html',
       'css' => 'text/css',
       'js' => 'application/javascript',
       'json' => 'application/json',
       'xml' => 'application/xml',
       'swf' => 'application/x-shockwave-flash',
       'flv' => 'video/x-flv',

       // images
       'png' => 'image/png',
       'jpe' => 'image/jpeg',
       'jpeg' => 'image/jpeg',
       'jpg' => 'image/jpeg',
       'gif' => 'image/gif',
       'bmp' => 'image/bmp',
       'ico' => 'image/vnd.microsoft.icon',
       'tiff' => 'image/tiff',
       'tif' => 'image/tiff',
       'svg' => 'image/svg+xml',
       'svgz' => 'image/svg+xml',

       // archives
       'zip' => 'application/zip',
       'rar' => 'application/x-rar-compressed',
       'exe' => 'application/x-msdownload',
       'msi' => 'application/x-msdownload',
       'cab' => 'application/vnd.ms-cab-compressed',

       // audio/video
       'mp3' => 'audio/mpeg',
       'qt' => 'video/quicktime',
       'mov' => 'video/quicktime',

       // adobe
       'pdf' => 'application/pdf',
       'psd' => 'image/vnd.adobe.photoshop',
       'ai' => 'application/postscript',
       'eps' => 'application/postscript',
       'ps' => 'application/postscript',

       // dokumenty
       'csv' => 'text/plain',
       'doc' => 'application/msword',
       'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
       'rtf' => 'application/rtf',
       'xls' => 'application/vnd.ms-excel',
       'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
       'ppt' => 'application/vnd.ms-powerpoint',

       // open office
       'odt' => 'application/vnd.oasis.opendocument.text',
       'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
      
      // ostatní
       'other' => 'application/octet-stream',
   );

   /**
    * Konstruktor třídy
    *
    * @param string $file -- název souboru
    * @param string $path -- (option) cesta k souboru (pokud není zapsána, pokusí se cestu parsovat z názvu souboru)
    * @todo odestranit závislost na Filesystem_Dir
    */   
   public function __construct($name = null, $path = null)
   {
      // Pokud je vložen objekt File
      if($name instanceof File) {
         $this->path = $name->getPath();
         $this->name = $file->getName();
      }
      else if($name instanceof Form_Element_File){
         $elem = $name->getValues();
         $this->name = $elem['name'];
         $this->path = new FS_Dir($elem['path']);
         $this->mimeType = $elem['type'];
      } else {
         if($path != null) {
            $this->name = $file;
            $this->path = new FS_Dir($path);
         } else {
            // rozparsování cesty a soubou u tmp
            $path_parts = pathinfo($name);
            $this->name = $path_parts['basename'];
            $this->path = new FS_Dir($path_parts['dirname']);
         }
      }
//      $this->locateFileSize();
      $this->locateMimeType();
      $this->init();
   }
   
   /**
    * Interní metoda pro inicializaci knihoven pro soubory
    */
   protected function init()
   {}

   /* info o souboru*/

   /**
    * Nastaví název souboru
    * @param string $name -- název souboru
    * @return File 
    */
   public function setName($name)
   {
      $this->name = $name;
      return $this;
   }
   
   /**
    * vrací název souboru
    * @return string 
    */
   public function getName()
   {
      return $this->name;
   }
   
   /**
    * Nastaví cestu k souboru
    * @param string/FS_Dir $path 
    * @return File
    */
   public function setPath($path)
   {
      if($path instanceof FS_Dir){
         $this->path = $path;
      } else {
         $this->path = new FS_Dir($path);
      }
      return $this;
   }
   
   /**
    * Metoda vrací cestu k souboru
    * @return FS_Dir
    */
   public function getPath()
   {
      return $this->path;
   }
   
   /**
    * metoda vrací veliskot souboru
    * @return int
    */
   public function getSize()
   {
      if($this->exist()){
         return @filesize((string)$this);
      }
      return -1;
   }
   
   /**
    * Metoda vrací poslední změnu souboru (timestamp)
    * @return int 
    */
   public function getChangeTime()
   {
      return (int)filectime((string)$this);
   }
   
   /**
    * metoda vrací MIME typ souboru
    * @return type 
    */
   public function getMimeType()
   {
      return $this->mimeType;
   }
   
   /**
    * Metoda nastaví práva k souboru
    * @param int $mode -- octal -- práva souboru např 0777
    */
   public function setRights($mode)
   {
      return @chmod((string)$this, $mode);
   }
   
   public function __toString()
   {
      return (string)$this->getPath().$this->getName();
   }
   
   /* Obsah souboru podle typu */
   
   /**
    * metodfa nastaví obsah souboru
    * @param type $cnt -- obsah
    */
   public function setContent($cnt){}
   
   /**
    * Metoda vrací obsah souboru
    * @return null
    */
   public function getContent(){
      return null;
   }
   
   /**
    * Metoda uloží daný soubor
    * @return File
    */
   public function save() {
      return $this;
   }
   
   /* Modoty pro práci se souborem */
   
   /**
    * Metoda testuje jestli soubor existuje
    * @return bool
    */
   public function exist()
   {
      if($this->getName() == null) return false; // protože pokud není soubor kontroluje adresář a ten může existovat
      return file_exists((string)$this);
   }
   
   /**
    * metoda vytvoří kopii souboru
    * @param type $path
    * @return File 
    */
   public function copy($path)
   {
      if(!$this->exist()){
         return $this;
      }
      
      // Kontrola adresáře
      $path = new FS_Dir($path);
      $path->check();

      // Kontrola jména
      $newFile = $this->creatUniqueName($path);

      if(file_exists((string)$path.$newFile)) {
         throw new UnexpectedValueException(sprintf($this->tr('Soubor %s pro kopírování neexistuje'), (string)$this), 1);
      }
      if(!copy((string)$this, (string)$dirObj.$newFile)) {
         throw new UnexpectedValueException(sprintf($this->tr('Chyba při kopírování souboru %s > %s'), (string)$this, (string)$path.$newFile), 2);
      }
      if(!chmod((string)$dirObj.$newFile, 0666)) {
         throw new UnexpectedValueException(sprintf($this->tr('Chyba při úpravě oprávnění souboru %s'),(string)$this), 3);
      }
      return $this;  
   }
   
   /**
    * Přejmenování souboru
    * @param string $newName -- nový název souboru
    * @return File
    */
   public function rename($newName)
   {
      if(!@rename((string)$this, $this->getPath().$newName)){
         throw new UnexpectedValueException($this->tr('Soubor se nepodařilo přejmenovat'));
      }
      return $this;
   }
   
   /**
    * metoda přesune soubor do jiného adresáře
    * @param FS_Dir $dstDir
    * @return File 
    */
   public function move($dstDir)
   {
      $dstDir = new FS_Dir($dstDir);
      $dstDir->check();
      
      if(!@rename((string)$this, $dstDir.$this->getName())){
         throw new UnexpectedValueException($this->tr('Soubor se nepodařilo přesunout'));
      }
      $this->fileDir = $dstDir;
      return $this;
   }
   
   /**
    * Metoda odešle zadaný soubor ke klientu
    */
   public function send()
   {
      if ($this->exist()) {
         header('Content-Description: File Transfer');
         header('Content-Type: application/octet-stream');
         header('Content-Disposition: attachment; filename='.$this->getName());
         header('Content-Transfer-Encoding: binary');
         header('Expires: 0');
         header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
         header('Pragma: public');
         header('Content-Length: ' . $this->getSize());
         ob_clean();
         flush();
         readfile($file);
      } else {
         header('Content-Description: File Transfer');
         header('Content-Type: application/octet-stream');
         header('Content-Disposition: attachment; filename='.$this->getName());
         header('Content-Transfer-Encoding: binary');
         header('Expires: 0');
         header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
         header('Pragma: public');
         header('Content-Length: ' . strlen($this->getContent()) );
         ob_clean();
         flush();
         echo $this->getContent();
      }
      exit;
   }

   /**
    * Metoda zjistí MIME typ souboru
    *
    * @return string -- mime type
    */
   private function locateMimeType() {
      $path_parts = pathinfo($this->getName(true));
      if($this->exist()) {
         if($this->mimeType === null) {
            //      $file = $this->fileName;
            if (function_exists('finfo_open')) {
               $finfo = finfo_open(FILEINFO_MIME);
               $mimetype = finfo_file($finfo, $this->getName(true));
               // odstranění utf-8 pokud existuje
               $mime = explode(';', $mimetype);
               $this->mimeType = $mime[0];
               finfo_close($finfo);
            }
            else if (array_key_exists($path_parts['extension'], self::$mimeTypes)) {
               $this->mimeType = self::$mimeTypes[$path_parts['extension']];
            }
            else {
               $this->mimeType = 'application/octet-stream';
            }
         }
      } else {
         // tady ujištění typu podle přípony
         if(isset (self::$mimeTypes[$path_parts['extension']])){
            $this->mimeType = self::$mimeTypes[$path_parts['extension']];
         } else {
            $this->mimeType = 'application/octet-stream';
         }
      }
   }

   /**
    * Funkce vytvoří nový název souboru, který je v zadaném adresáři unikátní
    *
    * @param string -- adresář, kde se bude soubor vytvářet
    * @param string -- (option) nový název souboru
    * @param integer -- (option) číslo které se přikládá za soubor
    *
    * @return string -- nový název souboru
    * @todo -- dodělat při přijmu souboru se dvěmi příponami
    */
   protected function creatUniqueName($destinationDir, $newName = null, $number = 0) 
   {
      // Pokud je zadáno pouze číslo
      if(is_int($newName)) {
         $newFileName = $this->getName() ;
         $addNumber = $newName;
      }
      else if($newName != null) {
         $newFileName = $newName;
         $addNumber = $number;
      }
      else {
         $newFileName = $this->getName();
         $addNumber = $number;
      }
      //doplnění posledního lomítka za dest adresář
      if($destinationDir[strlen($destinationDir)-1] != "/" AND $addNumber == 0) {
         $destinationDir .= "/";
      }
      //rozdělení názvu souboru na název a příponu
      $file_ext = array();
//      preg_match('/^([^.]*).([a-z0-9_]+)$/', strtolower($newFileName), $file_ext);
      preg_match('/^[^.]*\.((?:tar\.)?[a-z0-9_]+)$/', strtolower($newFileName), $file_ext);
      $file_name_short = $file_ext[1];
      $file_name_extension = $file_ext[2];
      //odstraneni nepovolenych zanků a složení dohromady
      $file_name_short = vve_cr_safe_file_name($file_name_short);
      unset($sFunction);
      if($addNumber == 0) {
         $createNewFileName=$file_name_short.'.'.$file_name_extension;
      }
      else {
         $createNewFileName=$file_name_short.$addNumber.'.'.$file_name_extension;
      }
      // kontrola existence
      if(file_exists($destinationDir.$createNewFileName)) {
         $createNewFileName = $this->creatUniqueName($destinationDir, (++$addNumber));
      }
      else {
//         $this->fileName = $createNewFileName;
      }
      return $createNewFileName;
   }

}
?>