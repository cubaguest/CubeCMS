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
      foreach ($modules as $name => $version) {
         // kontrola aktuálnosti
         $mClass = ucfirst($name) . '_Module';
         if(class_exists($mClass)){
            $inst = new $mClass($name);
         }
         array_push($mList, array('name' => $name, 'version' => $version));

      }

      $this->view()->modules = $mList;
   }
}
