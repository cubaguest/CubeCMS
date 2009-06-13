<?php
/**
 * Metoda pro práci s mp3 soubory, umožňuje ověřování a vše co umí třída File
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract		Třída pro práci s mp3 soubory
 */
class Mp3File extends File {
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
	 * @param boolean -- (option)jestli mají být hlášeny chyby
	 *
	 */
	public function isMp3File($showError = false) {
      if($this->getMimeType() == $this->mimeTypes['mp3']){
         return true;
		} else {
         if($showError){
            $this->errMsg()->addMessage(_('Nebyl zadán soubor typu Mp3'));
         }
			return false;
      }
	}
}
?>