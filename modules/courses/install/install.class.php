<?php
class Courses_Install extends Module_Install {
   protected $depModules = array('lecturers');
   public function install() {
      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }
}

?>
