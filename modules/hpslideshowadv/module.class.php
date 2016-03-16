<?php

class HPSlideShowAdv_Module extends Module_Admin {
   protected $version = '1.0.0';
   
   public function install()
   {
      $m = new HPSlideShowAdv_Model();
      $m->createTable();
      $m = new HPSlideShowAdv_Model_Items();
      $m->createTable();
      
      parent::install();
   }
}