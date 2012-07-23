<?php
class PressReports_Install extends Install_Module {
   public function install() {
       $model = new PressReports_Model();
       $model->createTable();
   }
}

?>
