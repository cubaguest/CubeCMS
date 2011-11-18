<?php
class ShopCart_Controller extends Controller {
   
   protected function init()
   {
      $this->category()->getModule()->setDataDir('shop');
   }

   public function mainController() {
      $this->checkReadableRights();
      
      $basket = new Shop_Basket();
      $basket->loadItems();
      
      $this->view()->basket = $basket;
      if($basket->isEmpty()){
         return;
      }
      
      $formItems = new Form('basket_items_');
      $formItems->setProtected(false);
      
      $eDel = new Form_Element_Checkbox('del', $this->tr('Odstranit'));
      $eDel->setDimensional();
      $eDel->setValues(false);
      $formItems->addElement($eDel);
      
      $eQty = new Form_Element_Text('qty', $this->tr('Množství'));
      $eQty->setDimensional();
      $eQty->addValidation(new Form_Validator_NotEmpty());
      $eQty->addValidation(new Form_Validator_IsNumber($this->tr('počet zboží musí být celé číslo'), Form_Validator_IsNumber::TYPE_INT));
      $formItems->addElement($eQty);
      
      $eSet = new Form_Element_Submit('set', $this->tr('Aktualizovat'));
      $formItems->addElement($eSet);
      
      if($formItems->isSend()){
         $newQty = $formItems->qty->getValues();
         // kontroly minimálního množství
         
         
      }
      
      if($formItems->isValid()){
         $delItems = $formItems->del->getValues();
         
         if($delItems != false){
            foreach ($delItems as $idProduct => $qty) {
               $basket->deleteItem($idProduct);
               unset ($newQty[$idProduct]);
            }
         }
         
         foreach ($newQty as $idProduct => $qty) {
            $basket->editQty($idProduct, $qty);
         }
         $this->link()->reload();
      }
      
      $this->view()->formItems = $formItems;
      
      $formReset = new Form('basket_reset_');
      $formReset->setProtected(false);
      $eSend = new Form_Element_Submit('reset', $this->tr('Vymazat položky'));
      $formReset->addElement($eSend);
      
      if($formReset->isValid()){
         $basket->clear();
         $this->infoMsg()->addMessage($this->tr('Všechny položky byly vymazány'));
         $this->link()->reload();
      }
      $this->view()->formReset = $formReset;
      
      // doprava a platba
      
      $modelShippings = new Shop_Model_Shippings();
      $modelPayments = new Shop_Model_Payments();
      
      $formGoNext = new Form('goto_order_');
      $formGoNext->setProtected(false);
      
      $eShipping = new Form_Element_Select('shipping', $this->tr('Doprava'));
      $shippings = $modelShippings->records();
      $shippingDisallowedPayments = array();
      $sh = $shippingsArray = array();
      foreach ($shippings as $shipping) {
         $str = (string)$shipping->{Shop_Model_Shippings::COLUMN_NAME};
         // free shipping
         $price = $shipping->{Shop_Model_Shippings::COLUMN_VALUE};
         if($basket->getPrice() >= VVE_SHOP_FREE_SHIPPING ){
            $price = 0;
         }
         $str .= $price != 0 ? ' ('.$price.' Kč)' : null;
         $eShipping->setOptions(array($str => $shipping->{Shop_Model_Shippings::COLUMN_ID}), true);
         
         $sh[$shipping->{Shop_Model_Shippings::COLUMN_ID}] = $price;
         
         $shippingDisallowedPayments[$shipping->{Shop_Model_Shippings::COLUMN_ID}] = 
            $shipping->{Shop_Model_Shippings::COLUMN_DISALLOWED_PAYMENTS} == null ? 
            array() : explode(';', $shipping->{Shop_Model_Shippings::COLUMN_DISALLOWED_PAYMENTS});
            
         $shippingsArray[$shipping->{Shop_Model_Shippings::COLUMN_ID}] = $shipping;   
      }
      $this->view()->shippings = $sh;
      if(isset ($_SESSION['shop_order']['shipping'])){
         $eShipping->setValues($_SESSION['shop_order']['shipping']);
      }
      $formGoNext->addElement($eShipping);
      
      $ePayment = new Form_Element_Select('payment', $this->tr('Platba'));
      
      $payments = $modelPayments->records();
      $py = $paymentsArray = array();
      foreach ($payments as $payment) {
         $str = (string)$payment->{Shop_Model_Payments::COLUMN_NAME};
         // free shipping
         $price = $payment->{Shop_Model_Payments::COLUMN_PRICE_ADD};
         if($basket->getPrice() >= VVE_SHOP_FREE_SHIPPING ){
            $price = 0;
         }
         $str .= $price != 0 ? ' ('.$price.' Kč)' : null;
         $ePayment->setOptions(array($str => $payment->{Shop_Model_Payments::COLUMN_ID}), true);
         
         $py[$payment->{Shop_Model_Payments::COLUMN_ID}] = $price;
         $paymentsArray[$payment->{Shop_Model_Payments::COLUMN_ID}] = $payment;
      }
      $this->view()->payments = $py;
      if(isset ($_SESSION['shop_order']['payment'])){
         $ePayment->setValues($_SESSION['shop_order']['payment']);
      }
      $formGoNext->addElement($ePayment);
      
      $eSend = new Form_Element_Submit('send', $this->tr('Přejít k obědnávce'));
      $formGoNext->addElement($eSend);
      if($formGoNext->isSend()){
         // kontrola způsobu platby
         if(in_array($formGoNext->payment->getValues(), $shippingDisallowedPayments[$formGoNext->shipping->getValues()])){
            $formGoNext->payment->setError($this->tr('Tuto kombinaci dopravy a platby nelze provést. Vyberte jiný způsob platby.'));
         }
      }
      
      if($formGoNext->isValid()){
         // uložení dopravy a platby
         $store = $this->getOrderStore();
         $pId = $formGoNext->payment->getValues();
         $sId = $formGoNext->shipping->getValues();
         
         $_SESSION['shop_order']['payment'] = $pId;
         $_SESSION['shop_order']['shipping'] = $sId;
         
         $this->link()->route('order')->reload();
      }
      
      $this->view()->formNext = $formGoNext;
      
   }

   public function orderController()
   {
      $basket = new Shop_Basket();
      if($basket->isEmpty()){
         $this->link()->route()->reload();
      }
      $this->orderControllerForm();
   }
   
   protected function orderControllerForm()
   {
      // načtení zón a způsobů doručení a plateb
      $modelZones = new Shop_Model_Zones();
      $zones = $modelZones->records();
      
      $formOrder = new Form('order_');
      $formOrder->setProtected(false);
      
      $fCustomerInfo = $formOrder->addGroup('customer', $this->tr('Informace o zákazníkovi'));
      
      $eCustomerName = new Form_Element_Text('customerName', $this->tr('Jméno a příjmení'));
      $eCustomerName->addValidation(new Form_Validator_NotEmpty());
      $formOrder->addElement($eCustomerName, $fCustomerInfo);
      
      $eCustomerEmail = new Form_Element_Text('customerEmail', $this->tr('E-mail'));
      $eCustomerEmail->addValidation(new Form_Validator_NotEmpty());
      $eCustomerEmail->addValidation(new Form_Validator_Email());
      $formOrder->addElement($eCustomerEmail, $fCustomerInfo);
      
      $eCustomerPhone = new Form_Element_Text('customerPhone', $this->tr('Telefon'));
      $eCustomerPhone->addValidation(new Form_Validator_NotEmpty());
      $eCustomerPhone->addValidation(new Form_Validator_Regexp(Form_Validator_Regexp::REGEXP_PHONE_CZSK));
      $formOrder->addElement($eCustomerPhone, $fCustomerInfo);
      
      
      // adresa nakupujícího
      $fGPayment = $formOrder->addGroup('payment', $this->tr('Fakturační adresa'));
      
      $ePaymentStreet = new Form_Element_Text('paymentStreet', $this->tr('Ulice a čp'));
      $ePaymentStreet->addValidation(new Form_Validator_NotEmpty());
      $formOrder->addElement($ePaymentStreet, $fGPayment);
      
      $ePaymentCity = new Form_Element_Text('paymentCity', $this->tr('Město'));
      $ePaymentCity->addValidation(new Form_Validator_NotEmpty());
      $formOrder->addElement($ePaymentCity, $fGPayment);
      
      $ePaymentPostCode = new Form_Element_Text('paymentPostCode', $this->tr('PSČ'));
      $ePaymentPostCode->addValidation(new Form_Validator_NotEmpty());
      $formOrder->addElement($ePaymentPostCode, $fGPayment);
      
      // stát je načten ze zones
      $ePaymentState = new Form_Element_Select('paymentCountry', $this->tr('Stát'));
      
      foreach ($zones as $zone) {
         $ePaymentState->setOptions(array($zone->{Shop_Model_Zones::COLUMN_NAME} => $zone->{Shop_Model_Zones::COLUMN_ID}), true);
      }
      $formOrder->addElement($ePaymentState, $fGPayment);
      
      $ePaymentCompanyName = new Form_Element_Text('paymentCompanyName', $this->tr('Firma'));
      $formOrder->addElement($ePaymentCompanyName, $fGPayment);
      
      $ePaymentCompanyIC = new Form_Element_Text('paymentCompanyIC', $this->tr('IČ'));
      $formOrder->addElement($ePaymentCompanyIC, $fGPayment);
      
      $ePaymentCompanyDIC = new Form_Element_Text('paymentCompanyDIC', $this->tr('DIČ'));
      $formOrder->addElement($ePaymentCompanyDIC, $fGPayment);
      
      
      // dodací adresa
      $fGDelivery = $formOrder->addGroup('shipping', $this->tr('Dodací adresa'));
      
      $eIsDeliveryAddress = new Form_Element_Checkbox('isDeliveryAddress', $this->tr('Jiná dodací adresa'));
      $eIsDeliveryAddress->setSubLabel($this->tr('Pokud chcete zaslat zboží na jinou adresu než fakturační.'));
      $formOrder->addElement($eIsDeliveryAddress, $fGDelivery);
      
      $eDeliveryName = new Form_Element_Text('deliveryName', $this->tr('Jméno a příjmení'));
      $eDeliveryName->addValidation(new Form_Validator_NotEmpty());
      $formOrder->addElement($eDeliveryName, $fGDelivery);
      
      $eDeliveryStreet = new Form_Element_Text('deliveryStreet', $this->tr('Ulice a čp'));
      $eDeliveryStreet->addValidation(new Form_Validator_NotEmpty());
      $formOrder->addElement($eDeliveryStreet, $fGDelivery);
      
      $eDeliveryCity = new Form_Element_Text('deliveryCity', $this->tr('Město'));
      $eDeliveryCity->addValidation(new Form_Validator_NotEmpty());
      $formOrder->addElement($eDeliveryCity, $fGDelivery);
      
      $eDeliveryPostCode = new Form_Element_Text('deliveryPostCode', $this->tr('PSČ'));
      $eDeliveryPostCode->addValidation(new Form_Validator_NotEmpty());
      $formOrder->addElement($eDeliveryPostCode, $fGDelivery);
      
      // stát je načten ze zones
      $eDeliveryCountry = new Form_Element_Select('deliveryCountry', $this->tr('Stát'));
      
      foreach ($zones as $zone) {
         $eDeliveryCountry->setOptions(array($zone->{Shop_Model_Zones::COLUMN_NAME} => $zone->{Shop_Model_Zones::COLUMN_ID}), true);
      }
      $formOrder->addElement($eDeliveryCountry, $fGDelivery);
      
      
      // ostatní (vytvořit účet, newsletter)
      $eNote = new Form_Element_TextArea('note', $this->tr('Poznámka'));
      $formOrder->addElement($eNote);
      
//      $eNewsletter = new Form_Element_Checkbox('newsletter', $this->tr('Novinky e-mailem'));
//      $eNewsletter->setValues(true);
//      $eNewsletter->setSubLabel($this->tr('Registrovat k se odběru novinek na zadaný e-mail'));
//      $formOrder->addElement($eNewsletter);
      
//      $eCreateAccount = new Form_Element_Checkbox('createAcc', $this->tr('Vytvořit účet'));
//      $eCreateAccount->setSubLabel($this->tr('Vytvořit uživatelský účet ze zadaných údajů pro příští nákup'));
//      $formOrder->addElement($eCreateAccount);
//      
//      $eCreateAccountPass = new Form_Element_Text('createAccPassword', $this->tr('Heslo'));
//      $formOrder->addElement($eCreateAccountPass);
      
      $eSend = new Form_Element_SaveCancel('send', array($this->tr('Objednat'), $this->tr('Zpět do košíku')));
      $eSend->setCancelConfirm(false);
      $formOrder->addElement($eSend);
      
      // obnova dat pokud existují
      $this->restoreOrderInfo($formOrder);
      
      if($formOrder->isSend()){
         if($formOrder->send->getValues() == false){
            $this->link()->route()->reload();
         }
         
         if($formOrder->createAcc->getValues() == true){
            $eCreateAccountPass->addValidation(new Form_Validator_NotEmpty());
         }
         if($formOrder->isDeliveryAddress->getValues() == false){
            $formOrder->deliveryName->removeValidation('Form_Validator_NotEmpty');
            $formOrder->deliveryStreet->removeValidation('Form_Validator_NotEmpty');
            $formOrder->deliveryCity->removeValidation('Form_Validator_NotEmpty');
            $formOrder->deliveryPostCode->removeValidation('Form_Validator_NotEmpty');
         }
         // uložíme informace o zákazníkovi do session. Mohl by se totiž vrátit, nebo špatné položky v košíku, a určitě to nechce všechno vyplňovat znovu
         $this->storeOrderInfo($formOrder);
      }

      if($formOrder->isValid()){
         
         // objednávka je hotova
         $modelOrder = new Shop_Model_Orders();
         $modelOrderItems = new Shop_Model_OrderItems();
         $modelPayments = new Shop_Model_Payments();
         $modelShippings = new Shop_Model_Shippings();
         $modelStatus = new Shop_Model_OrderStatus();
         try {
            $basket = new Shop_Basket();
            $basket->loadItems();
         
            /*
             * Kontroly styvu zboží a odečty položek. 
             * Pokud nelze odečíst, nelze vytvořit objednávku a redirect s chybou na košík.
             * Rovnou odstranit objednávku
             * V chybě: které položka a kolik zbývá
             */
            $arrOfKeys = $arrOfBinds = array();
            foreach ($basket->getItems() as $item) {
               $k = $item->getId();
               $arrOfKeys[] = ':id'.$k;
               $arrOfBinds['id'.$k] = $k;
            }
         
            $modelProducts = new Shop_Model_Product();
            $modelProducts->lock('WRITE');
            
            $products = $modelProducts->setSelectAllLangs(false)
               ->columns(array(Shop_Model_Product::COLUMN_QUANTITY, Shop_Model_Product::COLUMN_NAME, Shop_Model_Product::COLUMN_UNIT))
               ->where(Shop_Model_Product::COLUMN_ID.' IN ('.  implode(',', $arrOfKeys).')', $arrOfBinds)->records();
            // kontrola dosupnosti zboží
            foreach ($products as $product) {
               /* @var $item Shop_Basket_Item */
               $prQty = $product->{Shop_Model_Product::COLUMN_QUANTITY};
               // zboží už je vyprodané
               if ($prQty == 0) {
                  throw new RangeException(
                     sprintf($this->tr('Omlouváme se, ale zboží "%s" je již vyprodané. Upravte prosím položky v košíku.'), 
                        $product->{Shop_Model_Product::COLUMN_NAME}));
               } 
               // zboží není v potřebném množství
               else if ($prQty != -1 && $prQty < $basket[$product->{Shop_Model_Basket::COLUMN_ID_PRODUCT}]->getQty()) {
                  throw new RangeException(
                     sprintf($this->tr("Omlouváme se, ale zboží %s není již v požadovaném množství. 
                        Upravte prosím položky v košíku. Aktuálně je dostupné %s %s."), 
                        $product->{Shop_Model_Product::COLUMN_NAME}, $prQty, $product->{Shop_Model_Product::COLUMN_UNIT} ));
               }
            }
            // samotný update zboží
            foreach ($products as $product) {
               if($product->{Shop_Model_Product::COLUMN_QUANTITY} != -1){
                  $product->{Shop_Model_Product::COLUMN_QUANTITY} = $product->{Shop_Model_Product::COLUMN_QUANTITY}
                     -$basket[$product->{Shop_Model_Basket::COLUMN_ID_PRODUCT}]->getQty();
                  $modelProducts->save($product);
               }
            }
            $modelProducts->unLock();
         } catch (RangeException $exc) {
            $modelProducts->unLock();
            $this->errMsg()->addMessage($exc->getMessage(), true);
            $this->link()->route()->reload();
         }
         
         $order = $modelOrder->newRecord();
         
         $order->{Shop_Model_Orders::COLUMN_CUSTOMER_NAME} = $formOrder->customerName->getValues();
         $order->{Shop_Model_Orders::COLUMN_CUSTOMER_PHONE} = $formOrder->customerPhone->getValues();
         $order->{Shop_Model_Orders::COLUMN_CUSTOMER_EMAIL} = $formOrder->customerEmail->getValues();
         $order->{Shop_Model_Orders::COLUMN_CUSTOMER_STREET} = $formOrder->paymentStreet->getValues();
         $order->{Shop_Model_Orders::COLUMN_CUSTOMER_CITY} = $formOrder->paymentCity->getValues();
         $order->{Shop_Model_Orders::COLUMN_CUSTOMER_POST_CODE} = $formOrder->paymentPostCode->getValues();
         
         $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COUNTRY} = 
            array_search($formOrder->paymentCountry->getValues(), $formOrder->paymentCountry->getOptions());
         
         $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY} = $formOrder->paymentCompanyName->getValues();
         $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_IC} = $formOrder->paymentCompanyIC->getValues();
         $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_DIC} = $formOrder->paymentCompanyDIC->getValues();
         
         if($formOrder->isDeliveryAddress->getValues() == true){
            $order->{Shop_Model_Orders::COLUMN_DELIVERY_NAME} = $formOrder->deliveryName->getValues();
            $order->{Shop_Model_Orders::COLUMN_DELIVERY_STREET} = $formOrder->deliveryStreet->getValues();
            $order->{Shop_Model_Orders::COLUMN_DELIVERY_CITY} = $formOrder->deliveryCity->getValues();
            $order->{Shop_Model_Orders::COLUMN_DELIVERY_POST_CODE} = $formOrder->deliveryPostCode->getValues();
            $order->{Shop_Model_Orders::COLUMN_DELIVERY_COUNTRY} = 
               array_search($formOrder->deliveryCountry->getValues(), $formOrder->deliveryCountry->getOptions());
         } else {
            $order->{Shop_Model_Orders::COLUMN_DELIVERY_NAME} = $formOrder->customerName->getValues();
            $order->{Shop_Model_Orders::COLUMN_DELIVERY_STREET} = $formOrder->paymentStreet->getValues();
            $order->{Shop_Model_Orders::COLUMN_DELIVERY_CITY} = $formOrder->paymentCity->getValues();
            $order->{Shop_Model_Orders::COLUMN_DELIVERY_POST_CODE} = $formOrder->paymentPostCode->getValues();
            $order->{Shop_Model_Orders::COLUMN_DELIVERY_COUNTRY} = 
               array_search($formOrder->paymentCountry->getValues(), $formOrder->paymentCountry->getOptions());
         }
         
         $productsPrice = $basket->getPrice();
         
         $payment = $modelPayments->record($_SESSION['shop_order']['payment']);
         $shipping = $modelShippings->record($_SESSION['shop_order']['shipping']);
         
         // metoda platby
         $order->{Shop_Model_Orders::COLUMN_PAYMENT_ID} = $payment->{Shop_Model_Payments::COLUMN_ID};
         $order->{Shop_Model_Orders::COLUMN_PAYMENT_METHOD} = $payment->{Shop_Model_Payments::COLUMN_NAME};
         $order->{Shop_Model_Orders::COLUMN_SHIPPING_ID} = $shipping->{Shop_Model_Shippings::COLUMN_ID};
         $order->{Shop_Model_Orders::COLUMN_SHIPPING_METHOD} = $shipping->{Shop_Model_Shippings::COLUMN_NAME};
         
         if($productsPrice >= VVE_SHOP_FREE_SHIPPING){ // doprava a platba zdarma
            $shippingPrice = 0;
            $paymentPrice = 0;
         } else {
            if(strpos($payment->{Shop_Model_Payments::COLUMN_PRICE_ADD}, '%') === false){
               $paymentPrice = (int)$payment->{Shop_Model_Payments::COLUMN_PRICE_ADD};
            } else {
               $paymentPrice = $productsPrice/100*(int)$payment->{Shop_Model_Payments::COLUMN_PRICE_ADD};
            }
            
            if(strpos($shipping->{Shop_Model_Shippings::COLUMN_VALUE}, '%') === false){
               $shippingPrice = (int)$shipping->{Shop_Model_Shippings::COLUMN_VALUE};
            } else {
               $shippingPrice = $productsPrice/100*(int)$shipping->{Shop_Model_Shippings::COLUMN_VALUE};
            }
         }
         
         $order->{Shop_Model_Orders::COLUMN_PAYMENT_PRICE} = $paymentPrice;
         $order->{Shop_Model_Orders::COLUMN_SHIPPING_PRICE} = $shippingPrice;
         
         $order->{Shop_Model_Orders::COLUMN_TOTAL} = $productsPrice + $paymentPrice + $shippingPrice;
         
         // registrace do newsletteru
//         if($formOrder->newsletter->getValues() == true){
//            
//         }
         
         // registrace uživatelského účtu
//         if($formOrder->createAcc->getValues() == true){
//            
//         }
         
         // ostatní položky objednávky
         $order->{Shop_Model_Orders::COLUMN_NOTE} = $formOrder->note->getValues();
         $order->{Shop_Model_Orders::COLUMN_IP} = $_SERVER['REMOTE_ADDR'];
//         Debug::log($_SESSION, $order);
         $orderId = $modelOrder->save($order);
         $order->{Shop_Model_Orders::COLUMN_ID} = $orderId;// bude se používt do mailů
         
         // foreach nad položkami v košíku a přidávání položek
         foreach ($basket as $id => $item) {
            /* @var $item Shop_Basket_Item */
            $orderItem = $modelOrderItems->newRecord();
            
            $orderItem->{Shop_Model_OrderItems::COLUMN_ID_ORDER} = $orderId;
            $orderItem->{Shop_Model_OrderItems::COLUMN_NAME} = (string)$item->getName();
            $orderItem->{Shop_Model_OrderItems::COLUMN_NOTE} = $item->getNote();
            $orderItem->{Shop_Model_OrderItems::COLUMN_PRICE} = $item->getPrice(true, false);
            $orderItem->{Shop_Model_OrderItems::COLUMN_QTY} = $item->getQty();
            $orderItem->{Shop_Model_OrderItems::COLUMN_TAX} = $item->getTax();
            $orderItem->{Shop_Model_OrderItems::COLUMN_UNIT} = $item->getUnit();
            $orderItem->{Shop_Model_OrderItems::COLUMN_CODE} = $item->getCode();
            $orderItem->{Shop_Model_OrderItems::COLUMN_ID_PRODUCT} = $id;
            $modelOrderItems->save($orderItem);
         }
         
         // přidání statusu
         $status = $modelStatus->newRecord();
         $status->{Shop_Model_OrderStatus::COLUMN_ID_ORDER} = $orderId;
         $status->{Shop_Model_OrderStatus::COLUMN_NAME} = VVE_SHOP_ORDER_DEFAULT_STATUS;
         $modelStatus->save($status);
         
         $basket->clear();
         
         $this->generateMails($order, $basket);
         $_SESSION['shop_order']['orderId'] = $orderId;
         $_SESSION['shop_order']['paymentClass'] = $payment->{Shop_Model_Payments::COLUMN_CLASS};
         $this->link()->route('orderComplete')->reload();
      }
      
      
      $this->view()->formOrder = $formOrder;
   }
   
   private function &getOrderStore()
   {
      if(!isset ($_SESSION['shop_order']) || !is_array($_SESSION['shop_order'])){
         $_SESSION['shop_order'] = array();
      }
      return $_SESSION['shop_order'];
   }

   private function generateMails($order, $basket)
   {
      $userMailText = $adminMailText = "Objednávka z {STRANKY}\n\n{INFO}\n\n{ZBOZI}\n\n{ADRESA_DODACI}\n\n{ADRESA_FAKTUROVACI}";
      if (is_file($this->module()->getDataDir().'mail_tpl_user.txt')) {
         $userMailText = file_get_contents($this->module()->getDataDir().'mail_tpl_user.txt');
      }
      
      if (is_file($this->module()->getDataDir().'mail_tpl_admin.txt')) {
         $adminMailText = file_get_contents($this->module()->getDataDir().'mail_tpl_admin.txt');
      }
      
      $input = '-';
      $multiplier = 120;
      $separator = str_repeat($input, $multiplier)."\n";
      
      /* info o objednávce */
      $orderInfo = "Informace o objednávce\n".$separator;
      $orderInfo .= 'Číslo objednávky / variabilní symbol: '.$order->{Shop_Model_Orders::COLUMN_ID}."\n";
      $orderInfo .= 'Datum objednávky: '.  vve_date("%x")."\n".$separator;
      
      /* Zboží */
      $orderItems = "Objednané zboží\n". $separator;
      foreach ($basket as $item) {
         // 1 Ks Náhrdelník (zlatý) = 500 Kč
         $itemStr = $item->getQty().' '.$item->getUnit().' '.$item->getName();
         if($item->getNote() != null){
            $itemStr .= ' ('.$item->getNote().')';
         }
         $priceStr = $item->getPrice().' '.VVE_SHOP_CURRENCY_NAME;
         
         $orderItems .= $itemStr.str_repeat('.', 5).$priceStr."\n";
      }
      $orderItems .= $separator;
      $orderItems .= 'Mezisoučet: '.$basket->getPrice().' '.VVE_SHOP_CURRENCY_NAME."\n";
      $orderItems .= $separator;
      // info k dopravě
      $orderItems .= 'Doprva: '.$order->{Shop_Model_Orders::COLUMN_SHIPPING_METHOD}.' '
         .$order->{Shop_Model_Orders::COLUMN_SHIPPING_PRICE}.' '.VVE_SHOP_CURRENCY_NAME."\n";
      $modelS = new Shop_Model_Shippings;   
      $ship = $modelS->record($order->{Shop_Model_Orders::COLUMN_SHIPPING_ID});
      if($ship != false){
         $orderItems .= preg_replace(array('/\n\n*/', '/\s{2,}/'),array("\n", " "), strip_tags($ship->{Shop_Model_Shippings::COLUMN_TEXT}))."\n";
      }
      $orderItems .= $separator;
      // info k platbě
      $orderItems .= 'Platba: '.$order->{Shop_Model_Orders::COLUMN_PAYMENT_METHOD}.' '
         .$order->{Shop_Model_Orders::COLUMN_PAYMENT_PRICE}.' '.VVE_SHOP_CURRENCY_NAME."\n";
      $modelP = new Shop_Model_Payments();   
      $payment = $modelP->record($order->{Shop_Model_Orders::COLUMN_PAYMENT_ID});
      if($payment != false){
         $orderItems .= preg_replace(array('/\n\n*/', '/\s{2,}/'),array("\n", " "), strip_tags($payment->{Shop_Model_Payments::COLUMN_TEXT}))."\n";
      }
      // kompletní cena   
      $orderItems .= $separator;
      $orderItems .= 'Cena celkem: '.($basket->getPrice()+$order->{Shop_Model_Orders::COLUMN_PAYMENT_PRICE}
         +$order->{Shop_Model_Orders::COLUMN_SHIPPING_PRICE}).' '.VVE_SHOP_CURRENCY_NAME."\n";
      $orderItems .= $separator;

      /* Adresa fakturační */
      $addressPayment = "Adresa fakturační:\n".$separator;
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY} != null ? $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY}."\n" : null;
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_DIC} != null ? 
         'DIČ: '.$order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_DIC}."\n" : null;
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_IC} != null ? 
         'IČ: '.$order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_IC}."\n\n" : null;
      
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_NAME}."\n";
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_STREET}."\n";
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_CITY}.' '.$order->{Shop_Model_Orders::COLUMN_CUSTOMER_POST_CODE}."\n";
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COUNTRY}."\n";
      $addressPayment .= $separator;
      
      /* adresa dodací */
      $addressShip = "Adresa dodací:\n".$separator;
      $addressShip .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_NAME}."\n";
      $addressShip .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_STREET}."\n";
      $addressShip .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_CITY}.' '.$order->{Shop_Model_Orders::COLUMN_DELIVERY_POST_CODE}."\n";
      $addressShip .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_COUNTRY}."\n";
      $addressShip .= $separator;
       
      /* poznamka */
      $note = null;
      if($order->{Shop_Model_Orders::COLUMN_NOTE} != null){
         $note = "Poznámka:\n".$separator;
         $note .= $order->{Shop_Model_Orders::COLUMN_NOTE}."\n".$separator;
      }
      
      $searchArr = array(
         '{STRANKY}',
         '{DATUM}',
         '{INFO}',
         '{ZBOZI}',
         '{ADRESA_DODACI}',
         '{ADRESA_FAKTURACNI}',
         '{POZNAMKA}',
         '{IP}',
      );
      $replaceArr = array(
         VVE_WEB_NAME,
         vve_date('%X %x'),
         $orderInfo,
         $orderItems,
         $addressShip,
         $addressPayment,
         $note,
         $_SERVER['REMOTE_ADDR'],
      );
      
      
      $userMailText = str_replace($searchArr, $replaceArr, $userMailText);
      $adminMailText = str_replace($searchArr, $replaceArr, $adminMailText);
      
//      Debug::log($userMailText, $adminMailText);
      
      $emailUser = new Email();
      $emailUser->addAddress($order->{Shop_Model_Orders::COLUMN_CUSTOMER_EMAIL});
      $emailUser->setFrom(VVE_NOREPLAY_MAIL);
      $emailUser->setSubject(sprintf('[NOVÁ OBJEDNÁVKA] číslo %s', $order->{Shop_Model_Orders::COLUMN_ID}));
      $emailUser->setContent($userMailText);
      $emailUser->send();
      
      if(VVE_SHOP_ORDER_MAIL != null){
         $emailAdmin = new Email();
         $emailAdmin->addAddress(VVE_SHOP_ORDER_MAIL);
         $emailAdmin->setFrom(VVE_NOREPLAY_MAIL);
         $emailAdmin->setSubject(sprintf('[NOVÁ OBJEDNÁVKA] číslo %s', $order->{Shop_Model_Orders::COLUMN_ID}));
         $emailAdmin->setContent($adminMailText);
         $emailAdmin->send();
      }
   }
   
   private function storeOrderInfo($form)
   {
      $_SESSION['shop_order']['orderinfo'] = array();
      foreach ($form as $element) {
         if( $element instanceof Form_Element_Text || $element instanceof Form_Element_TextArea
            || $element instanceof Form_Element_Select || $element instanceof Form_Element_Checkbox ){
            $_SESSION['shop_order']['orderinfo'][$element->getName()] = $element->getValues();
         }
      }
   }
   
   private function restoreOrderInfo(Form $form)
   {
      if(isset ($_SESSION['shop_order']['orderinfo'])){
         foreach ($_SESSION['shop_order']['orderinfo'] as $eName => $value) {
            $eName = str_replace($form->getPrefix(), '', $eName);
            $form->{$eName}->setValues($value);
         }
      }
   }


   public function orderCompleteController()
   {
      if(!isset ($_SESSION['shop_order']) || !isset ($_SESSION['shop_order']['orderId']) || $_SESSION['shop_order']['orderId'] == null){
         $this->link()->route()->reload();
      }
      $modelOrders = new Shop_Model_Orders();
      $modelItems = new Shop_Model_OrderItems();
      $order = $modelOrders->record($_SESSION['shop_order']['orderId']);
      
      $this->view()->order = $order;
      $this->view()->items = $modelItems
         ->where(Shop_Model_OrderItems::COLUMN_ID_ORDER.' = :ido', array('ido' => $order->{Shop_Model_Orders::COLUMN_ID}))
         ->records();
         
      $modelP = new Shop_Model_Payments();   
      $this->view()->payment = $modelP->record($order->{Shop_Model_Orders::COLUMN_PAYMENT_ID});
      
      $modelS = new Shop_Model_Shippings();   
      $this->view()->shipping = $modelS->record($order->{Shop_Model_Orders::COLUMN_SHIPPING_ID});
   }

   public function settings(&$settings, Form &$form) {
   }
}

?>