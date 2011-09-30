<?php
class Photogalery_Routes extends Routes {
   public $itemKey = null;

   function initRoutes() {
      $reg = $replace = null;
      if($this->itemKey != null){
         $reg = '::'.$this->itemKey.'::/';
         $replace = '{'.$this->itemKey.'}/';
      }
      
      $this->addRoute('editphoto', $reg."editphotos/editphoto-::id::", 'editphoto', $replace."editphotos/editphoto-{id}/");
      
      $this->addRoute('editphotos', $reg."editphotos/", 'editphotos', $replace."editphotos/");
      $this->addRoute('sortphotos', $reg."sortphotos/", 'sortphotos', $replace."sortphotos/");
      $this->addRoute('deletephoto', "deletephoto.php", 'deletephoto', "deletephoto.php", 'XHR_Respond_VVEAPI');

      $this->addRoute('uploadFile', $reg."uploadFile.php", 'uploadFile', $replace.'uploadFile.php', "XHR_Respond_VVEAPI");
      $this->addRoute('checkFile', $reg."checkFile.php", 'checkFile', $replace.'checkFile.php', "XHR_Respond_VVEAPI");

      // tohle odstranit!!!
      $this->addRoute('edittext', "edittext", 'edittext','edittext/');
	}
}

?>