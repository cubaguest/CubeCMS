<?php
/**
 * Třída pro obsluhu souborů.
 * Třída poskytuje základní metody pro práci se soubory,
 * zjišťování mime typu, ukládání do filesystému, kopírování, mazání.
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu souborů
 * @todo          Doimplementovat metody "move" a "rename"
 */

class File {
  /**
   * Název výstupního souboru
   * @var string
   */
   private $fileNameOutput = null;

   /**
    * Název vstupního souboru
    * @var string
    */
   private $fileNameInput = null;

   /**
    * název nového souboru
    * @var string
    */
   private $fileNewName = null;

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
    * zadaný MIME typ soubou
    * @var string
    */
   private $fileMimeTypeEntered = null;

   /**
    * Velikost souboru
    * @var integer
    */
   private $fileSize = -1;

   /**
    * Pole s MIME typy
    * @var array
    */
   protected $mimeTypes = array(

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
    * @param string $inputFile -- název vstupního souboru např. z $_FILES pokud
    * je odlišný od výstupního
    * @param string $mimeType -- mime typ souboru
    * @param integer $size -- velikost souboru
    */
   function __construct($file, $dir = null, $inputFile = null, $mimeType = null, $size = null){
      // Pokud je vložen objekt File
      if($file instanceof File){
         $this->fileDir = new File_Dir($file->getFileDir());
         $this->fileNameOutput = $file->getName();
         $this->fileNameInput = $file->getNameInput();
         $this->fileMimeType = $file->getMimeType();
         $this->fileSize = $file->getFileSize();
      } else {
         // Pokud je zadán tmp name
         if($inputFile != null){
            // rozparsování cesty a soubou u tmp
            $arr = $this->parsePathFile($inputFile);
            if($arr != false){
               $this->fileNameInput = $arr[2];
               $this->fileDir = new File_Dir($arr[1]);
            }
            $this->fileNameOutput = $file;
         }
         // pokud je zadán název popřípadě cesta
         else {
            if($dir == null){
               // rozparsování cesty a soubou u tmp
               $arr = $this->parsePathFile($file);
               if($arr != false){
                  $this->fileNameOutput = $arr[2];
                  $this->fileNameInput = $arr[2];
                  $this->fileDir = new File_Dir($arr[1]);
               }
            } else {
               $this->fileNameOutput = $file;
               $this->fileNameInput = $file;
               $this->fileDir = new File_Dir($dir);
            }
         }
         $this->fileMimeTypeEntered = $mimeType;
         $this->fileMimeType = $this->locateMimeType();

         if($size != null){
            $this->fileSize = $size;
         }
      }
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
      if(eregi('^(.*/)([^'.DIRECTORY_SEPARATOR.']*)$', $string, $regep)){
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
      $return = null;
      if($this->fileNewName == null){
         $return = $this->fileNameOutput;
      } else {
         $return = $this->fileNewName;
      }
      if($withDir){
         $return = $this->getFileDir().$return;
      }
      return $return;
   }

   /**
    * Metoda vrací název vstupního (INPUT) souboru
    * @param boolean $withDir -- jestli má být vrácena i část s adresářem
    *
    * @return string -- název souboru
    */
   public function getNameInput($withDir = false) {
      $return = $this->fileNameInput;

      if($withDir){
         $return = $this->getFileDir().$return;
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
      if($this->fileSize == -1){
         $this->fileSize = $this->locateFileSize();
      }
      return $this->fileSize;
   }

   /**
    * Metoda vrací adresář souboru
    *
    * @return string -- adresář souboru
    */
   public function getFileDir() {
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
      $dirObj = new File_Dir();
      $dirObj->checkDir($dstDir);
      unset ($dirObj);

      // Kontrola jména
      $newFile = $this->creatUniqueName($dstDir);
      $this->fileNewName = $newFile;

      if(!$this->exist()){
         throw new UnexpectedValueException(sprintf(_('Soubor %s pro kopírování neexistuje'), $this->getNameInput(true)), 1);
      }
      if(!copy($this->getNameInput(true), $dstDir.$newFile)){
         throw new UnexpectedValueException(sprintf(_('Chyba při kopírování souboru %s > %s'), $this->getNameInput(true), $dstDir.$newFile), 2);
      }
      if(!chmod($dstDir.$newFile, 0666)){
         throw new UnexpectedValueException(sprintf(_('Chyba při úpravě práv souboru %s'),$this->getNameInput(true)), 3);
      }
      return true;
   }

   /**
    * Metoda přejmenuje soubor na nový název
    * @param string $newName -- nový název souboru
    *
    * @return boolean -- true pokud byl soubor přejmenován
    */
   public function rename($newName) {
      ;
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
      if($this->exist() AND !is_dir($this->getNameInput(true))){
         return unlink($this->getNameInput(true));
      }
      throw new UnexpectedValueException(sprintf(_('Soubor %s se nepodařilo smazat z Filesystému'), $this->getNameInput()));
   }

   /**
    * Metoda zjišťuje jestli soubor existuje
    *
    * @return boolean -- true pokud soubor existuje
    */
   public function exist() {
      return file_exists($this->getNameInput(true));
   }

   /**
    * Metoda zjistí a nastaví velikost souboru
    * @return integer -- velikost souboru
    */
   private function locateFileSize() {
      return filesize($this->getNameInput(true));
   }

   /**
    * Metoda zjistí MIME typ souboru
    *
    * @return string -- mime type
    */
   private function locateMimeType() {
      //      $file = $this->fileName;
      $fileArray = explode('.',$this->getName());
      $ext = strtolower(array_pop($fileArray));
      if (array_key_exists($ext, $this->mimeTypes)) {
         return $this->mimeTypes[$ext];
      }
      else if (function_exists('finfo_open')) {
         $finfo = finfo_open(FILEINFO_MIME);
         $mimetype = finfo_file($finfo, $this->getNameInput(true));
         finfo_close($finfo);
         return $mimetype;
      }
      else {
         return 'application/octet-stream';
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
      if(is_int($newName)){
         $newFileName = $this->getName() ;
         $addNumber = $newName;
      } else if($newName != null) {
         $newFileName = $newName;
         $addNumber = $number;
      } else {
         $newFileName = $this->getName();
         $addNumber = $number;
      }
      //doplnění posledního lomítka za dest adresář
      if($destinationDir[strlen($destinationDir)-1] != "/" AND $addNumber == 0){
         $destinationDir .= "/";
      }
      //rozdělení názvu souboru na název a příponu
      $file_ext = array();
      eregi('^([^.]*).(.*)$', strtolower($newFileName), $file_ext);
      $file_name_short = $file_ext[1];
      $file_name_extension = $file_ext[2];
      //odstraneni nepovolenych zanků a složení dohromady
      $sFunction = new Helper_Text();
      $file_name_short = $sFunction->utf2ascii($file_name_short);
      unset($sFunction);
      if($addNumber == 0){
         $createNewFileName=$file_name_short.'.'.$file_name_extension;
      } else {
         $createNewFileName=$file_name_short.$addNumber.'.'.$file_name_extension;
      }
      // kontrola existence
      if(file_exists($destinationDir.$createNewFileName)){
         $createNewFileName = $this->creatUniqueName($destinationDir, (++$addNumber));
      } else {
         $this->fileNewName = $createNewFileName;
      }
      return $createNewFileName;
   }

   /**
    * Metoda nastaví práva k souboru
    * @param int $mode -- octal -- práva souboru např 0777
    */
   public function setRights($mode) {
      @chmod($this->fileDir.$this->fileNameOutput, $mode);
   }
}
?>