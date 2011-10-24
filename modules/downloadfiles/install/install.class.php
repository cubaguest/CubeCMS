<?php
class DownloadFiles_Install extends Install_Module {
   public function install() {
       $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }
}

?>
