<?php
/**
 * Třída pro obsluhu INPUT prvku typu FILE
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu FILE. Umožňuje kontrolu
 * správnosti zadání,kódování/dekódování obsahu prvku, jazykovou obsluhu a jeho
 * vykreslení i s popisem v šabloně. Při špatném zadání se stará o zvýraznění
 * chyby.
 *
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 6.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_File extends Form_Element {
/**
 * Adresář pro upload souborů
 * @var string
 */
   private $uploadDir = null;

   /**
    * Konstruktor elemntu
    * @param string $name -- název elemntu
    * @param string $label -- popis elemntu
    */
   public function  __construct($name, $label = null, $prefix = null) {
      parent::__construct($name, $label,$prefix);
      $this->setUploadDir(AppCore::getAppCacheDir());
   }

   /**
    * Metoda naplní element
    * @param string $method -- typ metody přes kterou je prvek odeslán (POST|GET)
    * @todo dodělat vytváření unikátních názvů souborů
    */
   public function populate() {
      if(isset ($_FILES[$this->getName()])) {
      //         $this->values = $_FILES[$this->getName()];
         if($this->isMultiLang() OR $this->isDimensional()) {

         } else {
            if($_FILES[$this->getName()]['error'] == UPLOAD_ERR_OK) {
               $saveFileName = vve_cr_safe_file_name($_FILES[$this->getName()]["name"]);
               // kontrola adresáře
               $dir = new Filesystem_Dir($this->uploadDir);
               $dir->checkDir();
               move_uploaded_file($_FILES[$this->getName()]["tmp_name"],
                   $dir . $saveFileName);
               // vatvoření pole s informacemi o souboru
               $this->values = array('name' => $saveFileName,
                   'path' => $dir,
                   'size' => $_FILES[$this->getName()]["size"],
                   'type' => $this->getMimeType($dir.$_FILES[$this->getName()]["name"]),
                   'extension' => $this->getExtension($_FILES[$this->getName()]["name"]));
//               array_push($this->values, $file);
            } else if($_FILES[$this->getName()]['error'] == UPLOAD_ERR_NO_FILE) {
                  $this->values = null;
               } else {
                  $this->creteUploadError($_FILES[$this->getName()]['error'], $_FILES[$this->getName()]['name']);
               }
         }
      } else {
         $this->values = null;
      }
      $this->unfilteredValues = $this->values;
      $this->isPopulated = true;
   }

   /**
    * Metoda vytvoří chybovou hlášku podle zadaného kódu
    * @param int $errNumber -- id chybové hlášky
    */
   private function creteUploadError($errNumber, $fileName) {
      switch($errNumber) {
         case 0: //no error; possible file attack!
            $this->errMsg()->addMessage(sprintf(_('Problém s nahráním souboru "%s"'),$fileName));
            break;
         case UPLOAD_ERR_INI_SIZE: //uploaded file exceeds the upload_max_filesize directive in php.ini
         case UPLOAD_ERR_FORM_SIZE: //uploaded file exceeds the upload_max_filesize directive in php.ini
            $this->errMsg()->addMessage(sprintf(_('Soubor "%s" je příliš velký maximálně %sB'), $fileName, ini_get('upload_max_filesize')));
            break;
         case UPLOAD_ERR_PARTIAL: //uploaded file was only partially uploaded
            $this->errMsg()->addMessage(sprintf(_('Soubor "%s" byl nahrán jen částečně'),$fileName));
            break;
         case UPLOAD_ERR_NO_TMP_DIR: //uploaded file was only partially uploaded
            $this->errMsg()->addMessage(sprintf(_('Soubor "%s" se nepodařilo uložit do tmp adresáře'),$fileName));
            break;
         case UPLOAD_ERR_EXTENSION: //uploaded file was only partially uploaded
            $this->errMsg()->addMessage(sprintf(_('Nahrání souboru "%s" bylo zastaveno'),$fileName));
            break;
         case UPLOAD_ERR_CANT_WRITE: //uploaded file was only partially uploaded
            $this->errMsg()->addMessage(sprintf(_('Soubor "%s" se nepodařilo zapsat na serveru'),$fileName));
            break;
         default: //a default error, just in case!  :)
            $this->errMsg()->addMessage(sprintf(_('Problém s nahráním souboru "%s"'),$fileName));
            break;
      }
   }

   /**
    * Metoda vrací mime typ souboru
    * @param string $file -- cesta k souboru
    * @return string -- mime typ
    */
   private function getMimeType($file) {
      // for php 5.3 or finfo extension
      if(function_exists('finfo_open')) {
         $finfo = new finfo(FILEINFO_MIME, "/usr/share/misc/magic");
         if (!$finfo) {
            trigger_error("Opening fileinfo database failed");
            exit();
         }
         $type = $finfo->file($file);
         //vytažení extension
         $spacePos = strpos($type, ' ');
         if($spacePos > 0){
            $type = substr($type, 0, $spacePos);
         }

         unset ($finfo);
      }
      // for php 5.2 and older
      else {
         $type = mime_content_type($file);
      }
      return $type;
   }
   
   /**
    * Metoda vrací příponu souboru
    * @param string $file -- název souboru
    * @return string -- přípona
    */
   private function getExtension($file) {
      //najdeme první tečku.
      $dotPos = strpos($file, '.');
      $extension = strtolower(substr($file, $dotPos+1, strlen($file)));
      return $extension;
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      $this->html()->clearContent();
      if(!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass('formError');
      }
   // tady bude if při multilang
      $this->html()->setAttrib('name', $this->getName());
      $this->html()->setAttrib('type', 'file');

      if($this->isDimensional()){
         $this->html()->setAttrib('id', $this->getName()."_".$this->dimensional);
      } else {
         $this->html()->setAttrib('id', $this->getName());
      }
      $this->html()->setAttrib('value', $this->getUnfilteredValues());
      return $this->html();
   }

   /**
    * Metoda nastaví adresář pro nahrání souboru
    * @param string $dir -- adresář
    */
   public function setUploadDir($dir) {
      $this->uploadDir = $dir;
   }

   /**
    * Metoda naplní ovjekt typu file
    * @param string $className -- název třídy která se má vytvořit
    * @return Filesystem_File -- objekt douboru
    * @todo -- dořešit
    */
   public function createFileObject(Filesystem_File $className = null) {
      if($className === null){
         $className = "Filesystem_File";
      } else if(!class_exists($className)){
         throw new UnexpectedValueException(sprintf(_('Třídu %s se nepodařilo načíst'),$className), 1);
      }

      $fileObj = new $className($this->values['name'], $this->uploadDir);
      return $fileObj;
   }
}
?>
