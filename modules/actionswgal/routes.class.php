<?php
class Actionswgal_Routes extends Actions_Routes {
   function initRoutes() {
      // editace fotek akce
      $this->addRoute('editphotos', "::urlkey::/editphotos", 'editphotos','{urlkey}/editphotos/');
      // uprava miniatury
      $this->addRoute('editphoto', "::urlkey::/editphotos/editphoto-::id::", 'editphoto','{urlkey}/editphotos/editphoto-{id}/');
      // upload a úprava fotek
      $this->addRoute('uploadFile', "::urlkey::/uploadFile.php", 'uploadFile','{urlkey}/uploadFile.php');
      $this->addRoute('checkFile', "::urlkey::/checkFile.php", 'checkFile','{urlkey}/checkFile.php');
      // list s fotkyma akce
      $this->addRoute('detailPhotos', "::urlkey::/fotky", 'showPhotos','{urlkey}/fotky/');

      parent::initRoutes();
   }
}

?>