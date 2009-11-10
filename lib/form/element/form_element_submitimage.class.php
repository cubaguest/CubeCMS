<?php
/**
 * Třída pro obsluhu INPUT prvku typu IMAGE
 * Třída implementující objekt pro obsluhu INPUT prvkuu typu IMAGE. Umožňuje kontrolu
 * správnosti zadání,kódování/dekódování obsahu prvku, jazykovou obsluhu a jeho
 * vykreslení i s popisem v šabloně. Při špatném zadání se stará o zvýraznění
 * chyby.
 *
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE 5.1.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro obsluhu formulářového prvku typu Input-Text
 */
class Form_Element_SubmitImage extends Form_Element_Submit implements Form_Element_Interface {
/**
 * jestli je element potvrzen
 * @var boolean
 */
//   private $isSubmited = false;

   /**
    * Souřadnice x potvrzení
    * @var int
    */
   private $submitX = 0;

   /**
    * Souřadnice y potvrzení
    * @var int
    */
   private $submitY = 0;

   /**
    * Soubor s obrázkem
    * @var string
    */
   private $imageFile = null;

   /**
    * Metoda naplní prvek
    */
   public function populate($method = 'post') {
      switch ($method) {
         case 'get':
            if(isset ($_GET[$this->getName()]) OR (isset ($_GET[$this->getName().'_x']) AND isset ($_GET[$this->getName().'_y']))) {
               $this->isSubmited = true;
               $this->submitX = $_GET[$this->getName().'_x'];
               $this->submitY = $_GET[$this->getName().'_y'];
            }
            break;
         default:
            if(isset ($_POST[$this->getName()]) OR (isset ($_POST[$this->getName().'_x']) AND isset ($_POST[$this->getName().'_y']))) {
               $this->isSubmited = true;
               $this->submitX = $_POST[$this->getName().'_x'];
               $this->submitY = $_POST[$this->getName().'_y'];
            }
            break;
      }
      $this->isPopulated = true;
   }

   /**
    * Metoda vrací souřadnice potvrzení
    * @param char $axis -- 'x' nebo 'y'
    * @return int -- souřadnice
    */
   public function getSubmitPosition($axis = 'x') {
      if($axis == 'x'){
         return $this->submitX;
      } else {
         return $this->submitY;
      }
   }

   /**
    * Metoda nasatví obrázek elementu
    * @param string $image -- obrázek i s cestou
    */
   public function setImage($image) {
      $this->imageFile = $image;
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    */
   public function controll() {
      $this->html()->setAttrib('name', $this->getName());
      $this->html()->setAttrib('type', 'image');
      $this->html()->setAttrib('value', $this->getLabel());
      $this->html()->setAttrib('src', $this->imageFile);
      return $this->html();
   }
}
?>
