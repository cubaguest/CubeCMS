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

   private $overWrite = false;

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
         if($this->isMultiLang() OR $this->isDimensional()) {
            $dir = new Filesystem_Dir($this->uploadDir);
            $dir->checkDir();

            $files = array();
            foreach ($_FILES[$this->getName()]['name'] as $key => $filename) {
               if ($_FILES[$this->getName()]['error'][$key] == UPLOAD_ERR_OK) {
                  $saveFileName = vve_cr_safe_file_name($_FILES[$this->getName()]["name"][$key]);
                  // kontrola adresáře
                  move_uploaded_file($_FILES[$this->getName()]["tmp_name"][$key],
                     $dir . $saveFileName);
                  // vatvoření pole s informacemi o souboru
                  $files[] = array('name' => $saveFileName,
                     'path' => $dir,
                     'size' => $_FILES[$this->getName()]["size"][$key],
                     'type' => $this->getMimeType($dir . $saveFileName),
                     'extension' => pathinfo($dir.$saveFileName, PATHINFO_EXTENSION));
               } else if ($_FILES[$this->getName()]['error'][$key] == UPLOAD_ERR_NO_FILE) {
//                  $files[] = false; // @TODO -- proč takhle???
               } else {
                  $this->creteUploadError($_FILES[$this->getName()]['error'][$key], $_FILES[$this->getName()]['name'][$key]);
                  $files[] = array(
                     'name' => $_FILES[$this->getName()]['name'],
                     'size' => $_FILES[$this->getName()]["size"]
                  );
               }
            }
            $this->values = $files;
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
                   'type' => $this->getMimeType($dir.$saveFileName),
                   'extension' => pathinfo($dir.$saveFileName, PATHINFO_EXTENSION));
            } else if($_FILES[$this->getName()]['error'] == UPLOAD_ERR_NO_FILE) {
               $this->values = null;
            } else {
               $this->creteUploadError($_FILES[$this->getName()]['error'], $_FILES[$this->getName()]['name']);
               $this->values = array(
                  'name' => $_FILES[$this->getName()]['name'],
                  'size' => $_FILES[$this->getName()]["size"]
                  );
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
            $this->errMsg()->addMessage(sprintf($this->tr('Problém s nahráním souboru "%s"'),$fileName));
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
   public function controll() {
      $this->html()->clearContent();
      if(!$this->isValid AND $this->isPopulated) {
         $this->html()->addClass('formError');
      }
   // tady bude if při multilang
      $this->html()->setAttrib('type', 'file');
      if($this->isDimensional()){
         $this->html()->setAttrib('multiple', 'multiple');
         if($this->dimensional == null){
            $this->html()->setAttrib('id', $this->getName()."_".$this->renderedId);
         } else {
            $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId."_".$this->dimensional);
         }
         $this->html()->setAttrib('name', $this->getName().'['.$this->dimensional.']');
      } else {
         $this->html()->setAttrib('name', $this->getName());
         $this->html()->setAttrib('id', $this->getName().'_'.$this->renderedId);
      }
      $this->renderedId++;
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
    * Metoda nastaví jestli se má soubor popřípadě přepsat nebo vytvořit nový název
    * @param bool $overwrite -- true pro přepsání
    * @return Form_Element_File
    */
   public function setOverWrite($overwrite = false)
   {
      $this->overWrite = $overwrite;
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
         $className = "Filesystem_File";
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
