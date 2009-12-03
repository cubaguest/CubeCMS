<?php
/**
 * Třída pro obsluhu souborů.
 * Třída poskytuje základní metody pro práci se soubory,
 * zjišťování mime typu, ukládání do filesystému, kopírování, mazání.
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: file.class.php 636 2009-07-07 20:17:18Z jakub $ VVE3.9.4 $Revision: 636 $
 * @author        $Author: jakub $ $Date: 2009-07-07 22:17:18 +0200 (Út, 07 čec 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-07-07 22:17:18 +0200 (Út, 07 čec 2009) $
 * @abstract 		Třída pro obsluhu souborů
 * @todo          Doimplementovat metody "move" a "rename"
 */

class Filesystem_File {
   /**
    * Název souboru
    * @var string
    */
   private $fileName = null;

   /**
    * Adresář souboru
    * @var File_Dir
    */
   private $fileDir = null;

   /**
    * MIME typ soubou
    * @var string
    */
   private $fileMimeType = null;

   /**
    * Velikost souboru
    * @var integer
    */
   private $fileSize = -1;

   /**
    * Jestli se mají všechny chyby reportovat
    * @var boolean
    */
   private $reportErrors = true;

   /**
    * Proměná obsahuje jestli při práci se souborem vznikla chyb (pro použití v if)
    * @var bool
    */
   protected $isError = false;

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

       // ms office
       'doc' => 'application/msword',
       'rtf' => 'application/rtf',
       'xls' => 'application/vnd.ms-excel',
       'ppt' => 'application/vnd.ms-powerpoint',

       // open office
       'odt' => 'application/vnd.oasis.opendocument.text',
       'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
   );

   /**
    * Konstruktor třídy
    *
    * @param string $file -- název souboru (může být i s cestou - sám si to rozparsuje)
    * @param string $dir -- (option) cesta k souboru (pokud není zapsána, pokusí se cestu
    * parsovat z názvu souboru)
    */
   function __construct($file, $dir = null, $reportErrors = true) {
      // Pokud je vložen objekt File
      if($file instanceof Filesystem_File) {
         $this->fileDir = new Filesystem_Dir($file->getDir());
         $this->fileName = $file->getName();
      }
      else if($file instanceof Form_Element_File){
         $elem = $file->getValues();
         $this->fileName = $elem['name'];
         $this->fileDir = new Filesystem_Dir($elem['dir']);
         $this->fileMimeType = $elem['type'];
      } else {
         if($dir == null) {
            // rozparsování cesty a soubou u tmp
            $arr = $this->parsePathFile($file);
            if($arr != false) {
               $this->fileName = $arr[2];
               $this->fileDir = new Filesystem_Dir($arr[1]);
            }
         }
         else {
            $this->fileName = $file;
            $this->fileDir = new Filesystem_Dir($dir);
         }
      }
      $this->setReportErrors($reportErrors);
      $this->locateFileSize();
      $this->locateMimeType();
   }

   /**
    * Metoda pro převod na řetězec (vrací název souboru)
    * @return string -- název souboru
    */
   public function  __toString() {
      return $this->getName();
   }

   /**
    * Metoza zapíná nebo vypíná report chyb
    * @param bool $rep -- true pro zapnutí reportování chyb
    */
   public function setReportErrors($rep = true) {
      $this->reportErrors = $rep;
   }

   /**
    * Metoda vrací jestli je zapnuté reportování chyb
    * @return bool
    */
   public function reportErrors() {
      return $this->reportErrors;
   }

   /**
    * Metoda vrací jestli při zpracování vznikla chyba
    * @return bool -- true pokud vznikla chyba
    */
   public function isError() {
      return $this->isError;
   }

   /**
    * Metoda vrací objekt s informačními zprávami
    * @return Messages -- objekt zpráv
    */
   final protected function infoMsg() {
      return AppCore::getInfoMessages();
   }

   /**
    * Metoda vrací objekt s chybovými zprávami
    * @return Messages -- objekt zpráv
    */
   final protected function errMsg() {
      return AppCore::getUserErrors();
   }

   /**
    * Metoda rozparsuje řetězec na název souboru a cestu
    * @param string $string -- název souboru s cestou
    */
   private function parsePathFile($string) {
      $regep = array();
      if(eregi('^(.*/)([^'.DIRECTORY_SEPARATOR.']*)$', $string, $regep)) {
         return $regep;
      }
      return false;
   }

   /**
    * Metoda vrací název výstupního (OUTPUT) souboru, pokud existuje nový vrací ten
    * @param boolean $withDir -- jestli má být vrácena i část s adresářem
    *
    * @return string -- název souboru
    */
   public function getName($withDir = false) {
      $return = $this->fileName;
      if($withDir) {
         $return = $this->getDir().$return;
      }
      return $return;
   }

   /**
    * Metoda vrací mime typ souboru
    * @todo nutná portace na PECL rozšíření o informací o souboru
    *
    * @return string -- mime typ souboru
    */
   public function getMimeType() {
      return $this->fileMimeType;
   }

   /**
    * Metoda vrací velikost souboru
    *
    * @return integer -- velikost souboru
    */
   public function getFileSize() {
      if($this->fileSize == -1) {
         $this->fileSize = $this->locateFileSize();
      }
      return $this->fileSize;
   }

   /**
    * Metoda vrací adresář souboru
    *
    * @return Filesystem_Dir -- objekt s adresář souboru
    */
   public function getDir() {
      return $this->fileDir;
   }

   /**
    * Metoda kopíruje soubor do zadaného adresáře, kontroluje jméno a vytváří
    * unikátní název
    * @param string $dstDir -- cílový adresář
    * @param string $fileName -- (option) název souboru
    *
    * @return boolean -- true pokud byl soubor zkopírován
    */
   public function copy($dstDir, $fileName = null) {
      // Kontrola adresáře
      $dirObj = new Filesystem_Dir();
      $dirObj->checkDir($dstDir);
      unset ($dirObj);

      // Kontrola jména
      $newFile = $this->creatUniqueName($dstDir);
      $this->fileName = $newFile;

      if(!$this->exist()) {
         throw new UnexpectedValueException(sprintf(_('Soubor %s pro kopírování neexistuje'), $this->getName(true)), 1);
      }
      if(!copy($this->getName(true), $dstDir.$newFile)) {
         throw new UnexpectedValueException(sprintf(_('Chyba při kopírování souboru %s > %s'), $this->getName(true), $dstDir.$newFile), 2);
      }
      if(!chmod($dstDir.$newFile, 0666)) {
         throw new UnexpectedValueException(sprintf(_('Chyba při úpravě práv souboru %s'),$this->getName(true)), 3);
      }
      return true;
   }

   /**
    * Metoda přejmenuje soubor na nový název
    * @param string $newName -- nový název souboru
    *
    * @return boolean -- true pokud byl soubor přejmenován
    */
   public function rename($targetDir, $newName) {

   }

   /**
    * Metoda přesune soubor do zadaného adresáře, vytvoří unikátní název nebo
    * přejmenuje na zadaný název
    * @param string $dstDir -- cílový adresář
    * @param string $newName -- (option) nový název souboru
    *
    * @return boolean -- true pokud byl soubor přesunut
    */
   public function move($dstDir, $newName = null) {
      ;
   }

   /**
    * Metoda vymaže soubor z flesystému
    *
    * @return boolean -- true pokud byl soubor odstraněn
    */
   public function remove() {
      if($this->exist() AND !is_dir($this->getName(true))) {
         if(!@unlink($this->getName(true))) {
            new CoreErrors(new UnexpectedValueException(sprintf(_('Soubor %s se nepodařilo smazat z Filesystému'), $this->getName())));
         }
      }

   }

   /**
    * Metoda zjišťuje jestli soubor existuje
    *
    * @return boolean -- true pokud soubor existuje
    */
   public function exist() {
      return file_exists($this->getName(true));
   }

   /**
    * Metoda zjistí a nastaví velikost souboru
    * @return integer -- velikost souboru
    */
   private function locateFileSize() {
      return @filesize($this->getName(true));
   }

   /**
    * Metoda zjistí MIME typ souboru
    *
    * @return string -- mime type
    */
   private function locateMimeType() {
      if($this->fileMimeType === null) {
      //      $file = $this->fileName;
         $fileArray = @explode('.',$this->getName());
         $ext = strtolower(array_pop($fileArray));
         if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $this->fileMimeType = finfo_file($finfo, $this->getName(true));
            finfo_close($finfo);
         }
         else if (array_key_exists($ext, self::$mimeTypes)) {
               $this->fileMimeType = self::$mimeTypes[$ext];
            }
            else {
               $this->fileMimeType = 'application/octet-stream';
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
   protected function creatUniqueName($destinationDir, $newName = null, $number = 0) {
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
      eregi('^([^.]*).(.*)$', strtolower($newFileName), $file_ext);
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
         $this->fileName = $createNewFileName;
      }
      return $createNewFileName;
   }

   /**
    * Metoda nastaví práva k souboru
    * @param int $mode -- octal -- práva souboru např 0777
    */
   public function setRights($mode) {
      @chmod($this->getName(true), $mode);
   }
}
?>