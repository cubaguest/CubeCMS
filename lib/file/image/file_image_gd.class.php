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

   /**
    * Konstruktor třídy
    * @param file $file -- soubor
    */
   public function __construct(File $file)
   {
      parent::__construct($file);
   }
   
   public function crop($x, $y, $w, $h)
   {
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
      return $this;
   }
   
   public function rotate($degree = 180)
   {
      $this->loadImageData();
      
      return $this;
   }
   
   public function flip($axis = 'x')
   {
      $this->loadImageData();
      
      
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

   
   
   /*** PRIVATE METHODS  ***/
   
   /**
    * Načte data obrázku pro práci s ním
    */
   private function loadImageData()
   {
      if($this->imageData != null){
         return;
      }
      switch ($this->imageType) {
         case IMAGETYPE_GIF:
            $this->imageData = @imagecreatefromgif((string)$this->file);
            break;
         case IMAGETYPE_JPEG:
            $this->imageData = @imagecreatefromjpeg((string)$this->file);
            break;
         case IMAGETYPE_PNG:
            $this->imageData = @imagecreatefrompng((string)$this->file);
            break;
         case IMAGETYPE_WBMP:
            $this->imageData = @imagecreatefromwbmp((string)$this->file);
            break;
         case IMAGETYPE_JPEG2000:
            $this->imageData = @imagecreatefromjpeg((string)$this->file);
            break;
         default:
            throw new UnexpectedValueException( sprintf( $this->tr('Soubor % je neplatný typ obrázku'), (string)$this->file ) );
            break;
      };
      
      $this->width  = imagesx($this->imageData);  
      $this->height = imagesy($this->imageData);
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
}
?>