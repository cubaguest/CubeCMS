<?php
class Mails_Install extends Module_Install {
   //protected $depModules = array('newsletter');
   public function install() {
      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }

   public function  update($fromVersion, $toVersion) {
      switch ($fromVersion) {
         case '1.0':
            $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('upgrade_1.0_1.1.sql')));
            parent::update($fromVersion, $toVersion);
            break;
      }
   }
}

?>
