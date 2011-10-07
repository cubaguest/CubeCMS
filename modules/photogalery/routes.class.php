<?php
class Photogalery_Routes extends Routes {
   public $itemKey = null;

   function initRoutes() {
      $reg = $replace = null;
      if($this->itemKey != null){
         if(is_string($this->itemKey)){
            $reg = '::'.$this->itemKey.'::/';
            $replace = '{'.$this->itemKey.'}/';
         } else if(is_array($this->itemKey)){
            foreach ($this->itemKey as $key) {
               $reg .= '::'.$key.'::/';
               $replace .= '{'.$key.'}/';
            }
         }
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