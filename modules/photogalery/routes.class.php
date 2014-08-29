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

      // ajax actions
      $this->addRoute('imageUpload', $reg."image-upload.php", 'imageUpload', $replace.'image-upload.php');
      $this->addRoute('imageUploadAjax', $reg."image-upload-ajax.php", 'imageUploadAjax', $replace.'image-upload-ajax.php', "XHR_Respond_VVEAPI");
      
      $this->addRoute('imageDelete', $reg."delete.php", 'imageDelete', $replace.'delete.php', "XHR_Respond_VVEAPI");
      $this->addRoute('imageRotate', $reg."rotate.php", 'imageRotate', $replace.'rotate.php', "XHR_Respond_VVEAPI");
      $this->addRoute('imageMove', $reg."move.php", 'imageMove', $replace.'move.php', "XHR_Respond_VVEAPI");
      $this->addRoute('imageEditLabels', $reg."edit-labels.php", 'imageEditLabels', $replace.'edit-labels.php', "XHR_Respond_VVEAPI");
      // zatím neimplementována
      $this->addRoute('getImages', $reg."images.json", 'getImages', $replace.'images.json', "XHR_Respond_VVEAPI");
      // deprecated
      $this->addRoute('imageEdit', $reg."image-edit.php", 'imageEdit', $replace.'image-edit.php', "XHR_Respond_VVEAPI");
      $this->addRoute('cropThumb', $reg."cropThumb.php", 'cropThumb', $replace.'cropThumb.php', "XHR_Respond_VVEAPI");
      $this->addRoute('checkFile', $reg."checkFile.php", 'checkFile', $replace.'checkFile.php', "XHR_Respond_VVEAPI");
      
      // tohle odstranit!!!
      $this->addRoute('edittext', "edittext", 'edittext','edittext/');
	}
}