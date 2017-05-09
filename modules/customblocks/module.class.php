<?php

class CustomBlocks_Module extends Module {
   protected $version = '1.1.2';
   
   public function install()
   {
      $m = new CustomBlocks_Model_Blocks();
      $m->createTable();
      $m = new CustomBlocks_Model_Embeds();
      $m->createTable();
      $m = new CustomBlocks_Model_Texts();
      $m->createTable();
      $m = new CustomBlocks_Model_Images();
      $m->createTable();
      $m = new CustomBlocks_Model_Videos();
      $m->createTable();
      $m = new CustomBlocks_Model_Files();
      $m->createTable();
      
      parent::install();
   }
}