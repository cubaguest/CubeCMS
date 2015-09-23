<?php

class EmbeddedVideos_Module extends Module {
   protected $version = '1.0.0';
   
   public function install()
   {
      parent::install();
      
      $m = new EmbeddedVideos_Model();
      $m->createTable();
   }
}