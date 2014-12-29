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

class File_Image_Imagick extends File_Image_Base {
   /**
    * Data s obrázkem
    * @var Imagick 
    */
   protected $imageData = null;

   /**
    * Metoda provede ořez obrázku
    * @param int $x -- Pozice X ořezu
    * @param int $y -- Pozice Y ořezu
    * @param int $w -- Šířka ořezu
    * @param int $h -- Výška ořezu
    * @return File_Image_Base 
    */
   public function crop($x, $y, $w, $h)
   {
      $this->loadImageData();
      $this->imageData->cropimage($w, $h, $x, $y);
      return $this;
   }
   
   /**
    * Metoda pro změnu velikosti obrázku
    * @param int $w -- Maximální šířka
    * @param int $h -- Maximální Výška
    * @param type $option -- Konstanta třídy RESIZE_XXX
    * @return File_Image_Base 
    */
   public function resize($w, $h, $option = self::RESIZE_AUTO, $resizeUp = false, $interlace = false)
   {
      $this->loadImageData();
      return $this;
   }
   
   /**
    * Metoda pro rotaci obrázku
    * @param int $degree -- Stupňů otočení
    * @return File_Image_Base 
    */
   public function rotate($degree = 180)
   {
      $this->loadImageData();
      $this->imageData->rotateImage(new ImagickPixel('none'), $degree); 
      return $this;
   }
   
   /**
    * Metoda pro obrácení obrázku
    * @param int $type -- konstanta třídy jak se má obrázek obrátit FLIP_XXX
    * @return File_Image_Base 
    */
   public function flip($type = self::FLIP_HORIZONTAL)
   {
      $this->loadImageData();
      $type == self::FLIP_HORIZONTAL ? $this->imageData->flipimage() : $this->imageData->flopimage(); 
      return $this;
   }
   
   /**
    * Metoda provede filtraci daného obrázku
    * @param int $filter -- konstanta IMG_FILTER_XXX
    * @return File_Image_Base 
    */
   public function filter($filter, $arg1 = null, $arg2 = null, $arg3 = null)
   {
      $this->loadImageData();
      return $this;
   }
   
   /**
    * Metoda přidá ochranný obrázek
    * @param File_Image $img -- obrázek
    * @return File_Image_Base 
    */
   public function watermark(File_Image $img, $params = array())
   {
      $this->loadImageData();
      $params = array_merge($this->baseWaterMarkParams, $params);
      
      $watermark = new Imagick();
      $watermark->readImage((string)$img);
      
      /*
       * PARAMS
       * 'valign' => 'bottom',
       * 'halign' => 'right',
       * 'xoffset' => 5,
       * 'yoffset' => 5,
       * 'opacity' => 50,
       */
      
      // how big are the images?
      $wWidth = $watermark->getImageWidth();
      $wHeight = $watermark->getImageHeight();
      
      if ($this->getHeight() < $wHeight || $this->getWidth() < $wWidth) {
         // resize the watermark
         $watermark->scaleImage($this->getWidth()/3, 0); // 1/3 of original image
         // get new size
         $wWidth = $watermark->getImageWidth();
         $wHeight = $watermark->getImageHeight();
      }
      
      // calculate the position
       $x = ($this->getWidth() - $wWidth) / 2;
       $y = ($this->getHeight() - $wHeight) / 2;
      
      $this->imageData->compositeImage($watermark, Imagick::COMPOSITE_OVER, $x, $y);
      
      return $this;
   }
   
   public function textWatermark($text, $params = array())
   {
      
   }
       
   
   /**
    * Metoda pro uložení obrázku (automaticky upraví příponu) nebo ho vypíše
    * @param string/File $file -- objek souboru nebo název obrázku
    * @param const $format -- formát IMAGETYPE_XXX
    */
   public function write($file = null, $format = null)
   {
      $this->loadImageData();
      return null;
   }
   
   /**
    * Metoda pro uložení obrázku
    */
   public function save()
   {
      $this->loadImageData();
      return $this->write((string)$this->file);
   }
   
   /**
    * Načte data obrázku pro práci s ním
    */
   private function loadImageData()
   {
      if($this->imageData != null){
         return;
      }
      $this->imageData = new Imagick( (string)$this->file );
      $this->width  = $this->imageData->getimagewidth();
      $this->height = $this->imageData->getimageheight();
   }
}