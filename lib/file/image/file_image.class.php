<?php
/**
 * Třída pro práci s obrázky
 * Třída pro základní práci s obrázky. Umožňuje jejich ukládání, ořezávání,
 * změnu velikost a změnu formátu obrázku.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro práci s obrázky
 */

class File_Image extends File {
   /**
    * Objekt pro práci s obrázky
    * @var File_Image_Base
    */
   private $imageObj = null;

   /**
    * Konstruktor třídy
    * @param string/File $file -- název souboru nebo objekt typu File
    * @param string $dir -- (option) název adresáře se souborem může být uveden
    * v názvu souboru
    */
   public function __construct($name = null, $path = null)
   {
      parent::__construct($name, $path);
      $this->initImageObj();
   }
   
   /**
    * kopírování souboru
    * @param string/FS_Dir $path -- nová cesta
    * @param bool $returnNewObj -- retunr new obj
    * @return File_Image
    */
   public function copy($path, $returnNewObj = false, $newFile = null, $createUniqueName = true)
   {
      $fileObj = parent::copy($path, $returnNewObj, $newFile, $createUniqueName);
      // need reinit image
      $fileObj->getData()->_setFileObj($fileObj);
      return $fileObj;
   }

   private function initImageObj()
   {
      /* může být prázdný ??? */
      if(VVE_USE_IMAGEMAGICK == true){
         $this->imageObj = new File_Image_Imagick($this);
      } else {
         $this->imageObj = new File_Image_Gd($this);
      }
   }


   /**
    * metoda vrátí obsah obrázku
    * @return File_Image_Base
    */
   public function getData()
   {
      return $this->imageObj;
   }
   
   /**
    * metoda vrátí obsah obrázku
    * @return File_Image_Base
    */
   public function setData($data)
   {
      if($data instanceof File_Image_Base){
         $this->imageObj = $data;
      } else {
         throw new UnexpectedValueException($this->tr('Předán špátný paramter s obsahem obrázku'));
      }
      parent::setContent($data);
   }

   /**
    * Matedoa zjišťuje, jestli je daný soubor obrázek
    * @return boolean -- true pokud se jedná o obrázek se kterým umí pracovat
    */
   public function isImage() {
      if($this->exist()) {
      //		zjištění vlastností obrázků
         $imageProperty = getimagesize((string)$this);
         /*
          * kvůli flashi je tady vyjímka protože flash se zpracovává v File_Flash
          * a nelze mu měniti velikost ani jej resamplovat
          *
          * Flash má typ obrázku 4 a 13 (IMAGETYPE_SWF a IMAGETYPE_SWC)
          */
         if($this->imageType != null && $this->imageType != IMAGETYPE_SWF && $this->imageType != IMAGETYPE_SWC) {
            return true;
         }
      } else {
         return false;
      }
   }
   
   public function send()
   {
      ob_end_clean();
      header("Pragma: public"); // required
      header("Expires: 0");
      header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
      header("Cache-Control: private",false); // required for certain browsers
      header("Content-Type: ".$this->getMimeType());
      header("Content-Disposition: attachment; filename=\"".$this->getName()."\";" );
      header("Content-Transfer-Encoding: binary");
//      header("Content-Length: ".$this->getSize()); // not work. images are corrupted
      flush();
      $this->imageObj->write(); 
      exit;
   }
   
   public function save()
   {
      parent::save();
      $this->imageObj->write((string)$this);
   }
}
?>