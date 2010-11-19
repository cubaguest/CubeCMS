<?php
/**
 * Třída pro vytvoření požadavku pro naplnění jqgrid
 */
class Component_TinyMCE_Settings_Simple extends Component_TinyMCE_Settings {

   public function  __construct() {
      parent::__construct();
      $this->settingName = 'simple';
   }
}
?>
