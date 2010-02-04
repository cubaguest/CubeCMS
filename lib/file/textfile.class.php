<?php
/**
 * Metoda pro práci s textovými soubory, umožňuje jejich vytváření, mazání, ukládání
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract		Třída pro práci s textovými souory
 * @todo          není zcela implementovány, chyby načítání, doimplementovat metody
 * getContent a setContent
 */

class TextFile extends File{
   /**
    * Jestli má být soubor přepsán nebo vytvořen nový
    * @var boolean
    */
   private $override = false;

   /**
    * obsah textového souboru
    * @var string
    */
   private $content = null;

   /**
    * Konstruktor třídy
    * @param string/File $file -- název souboru nebo objekt typu File
    * @param string $dir -- (option) název adresáře se souborem může být uveden
    * v názvu souboru
    * @param boolean $overrideExist -- jestli se má přepsat pokud již existuje
    */
   function __construct($file, $dir = null, $overrideExist = false){
      if($file instanceof File){
         parent::__construct($file);
      } else {
         parent::__construct($file, $dir);
      }
      $this->override = $overrideExist;
   }

   public function setContent($content, $merge = true) {
      if($merge){
         $this->content .= $content;
      } else {
         $this->content = $content;
      }
   }

   /**
    * Metoda vreací obsah souboru
    * @return string -- obsah souboru
    */
   public function getContent() {
      ;
   }

   /**
    * Metoda ukládá soubor
    * @return boolean -- true pokud se soubor podařilo uložit
    */
   public function save($file = null, $dir = null) {
      if($file == null){
         $file = $this->getNameInput();
      }
      if($dir == null){
         $dir = $this->getFileDir();
      }
      // pokud existuje a máme přepisovat
      if($this->exist() AND $this->override){
         // smažeme původní
         $this->remove();
      }
      // pokud existuje ale přepisovat nemáme
      else if($this->exist()){
         $this->creatUniqueName($dir);
         $file = $this->getName();
      }
      // pouze zkontrolujeme adresář
      else {
         $this->checkDir();
      }
      // vytvoříme nový a nastavíme na zápis
      $handle = fopen($dir.$file, "w");
      if (!$handle) {
         $this->errMsg()->addMessage(_('Soubor ').$dir.$file._(' se nepodařilo otevřít'));
         return false;
      }
      if (fwrite($handle, $this->content) === FALSE) {
         $this->errMsg()->addMessage(_('Do souboru ').$dir.$file._(' se nepodařilo zapsat data'));
         return false;
      }
      // zaření souboru
      fclose($handle);
      return true;
   }
}
?>