<?php
class MailsAddressBook_Install extends Install_Module {
   public $version = array('major' => 1, 'minor' => 0);
   
   public function install() {
      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }
}

?>
