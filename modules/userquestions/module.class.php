<?php

class UserQuestions_Module extends Module {
   protected $version = '1.0.0';
   
   public function install()
   {
      parent::install();
      
      $m = new UserQuestions_Model();
      $m->createTable();
   }
}