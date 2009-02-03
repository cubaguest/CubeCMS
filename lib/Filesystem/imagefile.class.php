<?php
/**
 * Třída pro práci s obrázky
 * Třída pro základní práci s obrázky. Umožňuje jejich ukládání, ořezávání,
 * změnu velikost a změnu formátu obrázku.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: imagefile.class.php 15 2009-01-30 16:45:22Z jakub $ VVE3.5.0 $Revision: 15 $
 * @author        $Author: jakub $ $Date: 2009-01-30 16:45:22 +0000 (Pá, 30 led 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-01-30 16:45:22 +0000 (Pá, 30 led 2009) $
 * @abstract 		Třída pro práci s obrázky
 */

class ImageFile extends File {
   /**
    * Proměná obsahuje jestli je soubor obrázek
    * @var boolean
    */
   private $isImage = false;

   /**
    * Porměné obsahuje šířku původního obrázku
    * @var int
    */
   private $imageWidth = 0;

   /**
    * Porměné obsahuje výšku původního obrázku
    * @var int
    */
   private $imageHeight = 0;

   /**
    * Porměné obsahuje typ původního obrázku
    * @var int (constant 'IMAGETYPE_XXX')
    */
   private $imageType = null;

   /**
    * Proměná obsahuje, jestli má být obrázku zachovány rozměry, nebo ořezán
    * @var boolean
    */
   private $cropNewImage = false;

   /**
    * Proměná s šířkou nového obrázku
    * @var integer
    */
   private $newImageWidth = 0;

   /**
    * Proměná s výškou nového obrázku
    * @var integer
    */
   private $newImageHeight = 0;

   /**
    * proměná s nastavenou kvalitou pro výstup JPEG
    * @var int
    */
   private $jpegQuality = 85;

   /**
    * Proměná s nastavenou kvalitou pro výstup PNG
    * @var int
    */
   private $pngQuality = 9;

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
    * Matedoa zjišťuje, jestli je daný soubor obrázek
    * @param boolean $reportErrors -- jestli mají být vyvolány chybové hlášky
    * @return boolean -- true pokud se jedná o obrázek se kterým umí pracovat
    */
   public function isImage($reportErrors = true) {
      $this->reportErrors = $reportErrors;

      $this->checkIsImage();
      return $this->isImage;
   }

   /**
    * Metoda načte informace o obrázku
    */
   private function checkIsImage() {
      //	Ověření existence obrázku
      if($this->getNameInput() != null AND $this->exist()){

         //		zjištění vlastností obrázků
         $imageProperty = getimagesize($this->getNameInput(true));
         $this->imageWidth = $imageProperty[0];
         $this->imageHeight = $imageProperty[1];
         $this->imageType = $imageProperty[2];
         /*
          * kvůli flashi je tady vyjímka protože flash se zpracovává v flashfile
          * a nelze mu měniti velikost ani jej resamplovat
          *
          * Flash má typ obrázku 4
          */
         if($this->imageType == null OR $this->imageType == IMAGETYPE_SWF){
            $this->isImage = false;
            if($this->reportErrors){
               $this->errMsg()->addMessage(_('Zadaný soubor není podporovaný obrázek'));
            }
         } else {
            $this->isImage = true;
         }
      } else {
         if($this->reportErrors){
            new CoreException(_('Soubor se zadaným obrázkem neexistuje'), 1);
         }
      }
   }

   /**
    * metoda nastaví rozměry obrázku
    *
    * @param integer -- šířka v px
    * @param integer -- výška v px
    */
   public function setDimensions($width, $height){
      $this->newImageWidth = $width;
      $this->newImageHeight = $height;
   }

   /**
    * Metoda zapíná/vypíná ořezání obrázku
    * @param boolean -- true pro zapnutí ořezání
    */
   public function setCrop($crop) {
      $this->cropNewImage = $crop;
   }

   /**
    * Metoda uloží obrázek ve stejném formátu, v jakém byl zadán
    *
    * @param string -- cílový adresář, kam se obrázek ukládá
    * @param int -- šířka výsledného obrázku
    * @param int -- výška výsledného obrázku
    * @param string -- nový název obrázku
    * @param int -- typ výsledného obrázku (constant IMAGETYPE_XXX)
    *
    * @return boolean -- true pokud se obrázek podařilo uložit
    */
   public function saveImage($dstDir, $width = null, $heigh = null, $newName = null, $imageType = null) {
      $saved = false;
      $tmpImage = $this->createTempImage();

      if($width == null){
         $width = $this->imageWidth;
      }
      if($heigh == null){
         $heigh = $this->imageHeight;
      }
      if($newName == null){
         $newName = $this->getName();
      }

      //		test jestli je zpracováván obrázek
      if($this->isImage()){
         //			Test názvu souboru
         $newName = $this->creatUniqueName($dstDir);
         $newImage = $this->resampleImage($tmpImage,$width,$heigh);

         if($imageType == null){
            $imageType = $this->imageType;
         }

         $saved = $this->saveNewImage($newImage, $imageType, $dstDir, $newName);
         imagedestroy($newImage);
      }
      return $saved;
   }

   /**
    * Metoda uloží JPEG obrázek do zadané cesty
    *
    * @param string -- cílový adresář, kam se obrázek ukládá
    * @param int -- šířka výsledného obrázku
    * @param int -- výška výsledného obrázku
    * @param string -- nový název obrázku
    */
   public function saveJpegImage($dstDir, $width = null, $heigh = null, $newName = null) {
      $this->saveImage($dstDir, $width, $heigh, $newName, IMAGETYPE_JPEG);
   }

   /**
    * Metoda uloží PNG obrázek do zadané cesty
    *
    * @param string -- cílový adresář, kam se obrázek ukládá
    * @param int -- šířka výsledného obrázku
    * @param int -- výška výsledného obrázku
    * @param string -- nový název obrázku
    */
   public function savePngImage($dstDir, $width = null, $heigh = null, $newName = null) {
      $this->saveImage($dstDir, $width, $heigh, $newName, IMAGETYPE_GIF);
   }

   /**
    * Metoda rozhoduje ze kterého typu obrázku se bude načítat obrázku
    *
    * @return image -- vrací objekt obrázku z původního obrázku
    */
   private function createTempImage() {
      //		Zjištění druhu obrázku a vytvoření pracovního obrázk
      switch ($this->imageType) {
         case IMAGETYPE_GIF:
            $tempImage = imagecreatefromgif($this->getNameInput(true));
            break;
         case IMAGETYPE_JPEG:
            $tempImage = imagecreatefromjpeg($this->getNameInput(true));
            break;
         case IMAGETYPE_PNG:
            $tempImage = imagecreatefrompng($this->getNameInput(true));
            break;
         case IMAGETYPE_WBMP:
            $tempImage = imagecreatefromwbmp($this->getNameInput(true));
            break;
         case IMAGETYPE_JPEG2000:
            $tempImage = imagecreatefromjpeg($this->getNameInput(true));
            break;
         default:
            $tempImage = false;
      };
      return $tempImage;
   }

   /**
    * Metoda přesampluje zadaný obrázek na nový obrázek
    *
    * @param image -- vytvořený obrázek z původního
    * @param int -- šířka nového obrázku
    * @param int -- výška nového obrázku
    */
      private function resampleImage($tempImage, $width, $height) {
         if(!$this->cropNewImage){
            //				obrázek je na šířku
            if($this->imageWidth > $this->imageHeight){
               $imageRate = $this->imageHeight/$this->imageWidth;
               $height = $imageRate*$width;
            }
            //				obrázek je na výšku
            else {
               $imageRate = $this->imageWidth/$this->imageHeight;
               $width = $imageRate*$height;
            }

            $newImage = imagecreatetruecolor($width, $height);
            ImageCopyResampled($newImage, $tempImage, 0,0,0,0, $width, $height, $this->imageWidth, $this->imageHeight);
         } else {
            //			Ořezání obrázku do jedné velikosti
            $newImage = imagecreatetruecolor($width, $height);

            $scale = (($width / $this->imageWidth) > ($height / $this->imageHeight)) ? ($width / $this->imageWidth) : ($height / $this->imageHeight); // vyber vetsi pomer a zkus to nejak dopasovat...
            $newW = $width/$scale;    // jak by mel byt zdroj velky (pro poradek :)
            $newH = $height/$scale;

            // ktera strana precuhuje vic (kvuli chybe v zaokrouhleni)
            if (($this->imageWidth - $newW) > ($this->imageHeight - $newH)) {
               //				na sirku
               $imageX = floor(($this->imageWidth - $newW)/2);
               $imageY = 0;
               $imageWidth = floor($newW);
               $imageHeight = $this->imageHeight;
            } else {
               //				na vysku
               $imageX = 0;
               $imageY = floor(($this->imageHeight - $newH)/2);
               $imageWidth = $this->imageWidth;
               $imageHeight = floor($newH);
            }

            ImageCopyResampled($newImage, $tempImage, 0,0, $imageX, $imageY, $width, $height, $imageWidth,$imageHeight);
         }
         return $newImage;
      }

      /**
       * Metoda uloží obrázek do souboru
       *
       * @param image $newImage -- obrázek který se má uložit
       * @param IMAGE_TYPE $type -- typ obrázku
       * @param string $dstDir -- cílový adresář pro uložení
       * @param string $fileName -- cílový soubor pro uložení
       *
       * @return boolean -- true pokud se obrázek podařilo uložit
       */
      private function saveNewImage($newImage, $type, $dstDir, $fileName) {
         $dirObj = new Dir();
         if($dirObj->checkDir($dstDir)){

            switch ($type) {
               case IMAGETYPE_GIF:
                  $saved = imagegif($newImage, $dstDir.$fileName);
                  break;
               case IMAGETYPE_PNG:
                  $saved = imagepng($newImage, $dstDir.$fileName, $this->pngQuality);
                  break;
               case IMAGETYPE_WBMP:
                  $saved = imagewbmp($newImage, $dstDir.$fileName);
                  break;
               case IMAGETYPE_JPEG2000:
                  $saved = imagejpeg($newImage, $dstDir.$fileName, $this->jpegQuality); //jen výstup do jpegu
                  break;
               case IMAGETYPE_JPEG:
                  default: // výchozí je jpeg
                     $saved = imagejpeg($newImage, $dstDir.$fileName, $this->jpegQuality);
                     break;
            }
            return $saved;
         } else {
            new CoreException(_('Nepodařilo se vytvořit adresář pro uložení obrázku'), 2);
         }
      }

   /**
    * Funkce vytvoří obrázek ze zadaného obrázku a uloží jej do specifikovaného souboru
    *
    * @param string -- zdrojový soubor ($_FILE['file']['tmp_file'])
    * @param string -- cesta a cílový soubor
    * @param integer -- max. šířka výsledného obrázku
    * @param integer -- max. výška výsledného obrázku
    * @param boolean -- true pro ořezání obrázku na zadané velikosti, false pouze pro změnu velikosti
    * @param integer -- výsledná kvalita jpg komprese (default 85)
    * @param integer -- výsledná kvalit png komprese (default 9
    * @return boolean -- true, jestliže byl obrázek uspěšně vytvořen a ulože
    */
      public function createImage($srcFile, $destFile, $width, $height, $cropSize = false, $jpegQuality = 85, $pngQuality = 9){
         //		Výpočet nové velikosti
         $imageProperty = getimagesize($srcFile);
         if($newImageType != false){
            if($cropSize == false){
               //				obrázek je na šířku
               if($imageProperty[0] > $imageProperty[1]){
                  $imageRate = $imageProperty[1]/$imageProperty[0];
                  $height = $imageRate*$width;
               }
               //				obrázek je na výšku
               else {
                  $imageRate = $imageProperty[0]/$imageProperty[1];
                  $width = $imageRate*$height;
               }

               $newImage = imagecreatetruecolor($width, $height);
               imagecopyresampled($newImage, $tempImage, 0,0,0,0, $width, $height, $imageProperty[0], $imageProperty[1]);
            } else {
               //			Ořezání obrázku do jedné velikosti
               $newImage = imagecreatetruecolor($width, $height);

               $scale = (($width / $imageProperty[0]) > ($height / $imageProperty[1])) ? ($width / $imageProperty[0]) : ($height / $imageProperty[1]); // vyber vetsi pomer a zkus to nejak dopasovat...
               $newW = $width/$scale;    // jak by mel byt zdroj velky (pro poradek :)
               $newH = $height/$scale;

               // ktera strana precuhuje vic (kvuli chybe v zaokrouhleni)
               if (($imageProperty[0] - $newW) > ($imageProperty[1] - $newH)) {
                  $imageX = floor(($imageProperty[0] - $newW)/2);
                  $imageY = 0;
                  $imageWidth = floor($newW);
                  $imageHeight = $imageProperty[1];

                  //					$src = array(floor(($imageProperty[0] - $newW)/2), 0, floor($newW), $imageProperty[1]);
               }
               else {
                  $imageX = 0;
                  $imageY = floor(($imageProperty[1] - $newH)/2);
                  $imageWidth = $imageProperty[0];
                  $imageHeight = floor($newH);

                  //					$src = array(0, floor(($imageProperty[1] - $newH)/2), $imageProperty[0], floor($newH));
               }
               ImageCopyResampled($newImage, $tempImage, 0,0, $imageX, $imageY, $width, $height, $imageWidth,$imageHeight);
            }

            //			uložení obrázku
            if ($newImageType == IMAGETYPE_JPEG){
               $newImageFunction($newImage, $destFile, $jpegQuality);
            } else if($newImageType == IMAGETYPE_JPEG){
               $newImageFunction($newImage, $destFile, $pngQuality);
            } else {
               $newImageFunction($newImage, $destFile);
            }
            ImageDestroy($newImage);
            ImageDestroy($tempImage);

            return true;
         }
         return false;
      }

   /**
    * Metoda vrací rozměr původního obrázku - Šířku
    *
    * @return integer -- šířka obrázku
    */
      public function getOriginalWidth() {
         return $this->imageWidth;
      }

   /**
    * Metoda vrací rozměr původního obrázku - Výšku
    *
    * @return integer -- výška obrázku
    */
      public function getOriginalHeight() {
         return $this->imageHeight;
      }
   }
?>