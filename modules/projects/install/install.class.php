<?php
class Projects_Install extends Install_Module {
   protected $depModules = array('photogalery');
   public function install() {
      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }
}

?>
