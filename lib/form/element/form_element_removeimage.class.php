<?php
/**
 * Třída pro přidání prvku s aktuálním obrázkem do formuláře, popřípadě jeho odstranění
 * Třída implementující objekt pro odstranění uloženého obrázku. Zobrazí uložený obrázek
 * a přidá chcekbox pro jeho smazání
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 6.0.5 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_RemoveImage extends Form_Element_Checkbox implements Form_Element_Interface {
   private $imagePath;

   const IMAGE_W = 50;
   const IMAGE_H = 50;

   public function setImgPath($path){
      $this->imagePath = $path;
   }

   public function subLabel($renderKey = null) {
      return "tady bude obr";
   }
}
?>
