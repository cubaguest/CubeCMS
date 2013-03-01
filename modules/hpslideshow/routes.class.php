<?php
class HPSlideShow_Routes extends Routes {
   public $itemKey = null;

   function initRoutes() {
      $this->addRoute('editImage', "edit-image.php", 'editImage', 'edit-image.php', "XHR_Respond_VVEAPI");
	}
}
