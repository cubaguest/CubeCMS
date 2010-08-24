<?php
class Mails_Install extends Module_Install {
   //protected $depModules = array('newsletter');
   public function install() {
      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }
}

?>
