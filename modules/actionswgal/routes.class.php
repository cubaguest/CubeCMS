<?php
class Actionswgal_Routes extends Actions_Routes {
   function initRoutes() {
      // editace fotek akce
      $this->registerModule('photogalery', array('itemKey' => 'urlkey'));
      // list s fotkyma akce
      $this->addRoute('detailPhotos', "::urlkey::/fotky", 'showPhotos','{urlkey}/fotky/');

      parent::initRoutes();
   }
}

?>