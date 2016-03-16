<?php
class HPSlideShowAdv_Routes extends Routes {
   public $itemKey = null;

   function initRoutes() {
      $this->addRoute('addSlide');
      $this->addRoute('editSlideParams', "slide-edit.php", 'editSlideParams','slide-edit.php', "XHR_Respond_VVEAPI");
      $this->addRoute('editSlide', "slide-::id::/edit/", 'editSlide','slide-{id}/edit/');
      $this->addRoute('editItem', "slide-::id::/edit-item-::idItem::.php", 'editItem', 'slide-{id}/edit-item-{idItem}.php', "XHR_Respond_VVEAPI");
      $this->addRoute('uploadSlideItem', "slide-::id::/upload.php", 'uploadSlideItem', 'slide-{id}/upload.php', "XHR_Respond_VVEAPI");
	}
}
