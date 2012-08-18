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

   private $overWrite = true;
   
   /**
    * Jestli nemá zpracovávat nebezpečné soubory
    * @var 
    */
   private $onlySecureFiles = true;
   private $insecureFiles = array('php', 'phtml', 'php5', 'php3', 'php4', 'asp', 'aspx', 'sh');

   /**
    * Konstruktor elemntu
    * @param string $name -- název elemntu
    * @param string $label -- popis elemntu
    */
   public function  __construct($name, $label = null, $prefix = null) {
      parent::__construct($name, $label,$prefix);
      $this->setUploadDir(AppCore::getAppCacheDir());
      $this->addValidation(new Form_Validator_FileSize(VVE_MAX_UPLOAD_SIZE));
   }

   /**
    * Metoda naplní element
    * @param string $method -- typ metody přes kterou je prvek odeslán (POST|GET)
    * @todo dodělat vytváření unikátních názvů souborů a overwrite
    */
   public function populate() {
      if(isset ($_FILES[$this->getName()])) {
         if($this->isDimensional()) {
            foreach ($_FILES[$this->getName()]['name'] as $key => $filename) {
               $this->uploadFile(
                     $_FILES[$this->getName()]["name"][$key],
                     $_FILES[$this->getName()]["tmp_name"][$key],
                     $_FILES[$this->getName()]["error"][$key],
                     $_FILES[$this->getName()]["type"][$key],
                     $_FILES[$this->getName()]["size"][$key], $key );
            }
         } else if($this->isMultiLang()){
            foreach ($_FILES[$this->getName()]['name'] as $key => $filename) {
               $this->uploadFile(
                     $_FILES[$this->getName()]["name"][$key],
                     $_FILES[$this->getName()]["tmp_name"][$key],
                     $_FILES[$this->getName()]["error"][$key],
                     $_FILES[$this->getName()]["type"][$key],
                     $_FILES[$this->getName()]["size"][$key], $key );
            }
         } else {
            $this->uploadFile(
                  $_FILES[$this->getName()]["name"], 
                  $_FILES[$this->getName()]["tmp_name"], 
                  $_FILES[$this->getName()]["error"], 
                  $_FILES[$this->getName()]["type"], 
                  $_FILES[$this->getName()]["size"] );
         }
      } else {
         $this->values = null;
      }
      $this->unfilteredValues = $this->values;
      $this->isPopulated = true;
   }
    
   private function uploadFile($name, $tmpName, $error, $mime, $size, $key = null)
   {
      if($error == UPLOAD_ERR_OK) {
         $saveFileName = vve_cr_safe_file_name($name);
          
         if($this->onlySecureFiles && in_array(pathinfo($saveFileName, PATHINFO_EXTENSION), $this->insecureFiles)){
            $this->creteUploadError(0, $name);
            $this->isValid(false);
         } else {
            // kontrola adresáře
            $dir = new Filesystem_Dir($this->uploadDir);
            $dir->checkDir();
            if(!$this->overWrite){
               $saveFileName = File::creatUniqueName($saveFileName, $dir);
            }
            move_uploaded_file($tmpName, $dir . $saveFileName);
            // vatvoření pole s informacemi o souboru
            $this->_setFileValue( 
                  array('name' => $saveFileName,
                         'path' => $dir,
                         'size' => $size,
                         'mime' => $mime,
                         'type' => $this->getMimeType($dir.$saveFileName),
                         'extension' => pathinfo($dir.$saveFileName, PATHINFO_EXTENSION)), $key
                  );
         }
      } else if($error == UPLOAD_ERR_NO_FILE) {
         
      } else {
         $this->creteUploadError($error, $name);
         $this->_setFileValue( array( 'name' => $name, 'size' => $size ) );
      };
   }

   private function _setFileValue($data, $key = null) 
   {
      if($this->isDimensional() || $this->isMultiLang()){
         if(!is_array($this->values)){
            $this->values = array();
         }
         $key == null ? $this->values[] = $data : $this->values[$key] = $data;
      } else {
         $this->values = $data;
      }
   }

   /**
    * Metoda vytvoří chybovou hlášku podle zadaného kódu
    * @param int $errNumber -- id chybové hlášky
    */
   private function creteUploadError($errNumber, $fileName) {
      switch($errNumber) {
         case 0: //no error; possible file attack!
            $this->errMsg()->addMessage(sprintf($this->tr('Nahrávání spustitelných souborů je zakázáno ("%s")'),$fileName));
            break;
         case UPLOAD_ERR_INI_SIZE: //uploaded file exceeds the upload_max_filesize directive in php.ini
         case UPLOAD_ERR_FORM_SIZE: //uploaded file exceeds the upload_max_filesize directive in php.ini
            $this->errMsg()->addMessage(sprintf($this->tr('Soubor "%s" je příliš velký maximálně %s'), $fileName, vve_create_size_str(VVE_MAX_UPLOAD_SIZE)));
            break;
         case UPLOAD_ERR_PARTIAL: //uploaded file was only partially uploaded
            $this->errMsg()->addMessage(sprintf($this->tr('Soubor "%s" byl nahrán jen částečně'),$fileName));
            break;
         case UPLOAD_ERR_NO_TMP_DIR: //uploaded file was only partially uploaded
            $this->errMsg()->addMessage(sprintf($this->tr('Soubor "%s" se nepodařilo uložit do tmp adresáře'),$fileName));
            break;
         case UPLOAD_ERR_EXTENSION: //uploaded file was only partially uploaded
            $this->errMsg()->addMessage(sprintf($this->tr('Nahrání souboru "%s" bylo zastaveno'),$fileName));
            break;
         case UPLOAD_ERR_CANT_WRITE: //uploaded file was only partially uploaded
            $this->errMsg()->addMessage(sprintf($this->tr('Soubor "%s" se nepodařilo zapsat na serveru'),$fileName));
            break;
         default: //a default error, just in case!  :)
            $this->errMsg()->addMessage(sprintf($this->tr('Problém s nahráním souboru "%s"'),$fileName));
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
         // todo doladit pod win platformu (není validní cesta)
         $finfo = new finfo(FILEINFO_MIME);
//         $finfo = new finfo(FILEINFO_MIME, "/usr/share/misc/magic");
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
   public function controll($renderKey = null) {
      $rKey = $renderKey != null ? $renderKey : $this->renderedId;
      $this->html()->clearContent();
      if(!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass('formError');
      }
   // tady bude if při multilang
      $this->html()->setAttrib('type', 'file');
      if($this->isDimensional()){
         $this->html()->setAttrib('multiple', 'multiple');
         if($this->dimensional == null){
            $this->html()->setAttrib('id', $this->getName()."_".$rKey);
         } else {
            $this->html()->setAttrib('id', $this->getName().'_'.$rKey."_".$this->dimensional);
         }
         $this->html()->setAttrib('name', $this->getName().'['.$this->dimensional.']');
      } else {
         $this->html()->setAttrib('name', $this->getName());
         $this->html()->setAttrib('id', $this->getName().'_'.$rKey);
      }
      if($renderKey == null){
         $this->renderedId++;
      }
      $this->html()->setAttrib('value', $this->getUnfilteredValues());
      return $this->html();
   }

   /**
    * Metoda nastaví adresář pro nahrání souboru
    * @param string $dir -- adresář
    * @return Form_Element_File
    */
   public function setUploadDir($dir) {
      $this->uploadDir = $dir;
      return $this;
   }

   /**
    * Metoda nastaví jestli se má soubor popřípadě přepsat nebo vytvořit nový název
    * @param bool $overwrite -- true pro přepsání
    * @return Form_Element_File
    */
   public function setOverWrite($overwrite = true)
   {
      $this->overWrite = $overwrite;
      return $this;
   }
   
   /**
    * Metoda nastaví jestli je povoleno i nahrávání nebezpečných souborů
    * @param bool $onlySecure -- false pro vypnutí omezení
    * @return Form_Element_File
    */
   public function setOnlySecureFiles($onlySecure = true)
   {
      $this->onlySecureFiles = $onlySecure;
      return $this;
   }

   /**
    * Metoda naplní ovjekt typu file
    * @param string $className -- název třídy která se má vytvořit
    * @return Filesystem_File -- objekt douboru
    * @todo -- dořešit
    */
   public function createFileObject($className = null) {
      if($className === null){
         $className = "File";
      } else if(!class_exists($className)){
         throw new UnexpectedValueException(sprintf($this->tr('Třídu %s se nepodařilo načíst'),$className), 1);
      }

      if(isset($this->values['name'])){// pokud je jeden soubor
         $fileObj = new $className($this->values['name'], $this->uploadDir);
      } else {
         $fileObj = array();
         foreach ($this->values as $file) {
            $fileObj[] = new $className($file['name'], $this->uploadDir);
         }
      }
      return $fileObj;
   }
}
?>
