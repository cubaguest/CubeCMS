<?php
class Contact_Install extends Module_Install {
   
   public function install() {
   }

   public function  update($fromVersion, $toVersion) {
      switch ($fromVersion) {
         case '1.0':
            $this->runSQLCommand($this->getSQLFileContent('install.sql'));
            parent::update($fromVersion, $toVersion);
            break;
      }
   }
}

?>
