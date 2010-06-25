<?php
class Articles_Install extends Module_Install {
   
   public function install() {
      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }

   public function  update($fromVersion, $toVersion) {
      switch ($fromVersion) {
         case '1.0':
            $this->runSQLCommand($this->getSQLFileContent('upgrade_1.0_1.1.sql'));
            parent::update($fromVersion, $toVersion);
            break;
      }
   }
}

?>
