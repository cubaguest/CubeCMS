<?php

class FAQ_Module extends Module {
   protected $version = '2.0.0';
   
   public function install()
   {
      parent::install();
      
      $m = new FAQ_Model();
      $m->createTable();
   }
}