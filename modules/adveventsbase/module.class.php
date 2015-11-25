<?php

class AdvEventsBase_Module extends Module {
   protected $version = '1.0.0';
   
   public function install()
   {
      parent::install();
      
      // install models
      $m = new AdvEventsBase_Model_Events();
      $m->createTable();
      
      $m = new AdvEventsBase_Model_EventsImages();
      $m->createTable();
      
      $m = new AdvEventsBase_Model_EventsSources();
      $m->createTable();
      
      $m = new AdvEventsBase_Model_EventsTimes();
      $m->createTable();
      
      $m = new AdvEventsBase_Model_Locations();
      $m->createTable();
      
      $m = new AdvEventsBase_Model_Organizers();
      $m->createTable();
      
      $m = new AdvEventsBase_Model_UserAdds();
      $m->createTable();
      
      $m = new AdvEventsBase_Model_Places();
      $m->createTable();
      
      $m = new AdvEventsBase_Model_Categories();
      $m->createTable();
      
   }
}