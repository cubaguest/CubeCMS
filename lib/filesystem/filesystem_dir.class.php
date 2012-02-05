<?php
/**
 * Třída pro práci s adresáři. umožnuje jejich vytváření, mazání, přejmenovávání
 * a kontrolu existence
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract		Třída pro práci s adresáři
 * @todo -- refaktoring, tak aby to byl objekt plnohodnotný
 */
class Filesystem_Dir {
   /**
    * Adresář a cesta k němu
    * @var string
    */
   private $dir = null;

   /**
    * Konstruktor vytvoří objekt adresáře
    * @param string $dirName -- (option) název adresáře
    */
   public function __construct($dirName = null) {
      // kontrola správnosti adresáře
      if($dirName instanceof Filesystem_Dir){
         $this->dir = (string)$dirName;
      } else {
         $this->dir = $this->checkDirPath($dirName);
      }
   }

    /**
	  * Metoda otestuje existenci adresáře, a pokud neexistuje pokusí se jej vytvořit
	  * @param string -- název adresáře
     * 
     * @todo dodělat přidávání lomítek před adresář
	 */
	public function checkDir($directory = null) {
		//doplnění posledního lomítka za dest adresář
		if($directory == null){
         $directory = $this->getDir();
      }
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
	 *
	 * @param string -- adresář
	 */
   public function createDir($path = null){
      if($path == null) $path = $this->getDir();

      if(!@mkdir($path, 0777, true)){
         throw new CoreException(sprintf(_('Adresáři "%s" se nepodařilo vytvořit, zkontrolujte oprávnění'),$path), 2);
      }
      if(!chmod($path, 0777)){
         throw new CoreException(sprintf(_('Adresáři "%s" se nepodařilo přidělit potřebná oprávnění'),$path), 3);
      }
      return true;
   }

   /**
	 * Metoda maže rekjurzivně zadaný adresář/soubor
	 *
	 * @param string -- adresář/soubor, který se má smazat
	 * @return boolena -- true pokud se podařilo adresář/soubor smazat
	 */
	public function rmDir($filepath = null){
      if($filepath === null) $filepath = $this->getDir();
      
		if (is_dir($filepath) && !is_link($filepath)){
         $dir = opendir($filepath);
			if ($dir){
				while (($sf = readdir($dir)) !== false){
					if ($sf == '.' || $sf == '..'){
						continue;
					}
					if (!$this->rmDir($filepath.'/'.$sf)){
                  throw new CoreException(sprintf(_('Soubor "%s" z adresáře "%s" nemohl být smazán.'),$sf,$filepath),4);
					}
				}
				closedir($dir);
			}
			if (!@rmdir($filepath)){
            throw new CoreException(sprintf(_('Adresář "%s" se nepodařilo smazát.'),$filepath),5);
			}
         return true;
		} else if(file_exists($filepath)){
			if (!@unlink($filepath)){
            throw new CoreException(sprintf(_('Soubor "%s" se nepodařilo smazat.'),$filepath),6);
			}
		}
      return true;
	}

   /**
	 * Metoda kontroluje jestli je cesta zadána správně,
	 * jinak vrací opravenou cestu
	 *
	 * @param string -- cesta
	 * @return string -- opravená cesta
	 */
   public function checkDirPath($path = null) {
      if($path == null){
         $path = $this->getDir();
      }
      if(($path[strlen($path)-1] != '/') AND ($path[strlen($path)-1] != '\\')){
         $path.=DIRECTORY_SEPARATOR;
      }
      return $path;
   }

   /**
    * Metoda vrací adresář
    * @return string -- adresář
    */
   public function getDir() {
      return $this->dir;
   }

   /**
    * Magická metoda pro vracení adresáře jako řetězec
    * @return string -- adresář
    */
   public function  __toString() {
      return $this->getDir();
   }

   /**
	 * Metoda vymaže zadaný adresář z filesystému i s obsahem
	 *
	 * @param dir -- adresář, který se má smazat
    * @deprecated -- je obssažena přes parametr rmdir pro rekurzi
	 */
	public function delTree($f){
		foreach(glob($f.'/*') as $sf){
			if (is_dir($sf) && !is_link($sf)){
				deltree($sf);
				rmdir($sf);
			}else{
				unlink($sf);
			}
		}
	}

   /**
    * Metoda přejmenuje adresář
    * @param string $newName -- nový název
    */
   public function rename($newName){
      $dirOld = substr($this->dir, 0, strlen($this->dir)-1);
      $dir = substr($dirOld, 0, strrpos($dirOld, DIRECTORY_SEPARATOR)+1);
      $newDir = $this->checkDirPath($dir.$newName);
      if(@rename($this->dir, $newDir)){
         $this->dir = $newDir;
      } else {
         throw new UnexpectedValueException(_('Adresář se nepodařilo přejmenovat'));
      }
      return true;
   }

   /**
    * Metoda kontroluje jestli zadaný adresář existuje
    * @return boolean -- true pokud adresář existuje
    */
   public function exist(){
      if(file_exists($this->getDir()) AND is_dir($this->getDir())){
         return true;
      }
      return false;
   }
}
?>
