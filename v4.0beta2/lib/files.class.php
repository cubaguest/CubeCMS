<?php
/**
 * Třída pro obsluhu souborů.
 * Základní třída pro práci se soubory. Umožňuje základní přístup k filesystému, 
 * tj. vytváření a mazání adresářů, kopírování, mazání a přesun souborů souborů, 
 * popřípadě zjišťování jejich existence.
 * 
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu souborů
 * @todo          Dodělat zadávání souboru nebo adresáře přes konstruktor
 */

class Files {
	/**
	 * Metoda překopíruje zadaný soubor do zadaného adresáře pod novým jménem
	 * @param string $srcFile -- název souboru
	 * @param string $dstDir -- adresář, kam se má soubor nakopírovat
	 * @param string $newName -- název souboru
	 */
	public function copyAs($srcFile, $dstDir, $newName) {
		if(!file_exists($dstDir)){
			$this->createDirs($dstDir);
		}
		$return = copy($srcFile, $dstDir.$newName);
		chmod($dstDir.$newName, 0666);
		return $return;
	}
	
	/**
	 * Funkce vytvoři zadaný adresář i podadresáře, pokud neexistují
	 *
	 * @param string $path -- adresář
	 */
	private function createDirs($path){
		if (!is_dir($path)){
					mkdir($path, 0777, true);
					chmod($path, 0777);
		}
	}
	  
	/**
	 * Funkce vytvoří nový název souboru, který je v zadaném adresáři unikátní
	 *
	 * @param string $file -- název souboru
	 * @param string $destinationDir -- adresář, kde se bude soubor vytvářet
	 * @return string -- nový název souboru
	 */
	public function createNewFileName($file, $destinationDir){
		$numberOfArguments = 3;  
		$arguments = func_num_args();
		
		$addNumber = 0;
//		Pokud je argumentů více než dva, je použit v rekurzi
		if($arguments >= $numberOfArguments){
			$addNumber = func_get_arg($numberOfArguments-1);
		}
		
		//doplnění posledního lomítka za dest adresář
		if($destinationDir[strlen($destinationDir)-1] != "/" AND $addNumber == 0){
			$destinationDir .= "/";
		}

		//rozdělení názvu obrázku na název a příponu
		$file_name=strtolower($file);
		$file_name_short=ereg_replace("\.[a-zA-Z]{3,4}$", "", $file_name);
		$tempCount=strlen($file_name_short);
		$tempCountAll=strlen($file_name);
		$file_name_extension=substr($file_name,$tempCount, $tempCountAll-$tempCount);

		//odstraneni nepovolenych zanků a složení dohromady
		$sFunction = new SpecialFunctions();
		
		$file_name_short = $sFunction->utf2ascii($file_name_short);
		unset($sFunction);
		
		if($addNumber == 0){
			$new_file_name=$file_name_short.$file_name_extension;
		} else {
			$new_file_name=$file_name_short.$addNumber.$file_name_extension;
		}

		if(file_exists($destinationDir.$new_file_name)){
			$new_file_name = $this->createNewFileName($file, $destinationDir, (++$addNumber));
		}
		return $new_file_name;
	}
	
	/**
	 * Metoda otestuje existenci adresáře, a pokud neexistuje pokusí se jej vytvořit
	 * @param string -- název adresáře
    * @todo dodělat přidávání lomítek před adresář
	 */
	public function checkDir($directory) {
		//doplnění posledního lomítka za cílový adresář
		if($directory[strlen($directory)-1] != "/"){
			$directory .= "/";
		}
		
		if(!file_exists($directory) OR !is_dir($directory)){
			$this->createDirs($directory);
		}
	}
	
	/**
	 * Rozbali zip soubor do cílového adresáře
	 *
	 * @param string $src_file -- Cesta k zip souboru
	 * @param string $dest_dir -- (option)Cesta, kam se zip soubor rozbalí, (false rozbalí soubor do aktuálního adresáře se zip souborem)
	 * @param boolean $create_zip_name_dir -- (option)Jestli má být zip soubor rozbalen do adresáře se stejným jménem
	 * @param boolean $overwrite -- (option)Jestli mají být soubory přepsány
	 *
	 * @return  boolean     Succesful or not
    * @todo Doladit tak aby v podmínce nebylo přiřazení
	 */
   public function unZip($src_file, $dest_dir=false, $create_zip_name_dir=true, $overwrite=true){
      if(!function_exists("zip_open")){
         throw new BadFunctionCallException(_('Metoda pro práci se ZIP soubory
není implementována. Je třeba použít PHP verze 5.2.0 nebo vyšší'), 1);
      }
      if(!is_resource(zip_open($src_file))){
         $src_file=dirname($_SERVER['SCRIPT_FILENAME'])."/".AppCore::ENGINE_CACHE_DIR.$src_file;
      }
      else if (is_resource($zip = zip_open($src_file))) {
         $splitter = ($create_zip_name_dir === true) ? "." : "/";
         if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";

         // Create the directories to the destination dir if they don't already exist
         $this->createDirs($dest_dir);

         // For every file in the zip-packet
         while ($zip_entry = zip_read($zip)){
            // Now we're going to create the directories in the destination directories

            // If the file is not in the root dir
            $pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
            if ($pos_last_slash !== false){
               // Create the directory where the zip-entry should be saved (with a "/" at the end)
               $this->createDirs($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
            }

            // Open the entry
            if (zip_entry_open($zip,$zip_entry,"r")){

               // The name of the file to save on the disk
               $file_name = $dest_dir.zip_entry_name($zip_entry);

               // Check if the files should be overwritten or not
               if ($overwrite === true || $overwrite === false && !is_file($file_name)){
                  // Get the content of the zip entry
                  $fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

                  if(!is_dir($file_name))
                  file_put_contents($file_name, $fstream );
                  // Set the rights
                  if(file_exists($file_name)){
                     chmod($file_name, 0777);
                  }
               }
               // Close the entry
               zip_entry_close($zip_entry);
            }
         }
         // Close the zip-file
         zip_close($zip);
      } else {
         throw new InvalidArgumentException(sprintf(_('Soubor "%" není ZIP archív'), $src_file),2);
      }
      return true;
   }
	
	/**
	 * Metoda smaže zadaný soubor
    *
	 * @param string -- název souboru
	 * @param string -- cesta k souboru
	 * 
	 * @return boolean -- true pokud byl soubor smazán
	 */
	public function deleteFile($file, $dstDir = null) {
		if($dstDir != null){
         $filePath = $dstDir.$file;
      } else {
         $filePath = $file;
      }
      if(!file_exists($filePath)){
         throw new InvalidArgumentException(sprintf(_('Soubor "%" neexistuje, nelze jej smazat'), $filePath));
      }
		if(!unlink($filePath)){
         throw new UnexpectedValueException(sprintf(_('Soubor "%" se nepodařilo smazat'), $filePath));
		}
		return true;
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
			if ($dh = opendir($filepath)){
				while (($sf = readdir($dh)) !== false){
					if ($sf == '.' || $sf == '..'){
						continue;
					}
					if (!$this->rmDir($filepath.'/'.$sf)){
						throw new UnexpectedValueException(
                     sprintf(_('Soubor "%s" v adresáři "%s" se nepodařilo smazat'), $sf, $filepath));
					}
				}
				closedir($dh);
			}
			if(!rmdir($filepath)){
             throw new UnexpectedValueException(
                 sprintf(_('Adresář "%s" se nepodařilo smazat'), $filepath));
         }
		}
      $this->deleteFile($filepath);
      return true;
	}
	
	/**
	 * Metoda zjišťuje, zdali zadaný soubor existuje
	 * 
	 * @param string -- název a cesta k souboru
	 */
	public function exist($file) {
		return file_exists($file);
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