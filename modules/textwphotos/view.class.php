<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class TextWPhotos_View extends Text_View {
   public function mainView() {
      parent::mainView();
      $pView = new Photogalery_View($this);
      $pView->addImagesToolbox();
   }
}