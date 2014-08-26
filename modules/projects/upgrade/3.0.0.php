<?php


$sections = Projects_Model_Sections::getAllRecords();
$model = new Projects_Model_Projects();

foreach ($sections as $section) {
   $projects = $model
       ->where(Projects_Model_Projects::COLUMN_ID_SECTION." = :idsec", array('idsec' => $section->getPK()))
       ->order(array(Projects_Model_Projects::COLUMN_WEIGHT => Model_ORM::ORDER_DESC, Projects_Model_Projects::COLUMN_TIME_ADD => Model_ORM::ORDER_DESC))
       ->records();
   
   $i = 1;
   foreach ($projects as $project) {
      /* @var $project Model_ORM_Ordered_Record */
      $project->setRecordPosition($i);
      $i++;
   }
}
