<?php
/**
 * Metoda pro práci se soubory typu zip, umožňuje rozbalování souborů, pakování
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.5.0 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract		Třída pro práci se ZIP soubory
 */
class ZipFile extends File {
   const MIME_ZIP_TYPE = 'application/zip';

   /**
	 * Pole s typy zip souborů
	 * @var array
	 */
	private $zipExtensionsArray = array("zip" => 'application/zip',
										"zip2" => 'application/x-zip-compressed',
										"zip3" => 'application/x-zip');

   /**
    * Konstruktor třídy
    * @param string/File $file -- název souboru nebo objekt typu File
    * @param string $dir -- (option) název adresáře se souborem může být uveden
    * v názvu souboru
    */
   function __construct($file, $dir = null){
      if($file instanceof File){
         parent::__construct($file);
      } else {
         parent::__construct($file, $dir);
      }
   }

   /**
	 * Metoda zjišťuje, zdali je uploadovaný soubor zip soubor
	 * @param string -- soubor, který se zjišťuje
	 *
	 */
	public function isZipFile() {
      if($this->getMimeType() == self::MIME_ZIP_TYPE){
			return true;
		} else {
			return false;
      }
	}

    /**
	 * Rozbali zip soubor do cílového adresáře
	 *
	 * @param   string --	Cesta, kam se zip soubor rozbalí, (false rozbalí soubor do aktuálního adresáře se zip souborem)
	 * @param   boolean --	Jestli má být zip soubor rozbalen do adresáře se stejným jménem
	 * @param   boolean --	Jestli mají být soubory přepsány
	 *
	 * @return  boolean     Succesful or not
	 */
	public function unZip($dest_dir=false, $create_zip_name_dir=true, $overwrite=true)
	{
		if(function_exists("zip_open"))
		{
         if(!is_resource(zip_open($this->getNameInput(true))))
			{
				$src_file=dirname($_SERVER['SCRIPT_FILENAME'])."/".$this->getNameInput();
			}
         $zip = zip_open($this->getNameInput(true));
			if (is_resource($zip))
			{
				$splitter = ($create_zip_name_dir === true) ? "." : "/";
				if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($this->getNameInput(true), $splitter))."/";

				// Create the directories to the destination dir if they don't already exist
            $dirsObj = new Dir();
            $dirsObj->checkDir($dest_dir);
            unset ($dirsObj);

				// For every file in the zip-packet
				while ($zip_entry = zip_read($zip))
				{
					// Now we're going to create the directories in the destination directories

					// If the file is not in the root dir
					$pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
					if ($pos_last_slash !== false)
					{
						// Create the directory where the zip-entry should be saved (with a "/" at the end)
						$this->createDirs($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
					}

					// Open the entry
					if (zip_entry_open($zip,$zip_entry,"r"))
					{

						// The name of the file to save on the disk
						$file_name = $dest_dir.zip_entry_name($zip_entry);

						// Check if the files should be overwritten or not
						if ($overwrite === true || $overwrite === false && !is_file($file_name))
						{
							// Get the content of the zip entry
							$fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

							if(!is_dir($file_name))
							file_put_contents($file_name, $fstream );
							// Set the rights
							if(file_exists($file_name))
							{
								chmod($file_name, 0777);
							}
						}

						// Close the entry
						zip_entry_close($zip_entry);
					}
				}
				// Close the zip-file
				zip_close($zip);
			}
			else
			{
            new CoreException(_('Zip archív ').$this->getNameInput(true)._(' neexistuje'));
				return false;
			}

			return true;
		}
		else
		{
			if(version_compare(phpversion(), "5.2.0", "<")){
            $infoVersion=_(" (použijte PHP 5.2.0 nebo vyšší)");
         }
			new CoreException(_('Je potřeba PHP zip rozšíření pro práci se zip archívy').$infoVersion);
		}
	}
}
?>
