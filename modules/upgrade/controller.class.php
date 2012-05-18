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
         $vers = file_get_contents(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
                 .$module->{Model_Module::COLUMN_NAME}.DIRECTORY_SEPARATOR
                 .AppCore::DOCS_DIR.DIRECTORY_SEPARATOR.self::VERSION_FILE);
         $m['name'] = $module->{Model_Module::COLUMN_NAME};
         $m['inst_ver'] = $module->{Model_Module::COLUMN_VERSION_MAJOR}.'.'.$module->{Model_Module::COLUMN_VERSION_MINOR};
         $m['avail_ver'] = $vers;
         if((float)$m['inst_ver'] >= (float)$m['avail_ver']){
            $m['upgrade'] = false;
         } else {
            $isUpgradeAvailable = true;
            $m['upgrade'] = true;
         }
         array_push($mList, $m);
      }

      if($isUpgradeAvailable === true) {
         $this->infoMsg()->addMessage($this->tr('Je k dispozici nová verze modulu. Doporučujeme provést povýšení na novou verzi, jinak systém nemusí pracovat správně'), false);
         $this->view()->allowUpgrade = true;
      }

      $upgradeForm = new Form('module_upgrade_', true);
      $elemSubimt = new Form_Element_Submit('save', $this->tr('Povýšit'));
      $upgradeForm->addElement($elemSubimt);

      if($upgradeForm->isValid()){
         foreach ($mList as $module) {
            if($module['upgrade'] == true){
               $instObjName = ucfirst($module['name']).'_Install';
               $instObj = new $instObjName;
               $instObj->update();
            }
         }
         $this->infoMsg()->addMessage($this->tr('Povýšení proběhlo úspěšně'));
         $this->link()->reload();
      }

      $this->view()->formUpgrade = $upgradeForm;

      $this->view()->modules = $mList;
   }
}

?>