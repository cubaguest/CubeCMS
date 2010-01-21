<?php
/**
 * Třída pro práci s Falsh objekty
 * Třída pro základní práci s Flash objekty. Umožňuje jejich zjišťování.
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: file_flash.class.php 627 2009-06-14 07:57:46Z jakub $ VVE3.9.4 $Revision: 627 $
 * @author        $Author: jakub $ $Date: 2009-06-14 09:57:46 +0200 (Ne, 14 čen 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-06-14 09:57:46 +0200 (Ne, 14 čen 2009) $
 * @abstract 		Třída pro práci s Flash objekty
 */

class File_Flash extends File {
   /**
    * Proměná obsahuje jestli je soubor Flash
    * @var boolean
    */
   private $isFlash = false;

   /**
    * Porměné obsahuje šířku flashe
    * @var int
    */
   private $flashWidth = 0;

   /**
    * Porměné obsahuje výšku flashe
    * @var int
    */
   private $flashHeight = 0;

   /**
    * Jestli mají bý hlášeny chyby
    * @var boolean
    */
   private $reportErrors = true;

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
    * Matedoa zjišťuje, jestli je daný soubor flash
    * @param boolean $reportErrors -- jestli mají být vyvolány chybové hlášky
    * @return boolean -- true pokud se jedná o flash se kterým umí pracovat
    */
   public function isFlash($reportErrors = true) {
      $this->reportErrors = $reportErrors;
      $this->checkIsFlash();
      return $this->isFlash;
   }

   /**
    * Metoda načte informace o flashi
    */
   private function checkIsFlash() {
      //	Ověření existence obrázku
      if($this->getNameInput() != null AND $this->exist()){
         //		zjištění vlastností obrázků
         $imageProperty = getimagesize($this->getNameInput(true));
         $this->flashWidth = $imageProperty[0];
         $this->flashHeight = $imageProperty[1];
         $type = $imageProperty[2];
         if($type == IMAGETYPE_SWF OR $type == IMAGETYPE_SWC){
            $this->isFlash = true;
         } else {
            $this->isFlash = false;
            if($this->reportErrors){
               $this->errMsg()->addMessage(_('Zadaný soubor není flash objekt'));
            }
         }
      } else {
         if($this->reportErrors){
            new CoreException(_('Soubor se zadaným flashem neexistuje'), 1);
         }
      }
   }

   /**
    * Metoda vrací rozměr flashe - Šířku
    *
    * @return integer -- šířka flashe
    */
   public function getWidth() {
      return $this->flashWidth;
   }

   /**
    * Metoda vrací rozměr flashe - Výšku
    *
    * @return integer -- výška flashe
    */
   public function getHeight() {
      return $this->flashHeight;
   }
}
?>