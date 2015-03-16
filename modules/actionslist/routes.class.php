<?php
class ActionsList_Routes extends Actions_Routes {
   function initRoutes() {
      parent::initRoutes();
      // seznam kategorií pro přejití
      $this->addRoute('listCatAdd', "listcat.phtml", 'listCatAdd', 'listcat.phtml');
   }
}