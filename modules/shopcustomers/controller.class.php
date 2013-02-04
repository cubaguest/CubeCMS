<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class ShopCustomers_Controller extends Controller {

   protected function init()
   {
      //		Kontrola práv
      $this->checkControllRights();
      $this->category()->getModule()->setDataDir('shop');
   }

   public function mainController()
   {
      $model = new Shop_Model_Customers();

      $customers = $model
         ->joinFK(Shop_Model_Customers::COLUMN_ID_USER)
         ->joinFK(Shop_Model_Customers::COLUMN_ID_GROUP)
         ->join(Shop_Model_Customers::COLUMN_ID, 'Shop_Model_Orders', Shop_Model_Orders::COLUMN_ID_CUSTOMER,
         array(
            'totalPaid' => 'SUM('.Shop_Model_Orders::COLUMN_TOTAL.')'
         ))
         ->groupBy(Shop_Model_Customers::COLUMN_ID);

      $orders = array();
//      if(!$this->getRequestParam('nord', false) && !$this->getRequestParam('nord', false) && !$this->getRequestParam('dord', false)){
//         $orders[NoveTrhy_Model_Registrations::COLUMN_TIME] = Model_ORM::ORDER_DESC;
//      }
      if($this->getRequestParam('nord', false)){
         $orders[Model_Users::COLUMN_NAME] = $this->getRequestParam('nord', "a") == "a" ? Model_ORM::ORDER_ASC : Model_ORM::ORDER_DESC;
      }
      if($this->getRequestParam('snord', false)){
         $orders[Model_Users::COLUMN_SURNAME] = $this->getRequestParam('nord', "a") == "a" ? Model_ORM::ORDER_ASC : Model_ORM::ORDER_DESC;
      }
      if($this->getRequestParam('emord', false)){
         $orders[Model_Users::COLUMN_MAIL] = $this->getRequestParam('emord', "a") == "a" ? Model_ORM::ORDER_ASC : Model_ORM::ORDER_DESC;
      }
      if($this->getRequestParam('ptord', false)){
         $orders['totalPaid'] = $this->getRequestParam('ptord', "a") == "a" ? Model_ORM::ORDER_ASC : Model_ORM::ORDER_DESC;
      }

      $model->order($orders);

      $this->view()->customers = $model->records();

      $modelCustGroups = new Shop_Model_CustomersGroups();
      $this->view()->customersGroups = $modelCustGroups->records();


      $formDel = new Form('cust_del_');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_NotEmpty());
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $formDel->addElement($elemId);

      $elemSubmit = new Form_Element_Submit('del', $this->tr('Smazat'));
      $formDel->addElement($elemSubmit);

      if($formDel->isValid()){
         $model->delete($formDel->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Zákazník byl smazán'));
         $this->link()->redirect();
      }

      $this->view()->formDelete = $formDel;

   }

   public function changeCustomerController()
   {
      $action = $this->getRequestParam('action', false);
      $id = $this->getRequestParam('id', false);

      if(!$action || !$id){
         throw new UnexpectedValueException($this->tr('Nebyly předány potřebné parametry'));
      }

      switch ($action){
         case 'delete':
            break;

         case 'changegrp':
            $m = new Shop_Model_Customers();
            $cust = $m->where(Shop_Model_Customers::COLUMN_ID." = :id", array('id' => $id))->record();
            $idg = $this->getRequestParam('idg', false);

            if(!$idg || !$cust){
               throw new UnexpectedValueException($this->tr('Zákazník neexistuje'));
            }

            $cust->{Shop_Model_Customers::COLUMN_ID_GROUP} = $idg;
            $cust->save();
            $this->infoMsg()->addMessage($this->tr('Zákazník byl zařazen do skupiny'));
            break;
      }

   }
}
