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
   
   public function zonesListView(){
      echo json_encode($this->respond);
   }
   
   public function ordersView()
   {
      $this->template()->addFile('tpl://orders.phtml');
      Template_Module::setEdit(true);

      // assign tinymce editor
      $themeAdvMail = new Component_TinyMCE_Settings_Mail();
      $themeAdvMail->setSetting('height', '300');
      $themeAdvMail->setVariablesURL($this->link()->route('mailVariables')->param('type', 'userMail'));

      $this->setTinyMCE($this->form->notifyUserMail, $themeAdvMail);

      $themeAdvMail->setVariablesURL($this->link()->route('mailVariables')->param('type', 'orderStatus'));
      $this->setTinyMCE($this->form->userOrderStatusMail, $themeAdvMail);

      $themeAdvMail->setVariablesURL($this->link()->route('mailVariables')->param('type', 'adminMail'));
      $this->setTinyMCE($this->form->notifyAdminMail, $themeAdvMail);

   }
   
   public function orderStatesView()
   {
      $this->template()->addFile('tpl://states.phtml');
      Template_Module::setEdit(true);

      // toolbox na úpravu stavu
      $toolbox = new Template_Toolbox2();
      $toolbox->setTemplate(Template_Toolbox2::TEMPLATE_INLINE);

      $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit', $this->tr('Upravit stav'));
      $toolEdit->setIcon(Template_Toolbox2::ICON_PEN);
      $toolbox->addTool($toolEdit);

      $toolRemove = new Template_Toolbox2_Tool_Form($this->formDeleteOrderState);
      $toolRemove->setIcon(Template_Toolbox2::ICON_DELETE);
      $toolRemove->setImportant(true);
      $toolRemove->setConfirmMeassage($this->tr('Opravdu smazat stav?'));
      $toolbox->addTool($toolRemove);

      foreach ($this->orderStates as $state) {
         $toolbox->edit->setAction($this->link()->clear()->route('editOrderState', array('id' => $state->getPK())));
         $toolbox->orderstatedel->getForm()->id->setValues($state->getPK());
         $state->toolbox = clone $toolbox;
      }
   }
   
   public function addOrderStateView()
   {
      $this->template()->addFile('tpl://state_edit.phtml');
      Template_Module::setEdit(true);
   }
   
   public function editOrderStateView()
   {
      $this->template()->addFile('tpl://state_edit.phtml');
      Template_Module::setEdit(true);
   }
   
   public function customersView()
   {
      
   }
}
