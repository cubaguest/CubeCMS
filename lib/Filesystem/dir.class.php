<?php
/**
 * Třída pro práci s adresáři. umožnuje jejich vytváření, mazání, přejmenovávání
 * a kontrolu existence
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.5.0 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract		Třída pro práci s adresáři
 * @todo -- refaktoring, tak aby to byl objekt plnohodnotný
 */
class Dir {
   /**
    * Obejt pro chybové hlášky hlášky
    * @var Messages
    */
//   private $errmsg = null;

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
      $this->dir = $dirName;

//      if(AppCore::getModuleErrors() instanceof Messages){
//         $this->errmsg = AppCore::getModuleErrors();
//      }
   }

    /**
	  * Metoda otestuje existenci adresáře, a pokud neexistuje pokusí se jej vytvořit
	  * @param string -- název adresáře
     * 
     * @todo dodělat přidávání lomítek před adresář
	 */
	public function checkDir($directory) {
		//doplnění posledního lomítka za dest adresář
		if($directory[strlen($directory)-1] != "/"){
			$directory .= "/";
		}

		if(!file_exists($directory) OR !is_dir($directory)){
			return $this->createDirs($directory);
		}
      return true;
	}

   /**
	 * Funkce vytvoři zadaný adresář i podadresáře, pokud neexistují
	 *
	 * @param string -- adresář
	 */
   public function createDirs($path)
	{
		if (is_dir($path)){
         return true;
      }

      if(mkdir($path, 0777, true)){
         if(chmod($path, 0777)){
            return true;
         } else {
            new CoreException(_('Adresáři ').$path._(' se nepodařilo přidělit správná oprávnění'), 2);
         }
      } else {
         new CoreException(_('Adresář ').$path._(' se nepodařilo vytvořit, zkontrolujte prosím oprávnění'), 1);
      }
   }

   /**
	 * Metoda maže rekjurzivně zadaný adresář/soubor
	 *
	 * @param string -- adresář/soubor, který se má smazat
	 * @return boolena -- true pokud se podařilo adresář/soubor smazat
	 */
	public function rmDir($filepath)
	{
		if (is_dir($filepath) && !is_link($filepath)){
         $dir = opendir($filepath);
			if ($dir){
				while (($sf = readdir($dir)) !== false){
					if ($sf == '.' || $sf == '..'){
						continue;
					}
					if (!$this->rmDir($filepath.'/'.$sf)){
						new CoreException($filepath.'/'.$sf._(' soubor nemohl být smazán.'));
					}
				}
				closedir($dir);
			}
			return rmdir($filepath);
		}
		if(file_exists($filepath)){
			return unlink($filepath);
		}
	}

   /**
	 * Metoda kontroluje jestli je cesta zadána správně,
	 * jinak vrací opravenou cestu
	 *
	 * @param string -- cesta
	 * @return string -- opravená cesta
	 */
	public function checkDirPath($path) {
		if(($path[strlen($path)-1] != '/') AND ($path[strlen($path)-1] != '\\')){
			$path.=DIRECTORY_SEPARATOR;
		}
		return $path;
	}
}
?>
