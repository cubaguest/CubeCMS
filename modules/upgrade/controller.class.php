<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Upgrade_Controller extends Controller {
   const VERSION_FILE = 'version.txt';

   public function mainController() {
      $this->checkControllRights();
      /* Upgrade modulů */
      $isUpgradeAvailable = false;

      // načtení instalovaných modulů
      $model = new Model_Module();

      $modules = $model->getInstalledModules();

      $mList = array();
      foreach ($modules as $module) {
         $m['name'] = $module->{Model_Module::COLUMN_NAME};
         $m['version'] = $module->{Model_Module::COLUMN_VERSION};

         // kontrola aktuálnosti
         $mClass = ucfirst($module->{Model_Module::COLUMN_NAME}) . '_Module';
         if(!class_exists($mClass)){
            $mClass = 'Module';
         }
         $inst = new $mClass($module->{Model_Module::COLUMN_NAME});

         array_push($mList, $m);
      }

      $this->view()->modules = $mList;
   }
}
