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

class File_Image_Base extends TrObject {
   
   const RESIZE_AUTO       = 1;
   const RESIZE_EXACT      = 2;
   const RESIZE_PORTRAIT   = 3;
   const RESIZE_LANDSCAPE  = 4;
   const RESIZE_CROP       = 5;


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
   protected $quality = 90;

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


   /**
    * Konstruktor třídy
    * @param file $file -- soubor
    */
   public function __construct(File $file)
   {
      $this->file = $file;
      $this->detectimageType();
   }
   
   public function crop($x, $y, $w, $h)
   {
      return $this;
   }
   
   public function resize($w, $h, $crop = false)
   {
      return $this;
   }
   
   public function rotate($degree = 180)
   {
      return $this;
   }
   
   public function flip($axis = 'x')
   {
      return $this;
   }
   
   public function setQuality($quality = 90)
   {
      $this->quality = $quality;
      return $this;
   }
   
   /**
    * Uložení obrázku do jiného formátu (automaticky upraví příponu) nebo ho vypíše
    * @param string/File $file -- objek souboru nebo název obrázku
    * @param const $format -- formát IMAGETYPE_XXX
    */
   public function write($file = null, $format = null)
   {
      
      
      return null;
   }

   /**
    * Detekce typu obrázku konstanty IMAGE_XXX
    */
   private function detectimageType()
   {
      if($this->file->exist()) {
         $imageProperty = getimagesize((string)$this->file);
         if($imageProperty == false){
            throw new File_Image_Exception($this->tr('Zadaný soubor není platný obrázek'));
         }
         $this->imageType = $imageProperty[2];
      } else if( ($type = array_search($this->file->getExtension(), $this->imgTypesExtensions)) !== false) {
         $this->imageType = $type;
      } else {
         throw new File_Image_Exception($this->tr('Zadaný soubor není platný obrázek'));
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
}
?>