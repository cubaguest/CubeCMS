<?php
class Projects_Install extends Module_Install {
   protected $depModules = array('photogalery');
   public function install() {
      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }
}

?>
