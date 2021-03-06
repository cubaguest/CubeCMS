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
    * Soubor s obrázkem
    * @var string
    */
   private $imageFile = null;

    protected function init() {
      $this->htmlElement = new Html_Element('input');
      $this->html()->setAttrib('type', 'image');
   }
   
   /**
    * Metoda naplní prvek
    */
   public function populate() {
      if(isset ($_REQUEST[$this->getName()])){
         $this->values = array('x' => 0, 'y' => 0);
      } else if(isset ($_REQUEST[$this->getName().'_x']) AND isset ($_REQUEST[$this->getName().'_y'])) {
         $this->values = array('x' => $_REQUEST[$this->getName().'_x'],
                         'y' => $_REQUEST[$this->getName().'_y']);
      }
      $this->unfilteredValues = $this->values;
      $this->isPopulated = true;
   }
   
   /**
    * Metoda vrací jestli byl element vůbec odeslán
    * @return bool
    */
   public function isSend() {
      if(isset ($_REQUEST[$this->getName()]) OR
          (isset ($_REQUEST[$this->getName().'_x']) AND isset ($_REQUEST[$this->getName().'_y']))) {
         return true; 
      }
      return false;
   }

   /**
    * Metoda vrací souřadnice potvrzení
    * @param char $axis -- 'x' nebo 'y'
    * @return int -- souřadnice
    */
   //public function getSubmitPosition($axis = 'x') {
   //   if($axis == 'x'){
   //      return $this->submitX;
   //   } else {
   //      return $this->submitY;
   //   }
   //}

   /**
    * Metoda nasatví obrázek elementu
    * @param string $image -- obrázek i s cestou
    */
   public function setImage($image) {
      if(strpos($image, '/') === false){
         $image = Url_Request::getBaseWebDir(true).'images/icons/'.$image;
      }
      $this->imageFile = $image;
   }

   /**
    * Metoda vrací prvek (html element podle typu elementu - input, textarea, ...)
    * @return string
    * @todo upravit podle submitu    
    */
   public function control($renderKey = null) {
      $this->html()->setAttrib('name', $this->getName());
      $this->html()->setAttrib('type', 'image');
      $this->html()->setAttrib('value', $this->getLabel());
      $this->html()->setAttrib('src', $this->imageFile);
      return $this->html();
   }
}
