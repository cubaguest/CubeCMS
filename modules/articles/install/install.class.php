<?php
class Articles_Install extends Module_Install {
   protected $depModules = array('text');
   public function install() {
      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }
}

?>
