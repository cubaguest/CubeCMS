<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class ShopOrders_Controller extends Controller {
   
   protected function init()
   {
      $this->category()->getModule()->setDataDir('shop');
   }
   
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
   }
   
   public function ordersListController() {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Shop_Model_Orders::COLUMN_ID);
      // search
      
      $modelOrders = new Shop_Model_Orders();

      $order = Model_ORM::ORDER_ASC;
      if($jqGrid->request()->order == 'desc'){
         $order = Model_ORM::ORDER_DESC;
      }
      
      switch ($jqGrid->request()->orderField) {
         case 'id':
            $modelOrders->order(array(Shop_Model_Orders::COLUMN_ID => $order));
            break;
         case 'time':
            $modelOrders->order(array(Shop_Model_Orders::COLUMN_TIME_ADD => $order));
            break;
         case 'status':
            $modelOrders->order(array(Shop_Model_Orders::COLUMN_CUSTOMER_NAME => $order));
            break;
         case 'price':
            $modelOrders->order(array(Shop_Model_Orders::COLUMN_TOTAL => $order));
            break;
         case 'name':
         default:
            $modelOrders->order(array(Shop_Model_Orders::COLUMN_CUSTOMER_NAME => $order));
            break;
      }
      
      if ($jqGrid->request()->isSearch()) {
//         $count = $modelAddresBook->searchCount($jqGrid->request()->searchString(),
//            (int)$this->getRequestParam('idgrp', Mails_Model_Groups::GROUP_ID_ALL),
//            $jqGrid->request()->searchField(),$jqGrid->request()->searchType());
//         $jqGrid->respond()->setRecords($count);
//
//         $book = $modelAddresBook->search($jqGrid->request()->searchString(),
//            (int)$this->getRequestParam('idgrp', Mails_Model_Groups::GROUP_ID_ALL),
//            $jqGrid->request()->searchField(),$jqGrid->request()->searchType(),
//            ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(),
//            $jqGrid->request()->rows, $jqGrid->request()->orderField, $jqGrid->request()->order);
      } else {
      // list
//         $groups = $this->getAllowedGroups($jqGrid);
      }
      
//      $modelSt = new Shop_Model_OrderStatus();
      $modelHistory = new Shop_Model_OrdersHistory();
      $modelState = new Shop_Model_OrdersStates();
      $modelOrders
      ->columns(array('*',
         'status' => 
         '(SELECT CONCAT_WS( \';\' , os.'. $modelState->getLangColumn(Shop_Model_OrdersStates::COLUMN_NAME).',os.'.Shop_Model_OrdersStates::COLUMN_COLOR.') '
          . ' FROM '.$modelHistory->getTableName().' AS oh'
         .' LEFT JOIN '.$modelState->getTableName().' AS os ON os.'.Shop_Model_OrdersStates::COLUMN_ID.' = oh.'.  Shop_Model_OrdersHistory::COLUMN_ID_STATE
         .' WHERE oh.'.Shop_Model_OrdersHistory::COLUMN_ID_ORDER.' = '.$modelOrders->getTableShortName().'.'.Shop_Model_Orders::COLUMN_ID
         .' ORDER BY oh.'.  Shop_Model_OrdersHistory::COLUMN_TIME_ADD.' DESC LIMIT 0,1)'));
      
      $jqGrid->respond()->sql = $modelOrders->getSQLQuery();
      
      $jqGrid->respond()->setRecords($modelOrders->count());
      
      $fromRow = ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage();
      
      $orders = $modelOrders->limit($fromRow, $jqGrid->request()->rows)->records();
      // out
      foreach ($orders as $order) {
         $pickupDate = "";
         if($order->{Shop_Model_Orders::COLUMN_PICKUP_DATE} != null){
            $pickupDate = vve_date("%x", new DateTime($order->{Shop_Model_Orders::COLUMN_PICKUP_DATE}));
         }
         $name = $order->{Shop_Model_Orders::COLUMN_CUSTOMER_NAME}
            . ( $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY} != null ? " (". $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY} .')' : null );
         $status = explode(';', $order->status);   
            
         array_push($jqGrid->respond()->rows, 
            array('id' => $order->{Shop_Model_Orders::COLUMN_ID},
                  'name' => $name,
                  'neworder' => $order->{Shop_Model_Orders::COLUMN_IS_NEW},
                  'time' => vve_date('%x %X', new DateTime($order->{Shop_Model_Orders::COLUMN_TIME_ADD})),
                  'pickupdate' => $pickupDate,
                  'status' => $status[0],
                  'color' => isset($status[1]) ? $status[1] : null,
                  'price' => $order->{Shop_Model_Orders::COLUMN_TOTAL},
                     ));
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function viewOrderController()
   {
      $this->checkReadableRights();
      
      $modelOrders = new Shop_Model_Orders();
      
      $order = $modelOrders->record($this->getRequest('id'));
      
      if($order == false){ return false;}
      $this->view()->order = $order;
      
      // už není nová
      $order->{Shop_Model_Orders::COLUMN_IS_NEW} = false;
      $modelOrders->save($order);
      
      $modelItems = new Shop_Model_OrderItems();
      
      $this->view()->items = $modelItems->where(Shop_Model_OrderItems::COLUMN_ID_ORDER.' = :ido', 
         array('ido' => $order->{Shop_Model_Orders::COLUMN_ID}))->records();
         
      $this->changeStatusController($order->{Shop_Model_Orders::COLUMN_ID});
   }

   public function changeStatusController($id = null)
   {
      $formChangeStatus = new Form('status');
      $formChangeStatus->setProtected(false);
      $formChangeStatus->setAction($this->link()->route('changeStatus'));
      
      $eNameSel = new Form_Element_Select('stateId', $this->tr('Název'));
      
      $mState = new Shop_Model_OrdersStates();
      $states = $mState->where(Shop_Model_OrdersStates::COLUMN_DELETED.' = 0', array())->records();
      foreach ($states as $value) {
         $eNameSel->addOption($value->{Shop_Model_OrdersStates::COLUMN_NAME}, $value->getPK());
      }
      $formChangeStatus->addElement($eNameSel);
      
      $eId = new Form_Element_Hidden('id');
      if($id != null){
         $eId->setValues($id);
      }
      $formChangeStatus->addElement($eId);
      
      $eNote = new Form_Element_TextArea('note', $this->tr('Poznámka'));
      $formChangeStatus->addElement($eNote);
      
      $eAdd = new Form_Element_Submit('add', $this->tr('Uložit'));
      $formChangeStatus->addElement($eAdd);
      
      $eInformCustomer = new Form_Element_Checkbox('infoCust', $this->tr('Informovat zákazníka e-mailem'));
      $eInformCustomer->setValues(true);
      $formChangeStatus->addElement($eInformCustomer);
      // checboxy o upozornění

      if($formChangeStatus->isValid()){
         
         $order = Shop_Model_Orders::getRecord($formChangeStatus->id->getValues());
         $order->changeState($formChangeStatus->stateId->getValues(), $formChangeStatus->infoCust->getValues(), $formChangeStatus->note->getValues());
         $this->view()->newStatus = $order->getLastState();
         
         $this->infoMsg()->addMessage($this->tr('Změna byla uložena'));
         $this->link()->route()->reload();
      }
      $this->view()->formStatus = $formChangeStatus;
   }
   
   public function deleteOrderController()
   {
      $this->checkControllRights();
      
      if(!isset ($_POST['id'])){
         throw new UnexpectedValueException($this->tr('Nebylo předáno ID objednávky pro mazání'));
      }
      $modelOrders = new Shop_Model_Orders();
      
      $modelOrders->delete($_POST['id']);
      $this->infoMsg()->addMessage($this->tr('Objednávka byla smazána'));
      
   }
   
   public function exportOrderController()
   {
//      $this->checkControllRights();
      
      $type = $this->getRequest('output', 'pdf');
      
      $id = $this->getRequest('id', 0);
      $modelOrders = new Shop_Model_Orders();
      $modelOrderItems = new Shop_Model_OrderItems();
      $modelOrderStatuses = new Shop_Model_OrderStatus();
      $order = $modelOrders->record($id);
      
      if($order == false){
         return false;
      }
      
      $orderItems = $modelOrderItems->where(Shop_Model_OrderItems::COLUMN_ID_ORDER." = :ido", array('ido' => $id))->records();
      $orderStatuses = $modelOrderStatuses->where(Shop_Model_OrderStatus::COLUMN_ID_ORDER." = :ido", array('ido' => $id))->records();
      
      if($type == 'pdf'){
         $this->exportOrderPDF($order, $orderItems, $orderStatuses);
      }
      
   }
   
   protected function exportOrderPDF($order, $orderItems, $orderStatuses) 
   {
      $c = new Component_Tcpdf();
      $fileName = 'order-'.$order->{Shop_Model_Orders::COLUMN_ID}.".pdf";
      
      $c->pdf()->AddPage();
      $family = $c->pdf()->getFontFamily();
      
      $c->pdf()->SetFontSize(20);
      $c->pdf()->Write(0, $this->tr('Objednávka č.').$order->{Shop_Model_Orders::COLUMN_ID}, "", false, "", true);
      $c->pdf()->Ln();
      
      $wAddress1 = 90;
      $wAddress2 = 90;
      $c->pdf()->SetFont($family, 'B', 16);
      $c->pdf()->Cell($wAddress1, 7, $this->tr('Fakturační adresa'), 0, 0, 'L');
      $c->pdf()->Cell($wAddress2, 7, $this->tr('Dodací adresa'), 0, 1, 'L');
      
      $addr1 = null;
      if($order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY} != null){
         $addr1 .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY}."\n";
      }
      $addr1 .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_NAME}."\n";
      $addr1 .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_STREET}."\n";
      $addr1 .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_CITY} . " ". $order->{Shop_Model_Orders::COLUMN_CUSTOMER_POST_CODE}."\n";
      $addr1 .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COUNTRY}."\n";
      $addr1 .= "\n";
      if($order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_IC} != null){
         $addr1 .= "IČ: ".$order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_IC}."\n";
      }
      if($order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_DIC} != null){
         $addr1 .= "DIČ: ".$order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_DIC}."\n";
      }
      if($order->{Shop_Model_Orders::COLUMN_CUSTOMER_EMAIL} != null){
         $addr1 .= "E-mail: ".$order->{Shop_Model_Orders::COLUMN_CUSTOMER_EMAIL}."\n";
      }
      if($order->{Shop_Model_Orders::COLUMN_CUSTOMER_PHONE} != null){
         $addr1 .= "Tel.: ".$order->{Shop_Model_Orders::COLUMN_CUSTOMER_PHONE}."\n";
      }
      
      $addr2 = null;
      $addr2 .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_NAME}."\n";
      $addr2 .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_STREET}."\n";
      $addr2 .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_CITY} . " ". $order->{Shop_Model_Orders::COLUMN_DELIVERY_POST_CODE}."\n";
      $addr2 .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_COUNTRY}."\n";
      $addr2 .= "\n";
      
      $c->pdf()->SetFont($family, '', 12);
      $c->pdf()->MultiCell($wAddress1, 60, $addr1, 1, 'L', 0, 0);
      $c->pdf()->MultiCell($wAddress2, 60, $addr2, 1, 'L', 0, 1);
      $c->pdf()->Ln(5);

      
      $c->pdf()->SetFont($family, 'B', 14);
      $c->pdf()->Write(0, $this->tr('Souhrn položek'), "", false, "", 1);
      
      
      $wName = 100;
      $wSum = 40;
      $wPrice = 40;
      
      $c->pdf()->SetFont($family, 'B', 12);
      $c->pdf()->Cell($wName, 5, $this->tr('Název'), 1, 0, 'L');
      $c->pdf()->Cell($wSum, 5, $this->tr('Množství'), 1, 0, 'L');
      $c->pdf()->Cell($wPrice, 5, $this->tr('Cena'), 1, 1, 'L');
      
      $c->pdf()->SetFont($family, '', 12);
      foreach ($orderItems as $item) {
         $c->pdf()->Cell($wName, 5, $item->{Shop_Model_OrderItems::COLUMN_NAME}, 1, 0, 'L');
         $c->pdf()->Cell($wSum, 5, $item->{Shop_Model_OrderItems::COLUMN_QTY}. ' ' . $item->{Shop_Model_OrderItems::COLUMN_UNIT}, 1, 0, 'L');
         $c->pdf()->Cell($wPrice, 5, Shop_Tools::getFormatedPrice($item->{Shop_Model_OrderItems::COLUMN_PRICE}), 1, 1, 'L');
      }
      
      $c->pdf()->Ln();
      
      $c->pdf()->SetFont($family, 'B', 14);
      $c->pdf()->Write(0, $this->tr('Doručení a platba'), "", false, "", 1);
      
      $c->pdf()->SetFont($family, '', 12);
      $shippingText = $order->{Shop_Model_Orders::COLUMN_SHIPPING_METHOD};
      if($order->{Shop_Model_Orders::COLUMN_PICKUP_DATE} != null){
         $shippingText .= sprintf($this->tr(' - datum odběru: %s'), vve_date("%x", new DateTime($order->{Shop_Model_Orders::COLUMN_PICKUP_DATE})));
      }
      $c->pdf()->Cell($wName + $wSum, 5, $shippingText, 1, 0, 'L');
      $shippingPrice = $order->{Shop_Model_Orders::COLUMN_SHIPPING_PRICE} != 0 ?
         Shop_Tools::getFormatedPrice($order->{Shop_Model_Orders::COLUMN_SHIPPING_PRICE}) : $this->tr('Zdarma') ;
      $c->pdf()->Cell($wPrice, 5, $shippingPrice, 1, 1, 'L');
      
      $paymentPrice = $order->{Shop_Model_Orders::COLUMN_PAYMENT_PRICE} != 0 ?
         Shop_Tools::getFormatedPrice($order->{Shop_Model_Orders::COLUMN_PAYMENT_PRICE}) : $this->tr('Zdarma') ;
      $c->pdf()->Cell($wName + $wSum, 5, $order->{Shop_Model_Orders::COLUMN_PAYMENT_METHOD}, 1, 0, 'L');
      $c->pdf()->Cell($wPrice, 5, $paymentPrice, 1, 1, 'L');
      
      $c->pdf()->Ln();
      
      $c->pdf()->SetFont($family, 'B', 16);
      $c->pdf()->Cell($wName + $wSum, 5, $this->tr('Cena celkem'), 1, 0, 'L');
      $c->pdf()->Cell($wPrice, 5, Shop_Tools::getFormatedPrice($order->{Shop_Model_Orders::COLUMN_TOTAL}), 1, 1, 'L');
      
      
      $c->pdf()->Ln();
      
      $c->pdf()->SetFont($family, 'B', 14);
      $c->pdf()->Write(0, $this->tr('Stavy objednávky'), "", false, "", 1);
      $wDate = 40;
      $wName = 25;
      $wNote = 115;
      $c->pdf()->SetFont($family, 'B', 12);
      $c->pdf()->Cell($wDate, 5, $this->tr('Datum a čas'), 1, 0, 'L');
      $c->pdf()->Cell($wName, 5, $this->tr('Název'), 1, 0, 'L');
      $c->pdf()->Cell($wNote, 5, $this->tr('Poznámka'), 1, 1, 'L');
      
      foreach ($orderStatuses as $item) {
         $c->pdf()->SetFont($family, '', 10);
         $c->pdf()->Cell($wDate, 5, vve_date('%X %x', new DateTime($item->{Shop_Model_OrderStatus::COLUMN_TIME_ADD})), 1, 0, 'L');
         $c->pdf()->Cell($wName, 5, $item->{Shop_Model_OrderStatus::COLUMN_NAME}, 1, 0, 'L');
         $c->pdf()->SetFont($family, 'I', 10);
         $c->pdf()->Cell($wNote, 5, $item->{Shop_Model_OrderStatus::COLUMN_NOTE}, 1, 1, 'L');
//         if($item->{Shop_Model_OrderStatus::COLUMN_NOTE} != null){
//            $c->pdf()->SetFont($family, 'I', 10);
//            $c->pdf()->Cell($wDate, 5, null, 0, 0, 'L');
//            $c->pdf()->Cell($wName, 5, $item->{Shop_Model_OrderStatus::COLUMN_NOTE}, 1, 1, 'L');
//         }
//         $c->pdf()->Ln(5);
      }
      
      
      $c->pdf()->Output($fileName, 'D');
      
      exit();
   }
}
