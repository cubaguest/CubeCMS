<?php
class Mails_Install extends Module_Install {
   public $version = array('major' => 4, 'minor' => 0);
   //protected $depModules = array('newsletter');
   public function install() {
      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }
}

?>
