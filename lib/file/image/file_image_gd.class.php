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

class File_Image_Gd extends File_Image_Base {
   /**
    * Data s obrázkem
    * @var type 
    */
   protected $imageData = null;

   public function crop($x, $y, $w, $h)
   {
      $this->loadImageData();
      
      $tempImg = imagecreatetruecolor($w, $h); 
      imagecopyresampled($tempImg, $this->imageData, 0, 0, $x, $y,  $w, $h, $x+$w, $y+$h);
      
      $this->imageData = $tempImg;
      
      // úprava rozměrů
      $this->width  = imagesx($this->imageData);  
      $this->height = imagesy($this->imageData);
      
      return $this;
   }
   
   public function resize($w, $h, $option = self::RESIZE_AUTO)
   {
      $this->loadImageData();
      
      // *** Get optimal width and height - based on $option  
      $optionArray = $this->getDimensions($w, $h, $option);

      $optimalWidth = $optionArray['optimalWidth'];
      $optimalHeight = $optionArray['optimalHeight'];

      // *** Resample - create image canvas of x, y size  
      $imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
      imagealphablending($imageResized, false);
      imagesavealpha($imageResized, true);
      
      imagecopyresampled($imageResized, $this->imageData, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);
      
      $this->imageData = $imageResized;
      
      // *** if option is 'crop', then crop too  
      if ($option == self::RESIZE_CROP) {
         // *** Find center - this will be used for the crop  
         $cropStartX = ( $optimalWidth  / 2) - ( $w / 2 );  
         $cropStartY = ( $optimalHeight / 2) - ( $h / 2 );  
  
         // *** Now crop from center to exact requested size  
         $imageCroped = imagecreatetruecolor($w , $h);  
         imagealphablending($imageCroped, false);
         imagesavealpha($imageCroped, true);//
         
         imagecopyresampled($imageCroped, $this->imageData , 0, 0, $cropStartX, $cropStartY, $w, $h , $w, $h);
         $this->imageData = $imageCroped;
      }
      // úprava rozměrů
      $this->width  = imagesx($this->imageData);  
      $this->height = imagesy($this->imageData);
      
      return $this;
   }
   
   public function rotate($degree = 180, $bgColor = 0, $ignoreTransparent = 0)
   {
      $this->loadImageData();
      
      if(function_exists("imagerotate")) {
         $this->imageData = imagerotate($this->imageData, $degree, $bgColor, $ignoreTransparent);
      } else {
         $this->imageRotateEquivalent($degree, $bgColor, $ignoreTransparent);
      }
      
      // úprava rozměrů
      $this->width  = imagesx($this->imageData);  
      $this->height = imagesy($this->imageData);
      
      return $this;
   }
   
   public function flip($type = self::FLIP_HORIZONTAL)
   {
      $this->loadImageData();
      
      if($type == self::FLIP_VERTICAL){
         $size_x = imagesx($this->imageData);
         $size_y = imagesy($this->imageData);
         $temp = imagecreatetruecolor($size_x, $size_y);
         $x = imagecopyresampled($temp, $this->imageData, 0, 0, 0, ($size_y-1), $size_x, $size_y, $size_x, 0-$size_y);
         if ($x) {
            $this->imageData = $temp;
         } else {
            die("Unable to flip image");
         } 
      } else {
         $size_x = imagesx($this->imageData);
         $size_y = imagesy($this->imageData);
         $temp = imagecreatetruecolor($size_x, $size_y);
         $x = imagecopyresampled($temp, $this->imageData, 0, 0, ($size_x-1), 0, $size_x, $size_y, 0-$size_x, $size_y);
         if ($x) {
            $this->imageData = $temp;
         } else {
            die("Unable to flip image");
         }
      }
      
      return $this;
   }
   
   /**
    * Uložení obrázku do jiného formátu (automaticky upraví příponu) nebo ho vypíše
    * @param string/File $file -- objek souboru nebo název obrázku
    * @param const $format -- formát IMAGETYPE_XXX, null pro zachování formátu
    */
   public function write($file = null, $format = null)
   {
      $this->loadImageData();
      
      $type = $format == null ? $this->imageType : $format;
      switch($type)  
      {  
        case IMAGETYPE_JPEG:  
            if (imagetypes() & IMG_JPG) {  
               imagejpeg($this->imageData, $file, $this->quality);  
            }  
            break;  
        case IMAGETYPE_GIF:  
            if (imagetypes() & IMG_GIF) {  
               imagegif($this->imageData, $file);  
            }  
            break;  
        case IMAGETYPE_PNG:  
            // *** Scale quality from 0-100 to 0-9  
            $scaleQuality = round(($this->quality/100) * 9);  
            if (imagetypes() & IMG_PNG) {  
               imagepng($this->imageData, $file, 9-$scaleQuality);  // need inver 0 for best
            }
            break;  
        case IMAGETYPE_BMP:  
            if (imagetypes() & IMG_WBMP) {  
               imagewbmp($this->imageData, $file);  
            }  
            break;  
      }  
   }

   public function filter($filter, $arg1 = null, $arg2 = null, $arg3 = null)
   {
      $this->loadImageData();
      imagefilter($this->imageData, $filter, $arg1, $arg2, $arg3);
      return $this;
   }
   
   public function watermark(File_Image $img, $params = array())
   {
      $this->loadImageData();
      
      $params = array_merge($this->baseWaterMarkParams, $params);
      
      $posX = $posY = 0;
      
      $wImgRes = $this->createImageResource((string)$img);
      $markW = imagesx($wImgRes);
      $markH = imagesy($wImgRes);

      $waterMarkImage = null;
      
      switch ($params['valign']) {
         case 'top':
            $posY = 0 + $params['yoffset'];
            break;
         case 'center':
            $posY = $this->getHeight()/2 - $markH/2 + $params['yoffset'];
            break;
         case 'bottom':
         default:
            $posY = $this->getHeight() - $markH - $params['yoffset'];
            break;
      }
      switch ($params['halign']) {
         case 'left':
            $posX = 0 + $params['xoffset'];
            break;
         case 'center':
            $posX = $this->getWidth()/2 - $markW/2 + $params['xoffset'];
            break;
         case 'right':
         default:
            $posX = $this->getWidth() - $markW - $params['xoffset'];
            break;
      }
      
//      if(!@imagecopymerge($this->imageData, $wImgRes, $posX, $posY, 0, 0, $markW, $markH, $params['opacity'])){
      if(!@imagecopyresampled($this->imageData, $wImgRes, $posX, $posY, 0, 0, $markW, $markH, $markW, $markH)){
         throw new File_Image_Exception($this->tr('Chyba při vytváření vodoznaku na obrázku'));
      }
      
      return $this;
   }
   
   
   /*** PRIVATE METHODS  ***/
   
   /**
    * Načte data obrázku pro práci s ním
    */
   private function loadImageData()
   {
      if($this->imageData != null){
         return;
      }
      $this->imageData = $this->createImageResource((string)$this->file, $this->imageType);
      $this->width  = imagesx($this->imageData);  
      $this->height = imagesy($this->imageData);
   }
   
   /**
    * načte obrázek a vytvoří zdroj pro další zpracování
    * @param string $file -- cesta k souboru
    * @return resource
    */
   private function createImageResource($file, $type = null)
   {
      $imageData = null;
      if($type == null){
         list($width, $height, $type, $attr) = getimagesize($file);
      }
      
      switch ($type) {
         case IMAGETYPE_GIF:
            $imageData = @imagecreatefromgif($file);
            imagealphablending($imageData, false);
            imagesavealpha($imageData, true);//
            break;
         case IMAGETYPE_JPEG:
            $imageData = @imagecreatefromjpeg($file);
            break;
         case IMAGETYPE_PNG:
            $imageData = @imagecreatefrompng($file);
            imagealphablending($imageData, false);
            imagesavealpha($imageData, true);//
            break;
         case IMAGETYPE_WBMP:
            $imageData = @imagecreatefromwbmp($file);
            break;
         case IMAGETYPE_JPEG2000:
            $imageData = @imagecreatefromjpeg($file);
            break;
         default:
            throw new UnexpectedValueException( sprintf( $this->tr('Soubor % je neplatný typ obrázku'), $file ) );
            break;
      };
      return $imageData;
   }

   private function getDimensions($newWidth, $newHeight, $option)
   {
      switch ($option) {
         case self::RESIZE_EXACT:
            $optimalWidth = $newWidth;
            $optimalHeight = $newHeight;
            break;
         case self::RESIZE_PORTRAIT:
            $optimalWidth = $this->getSizeByFixedHeight($newHeight);
            $optimalHeight = $newHeight;
            break;
         case self::RESIZE_LANDSCAPE:
            $optimalWidth = $newWidth;
            $optimalHeight = $this->getSizeByFixedWidth($newWidth);
            break;
         case self::RESIZE_AUTO:
            $optionArray = $this->getSizeByAuto($newWidth, $newHeight);
            $optimalWidth = $optionArray['optimalWidth'];
            $optimalHeight = $optionArray['optimalHeight'];
            break;
         case self::RESIZE_CROP:
            $optionArray = $this->getOptimalCrop($newWidth, $newHeight);
            $optimalWidth = $optionArray['optimalWidth'];
            $optimalHeight = $optionArray['optimalHeight'];
            break;
      }
      return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
   }
   
   private function getSizeByFixedHeight($newHeight)
   {
      $ratio = $this->width / $this->height;
      $newWidth = $newHeight * $ratio;
      return $newWidth;
   }

   private function getSizeByFixedWidth($newWidth)
   {
      $ratio = $this->height / $this->width;
      $newHeight = $newWidth * $ratio;
      return $newHeight;
   }

   private function getSizeByAuto($newWidth, $newHeight)
   {
      if ($this->height < $this->width) {
      // *** Image to be resized is wider (landscape)  
         $optimalWidth = $newWidth;
         $optimalHeight = $this->getSizeByFixedWidth($newWidth);
      } elseif ($this->height > $this->width) {
      // *** Image to be resized is taller (portrait)  
         $optimalWidth = $this->getSizeByFixedHeight($newHeight);
         $optimalHeight = $newHeight;
      } else {
      // *** Image to be resizerd is a square  
         if ($newHeight < $newWidth) {
            $optimalWidth = $newWidth;
            $optimalHeight = $this->getSizeByFixedWidth($newWidth);
         } else if ($newHeight > $newWidth) {
            $optimalWidth = $this->getSizeByFixedHeight($newHeight);
            $optimalHeight = $newHeight;
         } else {
            // *** Sqaure being resized to a square  
            $optimalWidth = $newWidth;
            $optimalHeight = $newHeight;
         }
      }

      return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
   }

   private function getOptimalCrop($newWidth, $newHeight)
   {

      $heightRatio = $this->height / $newHeight;
      $widthRatio = $this->width / $newWidth;

      if ($heightRatio < $widthRatio) {
         $optimalRatio = $heightRatio;
      } else {
         $optimalRatio = $widthRatio;
      }

      $optimalHeight = $this->height / $optimalRatio;
      $optimalWidth = $this->width / $optimalRatio;

      return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
   }
   
   /**
    * Metoda pro rotaci obrázků, je tu protože né všechny php mají podporu pro rotate
    * @param <type> $srcImg
    * @param <type> $angle
    * @param <type> $bgcolor
    * @param <type> $ignore_transparent
    * @return <type>
    */
   private function imageRotateEquivalent($angle, $bgcolor, $ignore_transparent = 0) {
      function rotateX($x, $y, $theta) 
      {
         return $x * cos($theta) - $y * sin($theta);
      }
      function rotateY($x, $y, $theta) 
      {
         return $x * sin($theta) + $y * cos($theta);
      }

      $srcw = $this->width;
      $srch = $this->height;

      //Normalize angle
      $angle %= 360;
      //Set rotate to clockwise
      $angle = -$angle;

      if($angle == 0) {
         if ($ignore_transparent == 0) {
            imagesavealpha($this->imageData, true);
         }
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
               $color = imagecolorat($this->imageData, $srcX, $srcY );
            } else {
               $color = $bgcolor;
            }
            imagesetpixel($destimg, $x-$minX, $y-$minY, $color);
         }
      }
      $this->imageData = $destimg;
   }
}
?>