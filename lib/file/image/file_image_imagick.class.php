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
    * @var type 
    */
   protected $imageData = null;

   /**
    * Konstruktor třídy
    * @param string $filePath -- Cesta k obrázku
    */
   public function __construct($filePath)
   {
      $this->filePath = null;
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
   
   /**
    * Uložení obrázku do jiného formátu (automaticky upraví příponu)
    * @param const $format -- formát IMAGETYPE_XXX
    */
   public function saveAs($format = IMAGETYPE_JPEG)
   {
      return $this;
   }

   /**
    * Načte data obrázku pro práci s ním
    */
   private function loadImageData()
   {
      
   }
}
      ?>