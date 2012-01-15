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
         $this->name = $name->getName();
      }
      else if($name instanceof Form_Element_File){
         $elem = $name->getValues();
         $this->name = $elem['name'];
         $this->path = new FS_Dir($elem['path']);
         $this->mimeType = $elem['type'];
      } else {
         if($path != null) {
            $this->name = $name;
            $this->path = new FS_Dir($path);
         } else if(is_array ($name)) {
            // rozparsování cesty a soubou u tmp
            $this->name = $name['name'];
            if($name['path'] instanceof FS_Dir){
               $this->path = $name['path'];
            } else {
               $this->path = new FS_Dir((string)$name['path']);
            }
         } else if($name != null) {
            // rozparsování cesty a soubou u tmp
            $path_parts = pathinfo($name);
            $this->name = $path_parts['basename'];
            if($path_parts['dirname'] != '.'){
               $this->path = new FS_Dir($path_parts['dirname']);
            } else {
               $this->path = new FS_Dir();
            }
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
    * Metoda vrací příponu souboru
    * @return string
    */
   public function getExtension()
   {
      $fileParts = pathinfo((string)$this);
      return isset ($fileParts['extension']) ? strtolower($fileParts['extension']) : null;
   }


   /**
    * Metoda nastaví práva k souboru
    * @param int $mode -- octal -- práva souboru např 0777
    */
   public function setRights($mode)
   {
      return @chmod((string)$this, $mode);
   }
   
   /**
    * metoda vrací třídu jako řetězec. Tedy soubor i s cestou
    * @return string 
    */
   public function __toString()
   {
      return (string)$this->getPath().$this->getName();
   }
   
   /* Obsah souboru podle typu */
   
   /**
    * metodfa nastaví obsah souboru
    * @param string $data -- obsah
    */
   public function setData($data){
      return $this;
   }
   
   /**
    * Metoda vrací obsah souboru
    * @return null
    */
   public function getData(){
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
    * @param string/Fs_Dir $path -- cesta pro kopii
    * @param bool $returnNewObj -- jestli se má vrátit objekt nového souboru
    * @param string $newFile -- (option) nový název souboru
    * @return File 
    */
   public function copy($path, $returnNewObj = false, $newFile = null)
   {
      $obj = $this;
      if($returnNewObj == true){
//         $obj = unserialize(serialize($this)); // THIS IS SLOW !!!
         $obj = clone($this);
      }
      if(!$this->exist()){
         return $obj;
      }
      // Kontrola adresáře
      $path = new FS_Dir($path);
      $path->check();

      // Kontrola jména
      if($newFile == null){
         $newFile = $obj->creatUniqueName((string)$path);
      }
      if(!@copy((string)$this, (string)$path.$newFile)) {
         throw new File_Exception(sprintf($this->tr('Chyba při kopírování souboru %s > %s'), (string)$this, (string)$path.$newFile), 2);
      }
      if($returnNewObj == true){
         $obj->setPath($path);
         $obj->setName($newFile);
      }
      if(!chmod((string)$obj, 0666)) {
         throw new File_Exception(sprintf($this->tr('Chyba při úpravě oprávnění souboru %s'),(string)$obj), 3);
      }
      return $obj;  
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
    * Meroda odstraní daný soubor ze serveru
    */
   public function delete()
   {
      if($this->exist() && !@unlink((string)$this)){
         throw new File_Exception(sprintf( $this->tr('Soubor %s se nepodařilo smazat.'), (string)$this ) );
      }
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
      $path_parts = pathinfo((string)$this);
      if($this->exist()) {
         if($this->mimeType === null) {
            //      $file = $this->fileName;
            if (function_exists('finfo_open')) {
               $finfo = finfo_open(FILEINFO_MIME);
               $mimetype = finfo_file($finfo, (string)$this);
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
         if(isset ($path_parts['extension']) && isset (self::$mimeTypes[$path_parts['extension']])){
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
   protected function creatUniqueName($destinationDir) 
   {
      $fn = $this->getName();
      if(file_exists($destinationDir.$fn)){
         $file_suffix = substr($fn, (strrpos($fn, '.')+1));
         if  (!preg_match('/_\d+\.(?:\w{3}\.)?\w{3,4}$/', $fn)) {
            $fn = str_replace(".$file_suffix", "_1.$file_suffix", $fn);
         }
         if (preg_match('/_(\d+)\.((?:\w{3}\.)?\w{3,4})$/', $fn, $m)){
            while (file_exists($destinationDir.$fn)){
                  $fn = str_replace("$m[1].$m[2]", ++$m[1].".$m[2]", $fn);
            }
         }
      }
      return $fn;
   }

}
?>