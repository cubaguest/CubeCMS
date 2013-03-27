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

abstract class File_Image_Base extends TrObject {
   
   const RESIZE_AUTO       = 1;
   const RESIZE_EXACT      = 2;
   const RESIZE_PORTRAIT   = 3;
   const RESIZE_LANDSCAPE  = 4;
   const RESIZE_CROP       = 5;
   
   const FLIP_HORIZONTAL   = 1;
   const FLIP_VERTICAL     = 2;


   /**
    * Porměné obsahuje šířku původního obrázku
    * @var int
    */
   protected $width = 0;

   /**
    * Porměné obsahuje výšku původního obrázku
    * @var int
    */
   protected $height = 0;

   /**
    * Porměné obsahuje typ původního obrázku
    * @var int (constant 'IMAGETYPE_XXX')
    */
   protected $imageType = null;

   /**
    * proměná s nastavenou kvalitou pro výstup JPEG
    * @var int
    */
   protected $quality = VVE_IMAGE_COMPRESS_QUALITY;

   /**
    *
    * @var File
    */
   protected $file = null;

   private $imgTypesExtensions = array(
      IMAGETYPE_GIF         => 'gif',        ###  1 = GIF
      IMAGETYPE_JPEG        => 'jpg',        ###  2 = JPG
      IMAGETYPE_JPEG        => 'jpeg',        ###  2 = JPG
      IMAGETYPE_PNG         => 'png',        ###  3 = PNG
      IMAGETYPE_SWF         => 'swf',        ###  4 = SWF
      IMAGETYPE_PSD         => 'psd',        ###  5 = PSD
      IMAGETYPE_BMP         => 'bmp',        ###  6 = BMP   
      IMAGETYPE_TIFF_II     => 'tiff',        ###  7 = TIFF     (intel byte order)
      IMAGETYPE_TIFF_MM     => 'tiff',        ###  8 = TIFF     (motorola byte order)
      IMAGETYPE_JPC         => 'jpc',        ###  9 = JPC
      IMAGETYPE_JP2         => 'jp2',        ### 10 = JP2
      IMAGETYPE_JPX         => 'jpf',        ### 11 = JPX     Yes! jpf extension is correct for JPX image type
      IMAGETYPE_JB2         => 'jb2',        ### 12 = JB2
      IMAGETYPE_SWC         => 'swc',        ### 13 = SWC
      IMAGETYPE_IFF         => 'aiff',        ### 14 = IFF
      IMAGETYPE_WBMP        => 'wbmp',        ### 15 = WBMP
      IMAGETYPE_XBM         => 'xbm'        ### 16 = XBM
   );

   protected $baseWaterMarkParams = array(
         'valign' => 'bottom',
         'halign' => 'right',
         'xoffset' => 5,
         'yoffset' => 5,
         'opacity' => 50,
      );

   /**
    * Data s obrázkem
    */
   protected $imageData = null;
   
   /**
    * Konstruktor třídy
    * @param File $file -- soubor
    */
   public function __construct(File $file)
   {
      $this->file = $file;
      $this->detectimageType();
   }
   
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
      return $this;
   }
   
   /**
    * Metoda pro změnu velikosti obrázku
    * @param int $w -- Maximální šířka
    * @param int $h -- Maximální Výška
    * @param type $option -- Konstanta třídy RESIZE_XXX
    * @return File_Image_Base 
    */
   public function resize($w, $h, $option = self::RESIZE_AUTO, $resizeUp = false)
   {
      return $this;
   }
   
   /**
    * Metoda pro rotaci obrázku
    * @param int $degree -- Stupňů otočení
    * @return File_Image_Base 
    */
   public function rotate($degree = 180)
   {
      return $this;
   }
   
   /**
    * Metoda pro obrácení obrázku
    * @param int $type -- konstanta třídy jak se má obrázek obrátit FLIP_XXX
    * @return File_Image_Base 
    */
   public function flip($type = self::FLIP_HORIZONTAL)
   {
      return $this;
   }
   
   /**
    * Metoda nasatvuje kvalitu výstupního obrázku
    * @param int $quality -- Kvalita 0 - 100
    * @return File_Image_Base 
    */
   public function setQuality($quality = 90)
   {
      $this->quality = $quality;
      return $this;
   }
   
   /**
    * Metoda vrací šířku obrázku
    * @return int
    */
   public function getWidth()
   {
      return $this->width;
   }
   
   /**
    * Metoda vrací výšku obrázku
    * @return type 
    */
   public function getHeight()
   {
      return $this->height;
   }


   /**
    * Metoda provede filtraci daného obrázku
    * @param int $filter -- konstanta IMG_FILTER_XXX
    * @return File_Image_Base 
    */
   public function filter($filter, $arg1 = null, $arg2 = null, $arg3 = null)
   {
      return $this;
   }
   
   /**
    * Metoda přidá ochranný obrázek
    * @param File_Image $img -- obrázek
    * @return File_Image_Base 
    */
   public function watermark(File_Image $img, $params = array())
   {
      
      return $this;
   }
   
   /**
    * Metoda pro uložení obrázku (automaticky upraví příponu) nebo ho vypíše
    * @param string/File $file -- objek souboru nebo název obrázku
    * @param const $format -- formát IMAGETYPE_XXX
    */
   public function write($file = null, $format = null)
   {
      return null;
   }
   
   /**
    * Metoda pro uložení obrázku
    */
   public function save()
   {
      return $this->write((string)$this->file);
   }
   
   /**
    * Detekce typu obrázku konstanty IMAGE_XXX
    */
   private function detectimageType()
   {
      if($this->file->exist()) {
         $imageProperty = getimagesize((string)$this->file);
         if($imageProperty == false){
            throw new File_Image_Exception(sprintf($this->tr('Zadaný soubor %s není platný obrázek'), $this->file->getPath().$this->file->getName()));
         }
         $this->imageType = $imageProperty[2];
      } else if( ($type = array_search($this->file->getExtension(), $this->imgTypesExtensions)) !== false) {
         $this->imageType = $type;
      } else {
         throw new File_Image_Exception(sprintf($this->tr('Zadaný soubor %s není platný obrázek'), $this->file->getPath().$this->file->getName()));
      }
   }
   
   /**
    * Interní metoda pro nastavení objektu souboru
    * !!! NOT USE IN MODULES !!!
    * @ignore
    * @uses File_Image
    */
   public function _setFileObj(File $file)
   {
      $this->file = $file;
   }
   
   /**
    * Vrací interní objekt obrázku podle použité knihovny. Může vracet: gd objekt, imagick
    * @return mixed
    */
   public function getImageDataObj()
   {
      return $this->imageData;
   }
}
?>