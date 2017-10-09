<?php

class Courses_Module extends Module {
   protected $version = '2.0.0';
   protected $depModules = array('mails');
   
   public function install()
   {
      parent::install();
      
      $m = new Courses_Model();
      $m->createTable();
      
      $m = new Courses_Model_Lecturers();
      $m->createTable();
      
      $m = new Courses_Model_PrivateUsers();
      $m->createTable();
      
      $m = new Courses_Model_Registrations();
      $m->createTable();
      
      $m = new Courses_Model_Files();
      $m->createTable();
      
      $m = new Courses_Model_Places();
      $m->createTable();
      
   }
}