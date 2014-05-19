<?php
/**
 * Třída directory
 *
 * @copyright     Copyright (c) 2008-2011 Jakub Matas
 * @version       $Id:  $ VVE 7.3 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída 
 */
class FS_Dir extends TrObject {
   /**
    * Název adresáře
    * @var string
    */
   private $name = null;
   
   /**
    * Cesta k adresáři
    * @var string
    */
   private $path = null;

   /**
    * Konstruktor vytvoří objekt adresáře
    * @param string $name -- (option) název adresáře
    * @param string $path -- (option) cesta k adresáři (pokud není zadána pokusí se rozparsovat mízev adresáře)
    */
   public function __construct($name = null, $path = null)
   {
      // kontrola správnosti adresáře
      $this->name = $name;
      $this->path = $path;
      
      if($path == null){
         $i = pathinfo($name);
         $this->name = $i['basename'];
         $this->path = $i['dirname'];
      }
      
      if($this->path == null){
         $this->path = AppCore::getAppCacheDir();
      }
      // add las slash
      if(substr($this->path, -1) != DIRECTORY_SEPARATOR){
         $this->path .= DIRECTORY_SEPARATOR;
      }
   }

    /**
     * Metoda otestuje existenci adresáře, a pokud neexistuje pokusí se jej vytvořit
     * @param string -- název adresáře
     * 
     * @todo dodělat přidávání lomítek před adresář
    */
   public function check() 
   {
      //doplnění posledního lomítka za dest adresář
      if(substr($this->path, -1) != DIRECTORY_SEPARATOR){
         $this->path .= DIRECTORY_SEPARATOR;
      }
      if(!$this->exist() OR !is_dir((string)$this)){
         return $this->create();
      }
      return true;
   }

   /**
    * Metoda provede kontrolu existence adresáře a pokud neexistuje tak jej vytvoří
    * @param $dir
    * @return bool
    */
   public static function checkStatic($dir)
   {
      //doplnění posledního lomítka za dest adresář
      if(substr($dir, -1) != DIRECTORY_SEPARATOR){
         $dir .= DIRECTORY_SEPARATOR;
      }
      if(!file_exists($dir) || !is_dir($dir)){
         if(!@mkdir($dir, 0777, true)){
            return false;
         }
      }
      return true;
   }

   /**
    * Funkce vytvoři zadaný adresář i podadresáře, pokud neexistují
    * @param string -- adresář
    */
   public function create()
   {
      if(!@mkdir((string)$this, 0777, true)){
         throw new CoreException(sprintf($this->tr('Adresář "%s" se nepodařilo vytvořit, zkontrolujte oprávnění'),(string)$this), 2);
      }
      if(!chmod((string)$this, 0777)){
         throw new CoreException(sprintf($this->tr('Adresáři "%s" se nepodařilo přidělit potřebná oprávnění'),(string)$this), 3);
      }
      return true;
   }

   /**
    * Metoda maže rekurzivně zadaný adresář
    * @param string -- INTERNAL !!! not use !!!
    * @return FS_Dir
    * @todo -- přepsat !!!!
    */
   public function delete($path = null){
      if($path === null) $path = (string)$this;
      self::deleteStatic($path);
      return $this;
   }

   public static function deleteStatic($dir)
   {
      if (is_dir($dir) && !is_link($dir)){
         $files = glob($dir . '/*');
         if($files){
            foreach($files as $file) {
               if(is_dir($file))
                  self::deleteContentStatic($file);
               else
                  unlink($file);
            }
         }
         @rmdir($dir);
      }

      self::deleteContentStatic($dir);
   }

   /**
    * Metoda maže rekjurzivně obsah zadaného adresáře
    * @return FS_Dir
    */
   public static function deleteContentStatic($path){
      if (is_dir($path) && !is_link($path)){
         foreach(glob($path . '/*') as $file) {
            if(is_dir($file))
               self::deleteContentStatic($file);
            else
               unlink($file);
         }
      }
   }

   public function deleteContent(){
      self::deleteContentStatic((string)$this);
      return $this;
   }

   /**
    * Metoda vrací název adresáře
    * @return string -- název
    */
   public function getName() {
      return $this->name;
   }
   
   /**
    * Metoda vrací cestu k adresáři
    * @return string -- cesta
    */
   public function getPath() {
      return $this->path;
   }
   
   /**
    * Metoda nastaví název adresáře
    * @param string $name -- název
    * @return FS_Dir
    */
   public function setName($name) {
      $this->name = $name;
      return $this;
   }
   
   /**
    * Metoda nastaví cestu k adresáři
    * @param string $path -- cesta
    * @return FS_Dir
    */
   public function setPath($path) {
      $this->path = $path;
      return $this;
   }

   /**
    * Magická metoda pro vracení adresáře jako řetězec
    * @return string -- adresář
    */
   public function  __toString() {
      return $this->getPath().$this->getName().DIRECTORY_SEPARATOR;
   }

   /**
    * Metoda přejmenuje adresář
    * @param string $newName -- nový název
    * @return FS_Dir
    */
   public function rename($newName){
      // tady patří detekce jestli byla předána cesta nebo jenom název
      
      if(@rename((string)$this, $this->getPath().$newName)){
         $this->dir = $newName;
      } else {
         throw new UnexpectedValueException(sprintf($this->tr('Adresář "%s" se nepodařilo přejmenovat'), (string)$this));
      }
      return $this;
   }

   /**
    * Metoda kontroluje jestli zadaný adresář existuje
    * @return boolean -- true pokud adresář existuje
    */
   public function exist(){
      if(file_exists((string)$this) AND is_dir((string)$this)){
         return true;
      }
      return false;
   }
   
   /**
    * Metoda vrací cestu k adresáři pro web (tedy s http:// ...)
    * @return type 
    */
   public function getWebPath()
   {
      return null;
   }

   /**
    * Kopíruje adresář
    * @param string/FS_Dir $targetDir
    */
   public function copy($targetDir)
   {
      if(!($targetDir instanceof FS_Dir)){
         $targetDir = new FS_Dir($this->getName(), $targetDir);
      }
      $targetDir->check();
      foreach (
         $iterator = new RecursiveIteratorIterator(
          new RecursiveDirectoryIterator((string)$this, RecursiveDirectoryIterator::SKIP_DOTS),
          RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
          if ($item->isDir()) {
            mkdir((string)$targetDir . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
          } else {
            copy($item, (string)$targetDir . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
          }
      }
   }
}
