<?php
/**
 * Třída pro práci s obrázky
 * Třída pro základní práci s obrázky. Umožňuje jejich ukládání, ořezávání,
 * změnu velikost a změnu formátu obrázku.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: file_image.class.php 642 2009-08-18 09:23:18Z jakub $ VVE3.9.4 $Revision: 642 $
 * @author        $Author: jakub $ $Date: 2009-08-18 11:23:18 +0200 (Út, 18 srp 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-08-18 11:23:18 +0200 (Út, 18 srp 2009) $
 * @abstract 		Třída pro práci s obrázky
 */

class Filesystem_File_Image extends Filesystem_File {
/**
 * Proměná obsahuje jestli je soubor obrázek
 * @var boolean
 */
   private $isImage = null;

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
   private $cropImage = false;

   /**
    * Proměná s šířkou nového obrázku
    * @var integer
    */
   private $newImageWidth = false;

   /**
    * Proměná s výškou nového obrázku
    * @var integer
    */
   private $newImageHeight = false;

   /**
    * proměná s nastavenou kvalitou pro výstup
    * @var int
    */
   private $quality = 95;

   /**
    * Pracovní obrázek, používá se při přesamplování
    * @var resource
    */
   private $workingImage = null;

   /**
    * Konstruktor třídy
    * @param string/File $file -- název souboru nebo objekt typu File
    * @param string $dir -- (option) název adresáře se souborem může být uveden
    * v názvu souboru
    */
   function __construct($file, $dir = null, $reportErrors = true) {
      parent::__construct($file, $dir, $reportErrors);
   }

   /**
    * Matedoa zjišťuje, jestli je daný soubor obrázek
    * @param boolean $reportErrors -- jestli mají být vyvolány chybové hlášky
    * @return boolean -- true pokud se jedná o obrázek se kterým umí pracovat
    */
   public function isImage() {
      if($this->isImage === null) {
         $this->checkIsImage();
      }
      return $this->isImage;
   }

   /**
    * Metoda vrací zdroj pracovního obrázku
    * @return resource
    */
   public function getWorkingImage() {
      return $this->workingImage;
   }

   /**
    * Metoda nastaví zdroj pracovního obrázku (využití při úpravě obrázku externími funkcemi)
    * @param resource $imageRes -- zdroj obrázku
    */
   public function setWorkingImage($imageRes) {
      $this->workingImage = $imageRes;
   }

   /**
    * Metoda načte informace o obrázku
    */
   private function checkIsImage() {
   //	Ověření existence obrázku
      if($this->getName() != null AND $this->exist()) {
      //		zjištění vlastností obrázků
         $imageProperty = @getimagesize($this->getName(true));
         $this->imageWidth = $imageProperty[0];
         $this->imageHeight = $imageProperty[1];
         $this->imageType = $imageProperty[2];
         /*
          * kvůli flashi je tady vyjímka protože flash se zpracovává v File_Flash
          * a nelze mu měniti velikost ani jej resamplovat
          *
          * Flash má typ obrázku 4 a 13 (IMAGETYPE_SWF a IMAGETYPE_SWC)
          */
         if($this->imageType == null OR $this->imageType == IMAGETYPE_SWF
             OR $this->imageType == IMAGETYPE_SWC) {
            $this->isImage = false;
            $this->isError = true;
            if($this->reportErrors()) {
               $this->errMsg()->addMessage(sprintf(_('Soubor "%s" není podporovaný obrázek'),$this->getName()));
            }
         } else {
            $this->isImage = true;
         }
      } else {
         $this->isImage = true;
         if($this->reportErrors()) {
            throw new CoreException(sprintf(_('Obrázek "%s" neexistuje'),$this->getName(true)), 1);
         }
         $this->isError = true;
      }
   }

   /**
    * metoda nastaví rozměry obrázku
    *
    * @param integer -- šířka v px
    * @param integer -- výška v px
    */
   public function setDimensions($width, $height) {
      $this->newImageWidth = $width;
      $this->newImageHeight = $height;
   }

   /**
    * Metoda zapíná/vypíná ořezání obrázku
    * @param boolean -- true pro zapnutí ořezání
    */
   public function setCrop($crop) {
      $this->cropImage = $crop;
   }

   /**
    * Metoda rozhoduje ze kterého typu obrázku se bude načítat obrázku
    */
   public function createWorkingImage() {
      if(VVE_USE_IMAGEMAGICK != true) {
      //		Zjištění druhu obrázku a vytvoření pracovního obrázku
         switch ($this->imageType) {
            case IMAGETYPE_GIF:
               $this->workingImage = imagecreatefromgif($this->getName(true));
               break;
            case IMAGETYPE_JPEG:
               $this->workingImage = imagecreatefromjpeg($this->getName(true));
               break;
            case IMAGETYPE_PNG:
               $this->workingImage = imagecreatefrompng($this->getName(true));
               break;
            case IMAGETYPE_WBMP:
               $this->workingImage = imagecreatefromwbmp($this->getName(true));
               break;
            case IMAGETYPE_JPEG2000:
               $this->workingImage = imagecreatefromjpeg($this->getName(true));
               break;
            default:
               $this->isError = true;
               if($this->reportErrors()) {
                  throw new UnexpectedValueException(_('Soubor je neplatný typ obrázku'), 2);
               }
         };
      }
   }

   /**
    * Metoda přesampluje pracovní obrázek
    * @param int $maxWidth -- šířka nového obrázku
    * @param int $maxHeight -- výška nového obrázku
    */
   public function resampleImage($maxWidth = null, $maxHeight = null, $crop = null) {
      if($this->workingImage === null|false) {
         $this->loadImage();
      }
      if($crop === null) {
         $crop = $this->cropImage;
      }

      if($maxWidth === null) {
         $maxWidth = $this->imageWidth;
         if($this->newImageWidth !== false) {
            $maxWidth = $this->newImageWidth;
         }
      }
      if($maxHeight === null) {
         $maxHeight = $this->imageHeight;
         if($this->newImageHeight !== false) {
            $maxHeight = $this->newImageHeight;
         }
      }

      if(VVE_USE_IMAGEMAGICK != true) {
         if($crop === false) {
            $width = $this->imageWidth;
            $height = $this->imageHeight;
            if($width > $maxWidth && $width) {
               $rate = $maxWidth / $width;
               $width = $maxWidth;
               $height = ceil($rate * $height);
            }
            if($height > $maxHeight && $maxHeight) {
               $rate = $maxHeight / $height;
               $height = $maxHeight;
               $width = ceil($rate * $width);
            }
            $newImage = imagecreatetruecolor($width, $height);
            // Zapnutí alfy, tj průhlednost
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);//                     d d s s  d       d        s                  s
            if(!imagecopyresampled($newImage, $this->workingImage, 0,0,0,0, $width, $height, $this->imageWidth, $this->imageHeight)) {
               if($this->reportErrors())
                  throw new UnexpectedValueException(_('Chyba při resamplování obrázku'), 3);
               $this->isError = true;
            }
         } else {
            // pokud je obrázek v menším rozměru než výsledný, změnšíme ořezávanou plochu
            if($maxWidth > $this->imageWidth) $maxWidth = $this->imageWidth;
            if($maxHeight > $this->imageHeight) $maxHeight = $this->imageHeight;
            //			Ořezání obrázku do jedné velikosti
            $scale = (($maxWidth / $this->imageWidth) >= ($maxHeight / $this->imageHeight)) ? ($maxWidth / $this->imageWidth) : ($maxHeight / $this->imageHeight); // vyber vetsi pomer a zkus to nejak dopasovat...
            $newW = $maxWidth/$scale;    // jak by mel byt zdroj velky (pro poradek :)
            $newH = $maxHeight/$scale;
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
            $newImage = imagecreatetruecolor($maxWidth, $maxHeight);
            // Zapnutí alfy, tj průhlednost
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);//                     d d  s        s        d          d           s           s
            if(!imagecopyresampled($newImage, $this->workingImage, 0,0, $imageX, $imageY, $maxWidth, $maxHeight, $imageWidth,$imageHeight)) {
               if($this->reportErrors())
                  throw new UnexpectedValueException(_('Chyba při resamplování obrázku'), 3);
               $this->isError = true;
            }
         }
         $this->workingImage = $newImage;
      } else {
      // použití imagemagick jako resizer
         if($crop == true) {
            $offsetX = $offsetY = 0;

            $resizeString = null;
            //zjištění největší velikossti
            if($maxWidth > $maxHeight) {
               $resizeString = $maxWidth.'x';
               $offsetY = round(($maxWidth/$this->imageWidth*$this->imageHeight-$maxHeight)/2,0);
            } else if($maxWidth < $maxHeight) {
                  $resizeString = 'x'.$maxHeight;
                  $offsetX = round(($maxHeight/$this->imageHeight*$this->imageWidth-$maxWidth)/2,0);
               } else {
                  if($this->imageWidth > $this->imageHeight) {
                     $resizeString = 'x'.$maxHeight;
                     $offsetX = round(($maxHeight/$this->imageHeight*$this->imageWidth-$maxWidth)/2,0);
                  } else {
                     $resizeString = $maxWidth.'x';
                     $offsetY = round(($maxWidth/$this->imageWidth*$this->imageHeight-$maxHeight)/2,0);
                  }
               }
            exec('convert -resize '.escapeshellarg($resizeString).' '.$this->getName(true).' '.(string)$dir.$newName);

            exec('convert -crop '.escapeshellarg($maxWidth).'x'.escapeshellarg($maxHeight)
                .'+'.$offsetX.'+'.$offsetY.' '.(string)$dir.$newName.' '.(string)$dir.$newName);
         } else {
            exec('convert -resize '.escapeshellarg($maxWidth).'x'.escapeshellarg($maxHeight)
                .' '.$this->getName(true).' '.$this->getName(true));
         }
      }
   }

   /**
    * Metoda přetočí pracovní obrázek
    * @param float $angle -- úhel
    * @param int $bgColor -- barva pozadí viz. man php fce imagerotate
    */
   public function rotateImage($angle, $bgColor = 0, $ignoreTransparent = 0) {
      if(VVE_USE_IMAGEMAGICK != true) {

         if($this->workingImage === null OR $this->workingImage === false) {
            $this->loadImage();
         }

         if(function_exists("imagerotate")) {
            $this->workingImage = imagerotate($this->workingImage, $angle, $bgColor, $ignoreTransparent);
         } else {
            $this->imagerotateEquivalent($angle, $bgColor, $ignoreTransparent);
         }
      } else {
      //         exec('convert -rotate '.escapeshellarg($angle).' -background '.escapeshellarg($bgColor).' '.$this->getName(true).' '.$this->getName(true));
         exec('convert -rotate '.escapeshellarg($angle).' '.$this->getName(true).' '.$this->getName(true));
      }
   }

   /**
    * Metoda převrátí obrázek horizontálně
    * @param string $axis -- podle které osy se má převrátit obrázek
    */
   public function flip($axis = 'x') {
      switch ($axis) {
         case 'y':
            if(VVE_USE_IMAGEMAGICK != true) {

            } else {
               exec('convert -flop '.$this->getName(true).' '.$this->getName(true));
            }

            break;
         case 'x':
         default:
            if(VVE_USE_IMAGEMAGICK != true) {

            } else {
               exec('convert -flip '.$this->getName(true).' '.$this->getName(true));
            }
            break;
      }
   }

   /**
    * Metoda uloží obrázek do souboru
    *
    * @param IMAGE_TYPE $type -- typ obrázku
    * @param string $newImageDirName -- název adresáře pro uložení
    *
    * @return boolean -- true pokud se obrázek podařilo uložit
    */
   public function saveWorkingImage($type, $newDir=null, $newImageName = null) {
      if(VVE_USE_IMAGEMAGICK != true) {
      // dodělat smazání původního obrázku pokud již existuje
         if($newDir == null) {
            $newDir = $this->getDir();
         }
         if($newImageName == null) {
            $newImageName = $this->getName();
         }

         switch ($type) {
            case IMAGETYPE_GIF:
               $saved = @imagegif($this->workingImage, $newDir.$newImageName);
               break;
            case IMAGETYPE_PNG:
               imagealphablending($this->workingImage, false);
               imagesavealpha($this->workingImage, true);
               $saved = @imagepng($this->workingImage,$newDir.$newImageName, round($this->quality/10,0)-1);
               break;
            case IMAGETYPE_WBMP:
               $saved = @imagewbmp($this->workingImage, $newDir.$newImageName);
               break;
            case IMAGETYPE_JPEG2000:
            //jen výstup do jpegu
               $saved = @imagejpeg($this->workingImage, $newDir.$newImageName, $this->quality);
               break;
            case IMAGETYPE_JPEG:
            default: // výchozí je jpeg
               $saved = @imagejpeg($this->workingImage, $newDir.$newImageName, $this->quality);
               break;
         }
         // nastavení práv k obrázku na zápis pro všechny, kvůli ftp účtu ať může mazat
         if(!$saved) {
            $this->isError = true;
            if($this->reportErrors())
               throw new InvalidArgumentException(_("Chyba při ukládání pracovního obrázku. Zkontrolujte práva k adresáři."), 3);
         }
         $this->setRights(0777);
      } else {
      //         convert imagemagick to another formt
         $this->saveAs($this->getDir(), $this->newImageWidth, $this->newImageHeight, $this->cropImage);
      }
   }

   /**
    * Metoda načte obrázek z disku
    */
   public function loadImage() {
      if($this->isImage()) {
         $this->createWorkingImage();
      }
   }

   /**
    * Metoda uloží obrázek
    * @param int $width -- šířka obrázku
    * @param int $height -- výška obrázku
    */
   public function save($width = false, $height = false) {
      if($this->workingImage === null){
         $this->loadImage();
      }
   //		test jestli je zpracováván obrázek
      if($this->isImage()) {
         if($width !== false) $this->newImageWidth = $width;
         if($height !== false) $this->newImageHeight = $height;
         $this->saveWorkingImage($this->imageType);
      }
   }

   /**
    * Metoda pro rotaci obrázků, je tu protože né všechny php mají podporu pro rotate
    * @param <type> $srcImg
    * @param <type> $angle
    * @param <type> $bgcolor
    * @param <type> $ignore_transparent
    * @return <type>
    */
   private function imagerotateEquivalent($angle, $bgcolor, $ignore_transparent = 0) {
      function rotateX($x, $y, $theta) {
         return $x * cos($theta) - $y * sin($theta);
      }
      function rotateY($x, $y, $theta) {
         return $x * sin($theta) + $y * cos($theta);
      }

      $srcw = imagesx($this->workingImage);
      $srch = imagesy($this->workingImage);

      //Normalize angle
      $angle %= 360;
      //Set rotate to clockwise
      $angle = -$angle;

      if($angle == 0) {
         if ($ignore_transparent == 0) {
            imagesavealpha($this->workingImage, true);
         }
         return $this->workingImage;
      }

      // Convert the angle to radians
      $theta = deg2rad ($angle);

      //Standart case of rotate
      if ( (abs($angle) == 90) || (abs($angle) == 270) ) {
         $width = $srch;
         $height = $srcw;
         if ( ($angle == 90) || ($angle == -270) ) {
            $minX = 0;
            $maxX = $width;
            $minY = -$height+1;
            $maxY = 1;
         } else if ( ($angle == -90) || ($angle == 270) ) {
               $minX = -$width+1;
               $maxX = 1;
               $minY = 0;
               $maxY = $height;
            }
      } else if (abs($angle) === 180) {
            $width = $srcw;
            $height = $srch;
            $minX = -$width+1;
            $maxX = 1;
            $minY = -$height+1;
            $maxY = 1;
         } else {
         // Calculate the width of the destination image.
            $temp = array (rotateX(0, 0, 0-$theta),
                rotateX($srcw, 0, 0-$theta),
                rotateX(0, $srch, 0-$theta),
                rotateX($srcw, $srch, 0-$theta)
            );
            $minX = floor(min($temp));
            $maxX = ceil(max($temp));
            $width = $maxX - $minX;

            // Calculate the height of the destination image.
            $temp = array (rotateY(0, 0, 0-$theta),
                rotateY($srcw, 0, 0-$theta),
                rotateY(0, $srch, 0-$theta),
                rotateY($srcw, $srch, 0-$theta)
            );
            $minY = floor(min($temp));
            $maxY = ceil(max($temp));
            $height = $maxY - $minY;
         }

      $destimg = imagecreatetruecolor($width, $height);
      if ($ignore_transparent == 0) {
         imagefill($destimg, 0, 0, imagecolorallocatealpha($destimg, 255,255, 255, 127));
         imagesavealpha($destimg, true);
      }

      // sets all pixels in the new image
      for($x=$minX; $x<$maxX; $x++) {
         for($y=$minY; $y<$maxY; $y++) {
         // fetch corresponding pixel from the source image
            $srcX = round(rotateX($x, $y, $theta));
            $srcY = round(rotateY($x, $y, $theta));
            if($srcX >= 0 && $srcX < $srcw && $srcY >= 0 && $srcY < $srch) {
               $color = imagecolorat($this->workingImage, $srcX, $srcY );
            } else {
               $color = $bgcolor;
            }
            imagesetpixel($destimg, $x-$minX, $y-$minY, $color);
         }
      }
      $this->workingImage = $destimg;
   }

   /**
    * Metoda uloží obrázek ve stejném formátu, v jakém byl zadán
    *
    * @param string -- cílový adresář, kam se obrázek ukládá
    * @param int -- šířka výsledného obrázku
    * @param int -- výška výsledného obrázku
    * @param bool -- jestli se má obrázek ořezávat nebo zmenšovat
    * @param string -- nový název obrázku
    * @param int -- typ výsledného obrázku (constant IMAGETYPE_XXX)
    *
    * @return boolean -- true pokud se obrázek podařilo uložit
    *
    * @todo není implementována matoda pro volbu typu obrázku,
    * ověřit vytvoření obrázku podle nového jména,
    * dodělat při vypnutém imagemagicku
    */
   public function saveAsResampledImage($dstDir, $maxWidth = null, $maxHeight = null, $crop = false, $newName = null, $imageType = null) {
      if($this->workingImage === null|false) {
         $this->loadImage();
      }

      if($maxWidth === false|null) {
         $maxWidth = $this->imageWidth;
      }
      if($maxHeight === false|null) {
         $maxHeight = $this->imageHeight;
      }

      $dir = new Filesystem_Dir($dstDir);
      $dir->checkDir();

      if(VVE_USE_IMAGEMAGICK != true) {
      //		test jestli je zpracováván obrázek
         if($this->isImage()) {
         //	Test názvu souboru Je třeba?
         // $newName = $this->creatUniqueName($dstDir);
            $this->resampleImage($maxWidth,$maxHeight, $crop);
            if($imageType == null) {
               $imageType = $this->imageType;
            }

            $this->saveWorkingImage($imageType, $dir, $newName);
            imagedestroy($this->workingImage);
            $this->workingImage = null;
         }
      } else {
         if($newName === null) {
            $newName = $this->getName();
         }

         // použití imagemagick jako resizer
         if($crop === true) {
            $offsetX = $offsetY = 0;

            $resizeString = null;
            //zjištění největší velikossti
            if($maxWidth > $maxHeight) {
               $resizeString = $maxWidth.'x';
               $offsetY = round(($maxWidth/$this->imageWidth*$this->imageHeight-$maxHeight)/2,0);
            } else if($maxWidth < $maxHeight) {
                  $resizeString = 'x'.$maxHeight;
                  $offsetX = round(($maxHeight/$this->imageHeight*$this->imageWidth-$maxWidth)/2,0);
               } else {
                  if($this->imageWidth > $this->imageHeight) {
                     $resizeString = 'x'.$maxHeight;
                     $offsetX = round(($maxHeight/$this->imageHeight*$this->imageWidth-$maxWidth)/2,0);
                  } else {
                     $resizeString = $maxWidth.'x';
                     $offsetY = round(($maxWidth/$this->imageWidth*$this->imageHeight-$maxHeight)/2,0);
                  }
               }
            exec('convert -resize '.escapeshellarg($resizeString).' -quality '.$this->quality.' '.$this->getName(true).' '.(string)$dir.$newName);

            exec('convert -crop '.escapeshellarg($maxWidth).'x'.escapeshellarg($maxHeight)
                .'+'.$offsetX.'+'.$offsetY.' -quality '.$this->quality.' '.(string)$dir.$newName.' '.(string)$dir.$newName);

         } else {
            exec('convert -resize '.escapeshellarg($maxWidth).'x'.escapeshellarg($maxHeight)
                .' '.$this->getName(true).' '.(string)$dir.$newName);
         }
         unset ($dir);
      }
   }

   /**
    * Metoda uloží obrázek jako nový obrázek (alias pro saveAsResampledImage())
    *
    * @param string -- cílový adresář, kam se obrázek ukládá
    * @param int -- šířka výsledného obrázku
    * @param int -- výška výsledného obrázku
    * @param bool -- jestli se má obrázek ořezávat nebo zmenšovat
    * @param string -- nový název obrázku
    * @param int -- typ výsledného obrázku (constant IMAGETYPE_XXX)
    *
    * @return boolean -- true pokud se obrázek podařilo uložit
    *
    *
    */
   public function saveAs($dstDir, $width = null, $heigh = null, $crop = false, $newName = null, $imageType = null) {
      return $this->saveAsResampledImage($dstDir, $width, $heigh, $crop, $newName, $imageType);
   }

   /**
    * Metoda provede ořez pracovního obrázku
    * @param <type> $srcX
    * @param <type> $srcD
    * @param <type> $dstW
    * @param <type> $dstH
    */
   public function crop($srcX, $srcY, $dstX, $dstY) {
      if($this->workingImage === null|false) {
         $this->loadImage();
      }
      if(VVE_USE_IMAGEMAGICK != true) {

         $newImage = imagecreatetruecolor($dstX-$srcX, $dstY-$srcY);
         // Zapnutí alfy, tj průhlednost
         imagealphablending($newImage, false);
         imagesavealpha($newImage, true);
         if(!@imagecopyresampled($newImage, $this->workingImage,
         //d d s s
         0,0,$srcX,$srcY,
         $dstX-$srcX, $dstY-$srcY, $dstX-$srcX, $dstY-$srcY)) {
            if($this->reportErrors())
               throw new UnexpectedValueException(_('Chyba při resamplování obrázku'), 3);
            $this->isError = true;
         }
         $this->workingImage = $newImage;
      } else {

      }
   }


   /**
    * Metoda provede ořez pracovního obrázku a uloží jej na zadané místo
    * @param <type> $dstDir
    * @param <type> $srcX
    * @param <type> $srcD
    * @param <type> $srcW
    * @param <type> $srcH
    */
   public function cropAndSave($dstDir, $imgW, $imgH, $srcX, $srcY, $srcW, $srcH) {
      if($this->workingImage === null|false) {
         $this->loadImage();
      }
      if(VVE_USE_IMAGEMAGICK != true) {

         $newImage = imagecreatetruecolor($imgW, $imgH);
         // Zapnutí alfy, tj průhlednost
         imagealphablending($newImage, false);
         imagesavealpha($newImage, true);
         if(!@imagecopyresampled($newImage, $this->workingImage,
         //d d s s
         0,0,$srcX,$srcY,
         $imgW, $imgH, $srcW, $srcH)) {
            if($this->reportErrors())
               throw new UnexpectedValueException(_('Chyba při resamplování obrázku'), 3);
            $this->isError = true;
         }
         $this->workingImage = $newImage;
         $this->saveWorkingImage($this->imageType, $dstDir);
      } else {

      }
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