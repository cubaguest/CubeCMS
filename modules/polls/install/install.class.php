<?php
class Polls_Install extends Module_Install {
   public function install() {
      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }
}

?>
