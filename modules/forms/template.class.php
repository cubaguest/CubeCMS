<?php
class Forms_Template extends Template_Module {
   public function __construct() 
   {
      parent::__construct(new Url_Link_Module(), Category::getSelectedCategory());
      $this->addFile('tpl://forms:snipshet.phtml');
   }
}