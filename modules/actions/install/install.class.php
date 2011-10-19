<?php
class Actions_Install extends Module_Install {
   
   public function install() {
      $this->runSQLCommand($this->replaceDBPrefix($this->getSQLFileContent('install.sql')));
   }
   
   protected function moduleUpdate($toVersion)
   {
      switch ($toVersion) {
         case 1.2:
            // přesun titulních obrázků do složky pro titulní obrázky

            break;
         default:
            break;
      }
   }
}

?>
