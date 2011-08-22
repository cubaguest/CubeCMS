<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class ShopSettings_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://main.phtml');
      Template_Module::setEdit(true);
   }
   
   public function currencyAndTaxesView() {
      $this->template()->addFile('tpl://currency_taxes.phtml');
      Template_Module::setEdit(true);
   }
   
   public function taxesListView(){
      echo json_encode($this->respond);
   }
   
   public function shipAndPayView() {
      $this->template()->addFile('tpl://ship_pay.phtml');
      Template_Module::setEdit(true);
   }
   
   public function paymentsListView(){
      echo json_encode($this->respond);
   }
   
   public function shippingsListView(){
      echo json_encode($this->respond);
   }
   
   public function ordersView()
   {
      $this->template()->addFile('tpl://orders.phtml');
   }
   
   private function addTinyMCE() {
      $type = $this->category()->getParam(Text_Controller::PARAM_EDITOR_TYPE, 'advanced');
      if($type == 'none') return;
      $this->form->text->html()->addClass("mceEditor");
      $this->tinyMCE = new Component_TinyMCE();
      switch ($type) {
         case 'simple':
            $settings = new Component_TinyMCE_Settings_AdvSimple();
            $settings->setSetting('editor_selector', 'mceEditor');
            break;
         case 'full':
            // TinyMCE
            $settings = new Component_TinyMCE_Settings_Full();
            break;
         case 'advanced':
         default:
            $settings = new Component_TinyMCE_Settings_Advanced();
            break;
      }
      $settings->setSetting('height', '600');
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
   }
}

?>