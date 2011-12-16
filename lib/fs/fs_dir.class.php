<?php
/**
 * Třída directory
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id:  $ VVE 7.3 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída 
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
      $directory = (string)$this;
      
      if($directory[strlen($directory)-1] != "/"){
			$directory .= "/";
		}
      if(!file_exists($directory) OR !is_dir($directory)){
         return $this->createDir($directory);
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
         throw new CoreException(sprintf($this->tr('Adresáři "%s" se nepodařilo vytvořit, zkontrolujte oprávnění'),$path), 2);
      }
      if(!chmod((string)$this, 0777)){
         throw new CoreException(sprintf($this->tr('Adresáři "%s" se nepodařilo přidělit potřebná oprávnění'),$path), 3);
      }
      return true;
   }

   /**
	 * Metoda maže rekjurzivně zadaný adresář
	 * @param string -- INTERNAL !!! not use !!!
	 * @return FS_Dir
    * @todo -- přepsat !!!!
	 */
	public function delete($path = null){
      if($path === null) $path = (string)$this;
      
		if (is_dir($path) && !is_link($path)){
         $dir = opendir($path);
			if ($dir){
				while (($sf = readdir($dir)) !== false){
					if ($sf == '.' || $sf == '..'){
						continue;
					}
					try {
                  $this->delete($path.'/'.$sf);
               } catch (UnexpectedValueException $exc) {
                  throw new UnexpectedValueException(sprintf($this->tr('Soubor "%s" z adresáře "%s" nemohl být smazán.'),$sf,$path),4);
                  
                  break;
               }
				}
				closedir($dir);
			}
			if (!@rmdir($path)){
            throw new UnexpectedValueException(sprintf($this->tr('Adresář "%s" se nepodařilo smazat.'),$path),5);
			}
         return true;
		} else if(file_exists($path)){
			if (!@unlink($path)){
            throw new UnexpectedValueException(sprintf($this->tr('Soubor "%s" se nepodařilo smazát.'),$path),6);
			}
		}
      return $this;
	}

   /**
	 * Metoda kontroluje jestli je cesta zadána správně,
	 * jinak vrací opravenou cestu
	 *
	 * @param string -- cesta
	 * @return string -- opravená cesta
	 */
   private function checkDirPath($path = null) {
      if($path == null){
         $path = $this->getDir();
      }
      if(($path[strlen($path)-1] != '/') AND ($path[strlen($path)-1] != '\\')){
         $path.=DIRECTORY_SEPARATOR;
      }
      return $path;
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
      return $this->getPath().DIRECTORY_SEPARATOR.$this->getName().DIRECTORY_SEPARATOR;
   }

   /**
    * Metoda přejmenuje adresář
    * @param string $newName -- nový název
    * @return FS_Dir
    */
   public function rename($newName){
      // tady patří detekce jestli byla předána cesta nebo jenom název
      
      if(@rename((string)$this, $this->path.$newName)){
         $this->dir = $newDir;
      } else {
         throw new UnexpectedValueException($this->tr('Adresář se nepodařilo přejmenovat'));
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
}
?>
