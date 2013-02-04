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
   
   public function customersView()
   {
      
   }
}

?>