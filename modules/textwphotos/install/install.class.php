<?php
class TextWPhotos_Install extends Module_Install {
   protected $depModules = array('text', 'photogalery');
//   public function install() {
//      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
//   }
}

?>
