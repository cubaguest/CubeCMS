<?php
class ShopCart_Controller extends Controller {
   
   protected function init()
   {
      $this->category()->getModule()->setDataDir('shop');
   }

   public function mainController() {
      $this->checkReadableRights();
      
      $cart = new Shop_Cart();
      $cart->loadItems();
      
      $this->view()->cart = $cart;
      if($cart->isEmpty()){
         return;
      }

      if($this->getRequestParam('dp', false)){
         // delete product from cart
         $cart->deleteItem($this->getRequestParam('dp'));
         $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
         $this->link()->param('dp')->reload();
      }

      if($this->getRequestParam('updateQtyId', false) && $this->getRequestParam('qty', false)){
         $cart->editQty($this->getRequestParam('updateQtyId'), $this->getRequestParam('qty'));
         $this->infoMsg()->addMessage($this->tr('Množství bylo upraveno'));
         $this->link()->rmParam(array('updateQtyId', 'qty'))->reload();
      }

      $formItems = new Form('cart_items_');
      $formItems->setProtected(false);
      
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
         $newQty = $formItems->qty->getValues();
         foreach ($newQty as $idItem => $qty) {
            $cart->editQty($idItem, $qty);
         }
         $this->link()->reload();
      }
      
      $this->view()->formItems = $formItems;
      
      $formReset = new Form('cart_reset_');
      $formReset->setProtected(false);
      $eSend = new Form_Element_Submit('reset', $this->tr('Vymazat položky'));
      $formReset->addElement($eSend);
      
      if($formReset->isValid()){
         $cart->clear();
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
      
      if($cart->personalPickUpOnly()){
         $modelShippings->where(Shop_Model_Shippings::COLUMN_PERSONAL_PICKUP.' = 1', array());
      }
      $shippings = $modelShippings->records();
      
      $shippingDisallowedPayments = array();
      $sh = $shippingsArray = array();
      foreach ($shippings as $shipping) {
         $str = (string)$shipping->{Shop_Model_Shippings::COLUMN_NAME};
         // free shipping
         $price = $shipping->{Shop_Model_Shippings::COLUMN_VALUE};
         if($cart->getPrice() >= VVE_SHOP_FREE_SHIPPING && VVE_SHOP_FREE_SHIPPING != -1 ){
            $price = 0;
         }
         $str .= $price != 0 ? ' ('.Shop_Tools::getPrice($price).')' : null;
         $eShipping->setOptions(array($str => $shipping->{Shop_Model_Shippings::COLUMN_ID}), true);
         
         $sh[$shipping->{Shop_Model_Shippings::COLUMN_ID}] =
            array(
               'price' => $shipping->{Shop_Model_Shippings::COLUMN_VALUE},
               'name' => (string)$shipping->{Shop_Model_Shippings::COLUMN_NAME}
            );
         
         $shippingDisallowedPayments[$shipping->{Shop_Model_Shippings::COLUMN_ID}] = 
            $shipping->{Shop_Model_Shippings::COLUMN_DISALLOWED_PAYMENTS} == null ?
            array() : explode(';', $shipping->{Shop_Model_Shippings::COLUMN_DISALLOWED_PAYMENTS});
            
         $shippingsArray[$shipping->{Shop_Model_Shippings::COLUMN_ID}] = $shipping;   
      }
      $eShipping->setValues( key($sh) );

      $this->view()->shippings = $sh;
      $this->view()->disallowPayments = $shippingDisallowedPayments;
      if(isset ($_SESSION['shop_order']['shipping'])){
         $eShipping->setValues($_SESSION['shop_order']['shipping']);
      }
      $formGoNext->addElement($eShipping);

      if($cart->needPickUpDate()){
         $ePickUpDate = new Form_Element_Text('pickupDate', $this->tr('Datum vyzvednutí'));
         $ePickUpDate->addValidation(new Form_Validator_NotEmpty());
         $ePickUpDate->addValidation(new Form_Validator_Date());
         $ePickUpDate->addFilter(new Form_Filter_DateTimeObj());
         $formGoNext->addElement($ePickUpDate);
      }
      
      $ePayment = new Form_Element_Select('payment', $this->tr('Platba'));
      
      $payments = $modelPayments->records();
      $py = $paymentsArray = array();
      foreach ($payments as $payment) {
         $str = (string)$payment->{Shop_Model_Payments::COLUMN_NAME};
         // free shipping
         $price = $payment->{Shop_Model_Payments::COLUMN_PRICE_ADD};
         if($cart->getPrice() >= VVE_SHOP_FREE_SHIPPING && VVE_SHOP_FREE_SHIPPING != -1 ){
            $price = 0;
         }
         $str .= $price != 0 ? ' ('.$price.' Kč)' : null;
         $ePayment->setOptions(array($str => $payment->{Shop_Model_Payments::COLUMN_ID}), true);
         
         $py[$payment->{Shop_Model_Payments::COLUMN_ID}] =
            array(
               'price' => $payment->{Shop_Model_Payments::COLUMN_PRICE_ADD},
               'name' => (string)$payment->{Shop_Model_Payments::COLUMN_NAME}
            );

         $paymentsArray[$payment->{Shop_Model_Payments::COLUMN_ID}] = $payment;
      }
      $ePayment->setValues( key($py) );
      $this->view()->payments = $py;
      if(isset ($_SESSION['shop_order']['payment'])){
         $ePayment->setValues($_SESSION['shop_order']['payment']);
      }
      $formGoNext->addElement($ePayment);

      $eSend = new Form_Element_Submit('send', $this->tr('Pokračovat'));
      $formGoNext->addElement($eSend);
      if($formGoNext->isSend()){
         // kontrola způsobu platby
         if(in_array($formGoNext->payment->getValues(), $shippingDisallowedPayments[$formGoNext->shipping->getValues()])){
            $formGoNext->payment->setError($this->tr('Tuto kombinaci dopravy a platby nelze provést. Vyberte jiný způsob platby.'));
         }
      }
      $this->view()->paymentPrice = Shop_Tools::getPaymentOrShippingPrice($py[$formGoNext->payment->getValues()]['price'], $cart->getPrice());
      $this->view()->shippingPrice = Shop_Tools::getPaymentOrShippingPrice($sh[$formGoNext->shipping->getValues()]['price'], $cart->getPrice());
      $this->view()->freeShipAndPayFrom = VVE_SHOP_FREE_SHIPPING;

      if($formGoNext->isValid()){
         // uložení dopravy a platby
         $store = $this->getOrderStore();
         $pId = $formGoNext->payment->getValues();
         $sId = $formGoNext->shipping->getValues();
         $pickUpDate = null;
         if(isset ($formGoNext->pickupDate)){
            $pickUpDate = $formGoNext->pickupDate->getValues();
         }
         
         $_SESSION['shop_order']['payment'] = $pId;
         $_SESSION['shop_order']['shipping'] = $sId;
         $_SESSION['shop_order']['pickupdate'] = $pickUpDate;
         
         $this->link()->route('order')->reload();
      }
      
      $this->view()->formNext = $formGoNext;
      
   }

   public function cartUpdateController()
   {
      $action = $this->getRequestParam('action', false);
      if(!$action){
         return;
      }

      $cart = new Shop_Cart();
      switch ($action) {
         case "updateQty":
            $id = $this->getRequestParam('id', false);
            $qty = $this->getRequestParam('qty', false);
            if(!$id || !$qty || !is_numeric($qty)){
               throw new UnexpectedValueException($this->tr('Nebyly předány správné parametry'));
            }

            if(!$cart[$id]){
               throw new UnexpectedValueException($this->tr('Zadaná položka v košíku neexistuje'));
            }
            $cart->editQty($id, $qty);
            // return new price
            $this->view()->price = $cart->getItem($id)->getPrice();
            break;
         case "delete":
            $id = $this->getRequestParam('id', false);
            if(!$id){
               throw new UnexpectedValueException($this->tr('Nebyly předány správné parametry'));
            }
            if(isset($cart[$id])) {
               $cart->deleteItem($id);
            }
            $this->view()->deleted = true;
            break;
      }

   }

   public function orderController()
   {
      $cart = new Shop_Cart();
      if($cart->isEmpty() || !isset($_SESSION['shop_order']) ){
         $this->link()->route()->reload();
      }
      $this->orderControllerForm();
      $modelPayments = new Shop_Model_Payments();
      $modelShippings = new Shop_Model_Shippings();
      $payment = $modelPayments->record($_SESSION['shop_order']['payment']);
      $shipping = $modelShippings->record($_SESSION['shop_order']['shipping']);

      $this->view()->shippingPrice = Shop_Tools::getPaymentOrShippingPrice($shipping->{Shop_Model_Shippings::COLUMN_VALUE}, $cart->getPrice());
      $this->view()->shipping = $shipping;
      $this->view()->paymentPrice = Shop_Tools::getPaymentOrShippingPrice($payment->{Shop_Model_Payments::COLUMN_PRICE_ADD}, $cart->getPrice());
      $this->view()->payment = $payment;

      $this->view()->cart = $cart;
      $this->view()->priceTotal = $cart->getPrice()+$this->view()->shippingPrice+$this->view()->paymentPrice;
   }
   
   protected function orderControllerForm()
   {
      // načtení zón a způsobů doručení a plateb
      $modelZones = new Shop_Model_Zones();
      $zones = $modelZones->records();
      
      $formOrder = new Form('order_');
      $formOrder->setProtected(false);
      
      $fCustomerInfo = $formOrder->addGroup('customer', $this->tr('Informace o zákazníkovi'));
      
      $eCustomerName = new Form_Element_Text('customerName', $this->tr('Jméno'));
      $eCustomerName->addValidation(new Form_Validator_NotEmpty());
      $formOrder->addElement($eCustomerName, $fCustomerInfo);

      $eCustomerSName = new Form_Element_Text('customerSurname', $this->tr('Příjmení'));
      $eCustomerSName->addValidation(new Form_Validator_NotEmpty());
      $formOrder->addElement($eCustomerSName, $fCustomerInfo);

      $eCustomerEmail = new Form_Element_Text('customerEmail', $this->tr('E-mail'));
      $eCustomerEmail->addValidation(new Form_Validator_NotEmpty());
      $eCustomerEmail->addValidation(new Form_Validator_Email());
      $formOrder->addElement($eCustomerEmail, $fCustomerInfo);
      
      $eCustomerPhone = new Form_Element_Text('customerPhone', $this->tr('Telefon'));
      $phone = null;
      switch (Locales::getLang()) {
         case "cs":
            $phone = "+420";
            break;
      }

      $eCustomerPhone->setValues($phone);
      $eCustomerPhone->addValidation(new Form_Validator_NotEmpty());
      $eCustomerPhone->addValidation(new Form_Validator_Regexp(Form_Validator_Regexp::REGEXP_PHONE_CZSK, $this->tr('Telefon nebyl zadán ve správném formátu')));
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
      
      $eNewsletter = new Form_Element_Checkbox('newsletter', $this->tr('Novinky e-mailem'));
      $eNewsletter->setValues(true);
      $eNewsletter->setSubLabel($this->tr('Registrovat k se odběru novinek na zadaný e-mail'));
      $formOrder->addElement($eNewsletter);
      
//      $eCreateAccount = new Form_Element_Checkbox('createAcc', $this->tr('Vytvořit účet'));
//      $eCreateAccount->setSubLabel($this->tr('Vytvořit uživatelský účet ze zadaných údajů pro příští nákup'));
//      $formOrder->addElement($eCreateAccount);
//      
//      $eCreateAccountPass = new Form_Element_Text('createAccPassword', $this->tr('Heslo'));
//      $formOrder->addElement($eCreateAccountPass);
      
      $eSend = new Form_Element_SaveCancel('send', array($this->tr('Potvrdit objednávku'), $this->tr('Zpět do košíku')));
      $eSend->setCancelConfirm(false);
      $formOrder->addElement($eSend);
      
      // obnova dat pokud existují
      $this->restoreOrderInfo($formOrder);
      
      if($formOrder->isSend()){
         if($formOrder->send->getValues() == false){
            $this->link()->route()->reload();
         }
         
         if(isset($formOrder->createAcc) && $formOrder->createAcc->getValues() == true){
//            $eCreateAccountPass->addValidation(new Form_Validator_NotEmpty());
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
         try {
            $cart = new Shop_Cart();
            $cart->loadItems();
         
            /*
             * Kontroly stavu zboží a odečty položek pokud je zakázáno nakupování vyprodaného zboží.
             * Pokud nelze odečíst, nelze vytvořit objednávku a redirect s chybou na košík.
             * Rovnou odstranit objednávku
             * V chybě: které položka a kolik zbývá
             */

            $mProducts = new Shop_Model_Product();
            $mProductsComb = new Shop_Model_Product_Combinations();
//            Model_ORM::lockModels(array($mProducts, Model_ORM::LOCK_WRITE), array($mProductsComb, Model_ORM::LOCK_READ));
            // kontrola dosupnosti zboží pokud není povolen nákup zboží které není skladem
            if(!VVE_SHOP_ALLOW_BUY_NOT_IN_STOCK){
               foreach ($cart->getItems() as $item) {
                  $product = $mProducts
                     ->columns(array(
                        '*',
                        Shop_Model_Product::COLUMN_QUANTITY =>
                           'COALESCE('.$mProductsComb->getTableShortName().'.'.Shop_Model_Product_Combinations::COLUMN_QTY
                              .', '.$mProducts->getTableShortName().'.'.Shop_Model_Product::COLUMN_QUANTITY.')',
                     ))
                     ->join(
                        Shop_Model_Product::COLUMN_ID, array( $mProductsComb->getTableShortName() => 'Shop_Model_Product_Combinations'),
                        Shop_Model_Product_Combinations::COLUMN_ID_PRODUCT)


                     ->where(    // tady je chyba pokud není kombinace (idc = 0)
                        Shop_Model_Product::COLUMN_ID." = :idp AND "
                           ."(".Shop_Model_Product_Combinations::COLUMN_ID." = :idc OR ".Shop_Model_Product_Combinations::COLUMN_ID." IS NULL)",
                        array('idp' => $item->getProductId(), 'idc' => $item->getCombinationId()))
                     ->record();
                  $mProducts->reset();// reset variables ob product

                  if(!$product->{Shop_Model_Product::COLUMN_STOCK}){ // skip products without stock
                     continue;
                  }

                  if(!$product){
                     throw new RangeException(
                        sprintf($this->tr('Omlouváme se, ale zboží "%s" bylo stáhnuto z prodeje. Upravte prosím položky v košíku.'),
                           $product->{Shop_Model_Product::COLUMN_NAME}));
                  }

                  /* @var $item Shop_Cart_Item */
                  $prQty = $product->{Shop_Model_Product::COLUMN_QUANTITY};
                  // zboží už je vyprodané
                  if ($prQty <= 0) {
                     throw new RangeException(
                        sprintf($this->tr('Omlouváme se, ale zboží "%s" je již vyprodané. Upravte prosím položky v košíku.'),
                           $product->{Shop_Model_Product::COLUMN_NAME}.($item->getNote() != null ? " - ".$item->getNote() : null) ));
                  }
                  // zboží není v potřebném množství
                  else if ( $prQty < $item->getQty()) {
                     throw new RangeException(
                        sprintf($this->tr("Omlouváme se, ale zboží %s není již v požadovaném množství.
                           Upravte prosím položky v košíku. Aktuálně je dostupné: %s %s."),
                           $product->{Shop_Model_Product::COLUMN_NAME}.($item->getNote() != null ? " - ".$item->getNote() : null),
                           $prQty, $product->{Shop_Model_Product::COLUMN_UNIT} ));
                  }
               }
            }
            // vytvoření zákazníka
            $customer = $this->createCustomer($formOrder);
            // vytvoření objednávky
            $order = $this->createOrder($formOrder, $cart, $customer);
            $orderID = $order->getPK();

            $mCombination = new Shop_Model_Product_Combinations();
//            $mCombination->lock(Model_ORM::LOCK_WRITE);
            Model_ORM::lockModels(array($mProducts, Model_ORM::LOCK_WRITE), array($mCombination, Model_ORM::LOCK_WRITE));
            // samotný update zboží a zařazování položek do objednávky
            foreach ($cart->getItems() as $id => $item) {

               if($item->getCombinationId() == 0){
                  // update product combination
                  $mProducts->where(Shop_Model_Product::COLUMN_ID." = :idp", array('idp' => $item->getProductId()))
                  ->update(
                     array( Shop_Model_Product::COLUMN_QUANTITY => array(
                        'stmt' => Shop_Model_Product::COLUMN_QUANTITY." - :qty",
                        'values' => array('qty' => (int)$item->getQty())
                     )));
               } else {
                  // update product
                  $mCombination->where(Shop_Model_Product_Combinations::COLUMN_ID." = :idc", array('idc' => $item->getCombinationId()))
                     ->update(
                     array( Shop_Model_Product_Combinations::COLUMN_QTY => array(
                        'stmt' => Shop_Model_Product_Combinations::COLUMN_QTY." - :qty",
                        'values' => array('qty' => (int)$item->getQty())
                     )));
               }

               // add order item
               $this->createOrderItem($orderID, $item);
            }

            // unloc models
            Model_ORM::unLockTables();

            // remove from cart
            $cart->clear();

            // základní stav objednávky
//            $order->changeState(CUBE_CMS_SHOP_ORDER_DEFAULT_STATUS);
            
            // stav objednávky pokud má platba 
             // základní stav objednávky
            $order->changeState(CUBE_CMS_SHOP_ORDER_DEFAULT_STATUS);

            $modelPayments = new Shop_Model_Payments();
            $payment = $modelPayments->record($order->{Shop_Model_Orders::COLUMN_PAYMENT_ID});
            if($payment->{Shop_Model_Payments::COLUMN_ID_STATE} != null){
               $order->changeState($payment->{Shop_Model_Payments::COLUMN_ID_STATE});
            }
            
            // nasatvení stavu objendávky, podle zadané platební metody
//            $this->createOrderStatus($orderID);

            // send mails
            $this->generateMails($order, $cart);

         } catch (RangeException $exc) {
//            $this->errMsg()->addMessage($exc->getMessage()); // del
            $this->errMsg()->addMessage($exc->getMessage(), true);
            Model_ORM::unLockTables();
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            $this->log($e->getTraceAsString());
            new CoreErrors($e);
            Model_ORM::unLockTables();
            throw new CoreException($this->tr('Omlouváme se, ale vyskytla se chyba při vytváření objednávky a nepodařilo se zařadit všechny produkty.
                  Prosím kontaktujte nás pro její opravu a dokončení!'));
         }

         $this->link()->route('orderComplete')->reload();
      }

      $this->view()->formOrder = $formOrder;
   }

   private function createCustomer(Form $formOrder)
   {
      $modelCustomer = new Shop_Model_Customers();
      $modelUsers = new Model_Users();
//      $email = $formOrder->customerEmail->getValues();

      $user = $modelUsers->where(Model_Users::COLUMN_MAIL." = :mail", array('mail' => $formOrder->customerEmail->getValues()))->record();
      if(!$user){
         // nový uživatel
         $user = $modelUsers->newRecord();
         $user->{Model_Users::COLUMN_ID_GROUP} = VVE_SHOP_CUSTOMERS_GROUP_ID != 0 ? VVE_SHOP_CUSTOMERS_GROUP_ID : VVE_DEFAULT_ID_GROUP;
         $user->{Model_Users::COLUMN_USERNAME} = vve_cr_safe_file_name($formOrder->customerSurname->getValues());
         $user->{Model_Users::COLUMN_MAIL} = $formOrder->customerEmail->getValues();
         $user->{Model_Users::COLUMN_NAME} = $formOrder->customerName->getValues();
         $user->{Model_Users::COLUMN_SURNAME} = $formOrder->customerSurname->getValues();
         $user->{Model_Users::COLUMN_PASSWORD} = Auth::cryptPassword(Auth::generatePassword());
         $user->save();
      }

      $customer = $modelCustomer->where(Shop_Model_Customers::COLUMN_ID_USER." = :idu", array('idu' => $user->getPK()))->record();
      // pokud neexituje zákazník se zadaným emailem, vytvoří se
      if(!$customer){
         $customer = $modelCustomer->newRecord();
         $customer->{Shop_Model_Customers::COLUMN_ID_USER} = $user->getPK();
         $customer->{Shop_Model_Customers::COLUMN_ID_GROUP} = VVE_SHOP_CUSTOMERS_DEFAULT_GROUP_ID;
         $customer->{Shop_Model_Customers::COLUMN_PHONE} = $formOrder->customerPhone->getValues();

         $customer->{Shop_Model_Customers::COLUMN_COMPANY} = $formOrder->paymentCompanyName->getValues();
         $customer->{Shop_Model_Customers::COLUMN_STREET} = $formOrder->paymentStreet->getValues();
         $customer->{Shop_Model_Customers::COLUMN_CITY} = $formOrder->paymentCity->getValues();
         $customer->{Shop_Model_Customers::COLUMN_PSC} = $formOrder->paymentPostCode->getValues();
         $customer->{Shop_Model_Customers::COLUMN_ID_COUNTRY} = $formOrder->paymentCountry->getValues();
         $customer->{Shop_Model_Customers::COLUMN_IC} = $formOrder->paymentCompanyIC->getValues();
         $customer->{Shop_Model_Customers::COLUMN_DIC} = $formOrder->paymentCompanyDIC->getValues();
         $customer->{Shop_Model_Customers::COLUMN_DELIVERY_NAME} = $formOrder->deliveryName->getValues();
         $customer->{Shop_Model_Customers::COLUMN_DELIVERY_STREET} = $formOrder->deliveryStreet->getValues();
         $customer->{Shop_Model_Customers::COLUMN_DELIVERY_CITY} = $formOrder->deliveryCity->getValues();
         $customer->{Shop_Model_Customers::COLUMN_DELIVERY_PSC} = $formOrder->deliveryPostCode->getValues();
         $customer->{Shop_Model_Customers::COLUMN_ID_DELIVERY_COUNTRY} = $formOrder->deliveryCountry->getValues();
         // customer info

         $customer->save();
      }
      // newsletter
      if($formOrder->newsletter->getValues() && VVE_SHOP_NEWSLETTER_GROUP_ID != 0){
         $modelMails = new MailsAddressBook_Model_Addressbook();
         $newRec = $modelMails->newRecord();
         $newRec->{MailsAddressBook_Model_Addressbook::COLUMN_NAME} = $formOrder->customerName->getValues();
         $newRec->{MailsAddressBook_Model_Addressbook::COLUMN_SURNAME} = $formOrder->customerSurname->getValues();
         $newRec->{MailsAddressBook_Model_Addressbook::COLUMN_MAIL} = $formOrder->customerEmail->getValues();
         $newRec->{MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP} = VVE_SHOP_NEWSLETTER_GROUP_ID;
         $newRec->save();
      }

      return $customer;
   }

   private function createOrder(Form $formOrder, Shop_Cart $cart, Model_ORM_Record $customer)
   {
      $modelOrder = new Shop_Model_Orders();
      $modelPayments = new Shop_Model_Payments();
      $modelShippings = new Shop_Model_Shippings();
//      Model_ORM::lockModels( array($modelOrder, Model_ORM::LOCK_WRITE), array($modelPayments, Model_ORM::LOCK_READ), array($modelShippings, Model_ORM::LOCK_READ) );

      $order = $modelOrder->newRecord();

      $order->{Shop_Model_Orders::COLUMN_CUSTOMER_NAME} = $formOrder->customerName->getValues()." ".$formOrder->customerSurname->getValues();
      $order->{Shop_Model_Orders::COLUMN_ID_CUSTOMER} = $customer->getPK();
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
         $order->{Shop_Model_Orders::COLUMN_DELIVERY_NAME} = $formOrder->customerName->getValues()." ".$formOrder->customerSurname->getValues();
         $order->{Shop_Model_Orders::COLUMN_DELIVERY_STREET} = $formOrder->paymentStreet->getValues();
         $order->{Shop_Model_Orders::COLUMN_DELIVERY_CITY} = $formOrder->paymentCity->getValues();
         $order->{Shop_Model_Orders::COLUMN_DELIVERY_POST_CODE} = $formOrder->paymentPostCode->getValues();
         $order->{Shop_Model_Orders::COLUMN_DELIVERY_COUNTRY} =
            array_search($formOrder->paymentCountry->getValues(), $formOrder->paymentCountry->getOptions());
      }

      $productsPrice = $cart->getPrice();

      $payment = $modelPayments->record($_SESSION['shop_order']['payment']);
      $shipping = $modelShippings->record($_SESSION['shop_order']['shipping']);
      $pickUpDate = $_SESSION['shop_order']['pickupdate'];

      // metoda platby
      $order->{Shop_Model_Orders::COLUMN_PAYMENT_ID} = $payment->{Shop_Model_Payments::COLUMN_ID};
      $order->{Shop_Model_Orders::COLUMN_PAYMENT_METHOD} = (string)$payment->{Shop_Model_Payments::COLUMN_NAME};
      $order->{Shop_Model_Orders::COLUMN_SHIPPING_ID} = $shipping->{Shop_Model_Shippings::COLUMN_ID};
      $order->{Shop_Model_Orders::COLUMN_SHIPPING_METHOD} = (string)$shipping->{Shop_Model_Shippings::COLUMN_NAME};
      $order->{Shop_Model_Orders::COLUMN_PICKUP_DATE} = $pickUpDate;

      if($productsPrice >= VVE_SHOP_FREE_SHIPPING && VVE_SHOP_FREE_SHIPPING != -1){ // doprava a platba zdarma
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
      $order->save();
      
      $_SESSION['shop_order']['orderId'] = $order->getPK();
      $_SESSION['shop_order']['paymentClass'] = $payment->{Shop_Model_Payments::COLUMN_CLASS};
      return $order;
   }

   private function createOrderItem($orderId, Shop_Cart_Item $item)
   {
      $modelOrderItems = new Shop_Model_OrderItems();
      $modelOrderItems->lock(Model_ORM::LOCK_WRITE);
      $orderItem = $modelOrderItems->newRecord();

      $orderItem->{Shop_Model_OrderItems::COLUMN_ID_ORDER} = $orderId;
      $orderItem->{Shop_Model_OrderItems::COLUMN_NAME} = (string)$item->getName();
      $orderItem->{Shop_Model_OrderItems::COLUMN_NOTE} = $item->getNote();
      $orderItem->{Shop_Model_OrderItems::COLUMN_PRICE} = $item->getPrice(true, false);
      $orderItem->{Shop_Model_OrderItems::COLUMN_QTY} = $item->getQty();
      $orderItem->{Shop_Model_OrderItems::COLUMN_TAX} = $item->getTax();
      $orderItem->{Shop_Model_OrderItems::COLUMN_UNIT} = $item->getUnit();
      $orderItem->{Shop_Model_OrderItems::COLUMN_CODE} = $item->getCode();
      $orderItem->{Shop_Model_OrderItems::COLUMN_ID_PRODUCT} = $item->getProductId();

      $modelOrderItems->save($orderItem);
   }

   private function &getOrderStore()
   {
      if(!isset ($_SESSION['shop_order']) || !is_array($_SESSION['shop_order'])){
         $_SESSION['shop_order'] = array();
      }
      return $_SESSION['shop_order'];
   }

   private function generateMails($order, $cart)
   {
      // tady pouze admin mail. uživatel se odesílá při změně stavu
      $state = Shop_Model_OrdersStates::getRecord(CUBE_CMS_SHOP_ORDER_DEFAULT_STATUS);
      
      if($state->{Shop_Model_OrdersStates::COLUMN_ID_TEMPLATE} != 0 && CUBE_CMS_SHOP_ORDER_MAIL != null){
         $cnt = Templates_Model::getTemplate($state->{Shop_Model_OrdersStates::COLUMN_ID_TEMPLATE}, Locales::getDefaultLang());
         
         $mailCnt = Shop_Tools::getMailTplContent($cnt->{Templates_Model::COLUMN_CONTENT}, $order);
         
         $emailAdmin = new Email(true);
         $emailAdmin->addAddress(CUBE_CMS_SHOP_ORDER_MAIL);
         $emailAdmin->setFrom(CUBE_CMS_NOREPLAY_MAIL);
         $emailAdmin->setSubject(sprintf('[NOVÁ OBJEDNÁVKA] číslo %s', Shop_Tools::getFormatOrderNumber($order->{Shop_Model_Orders::COLUMN_ID})));
         $emailAdmin->setContent(Email::getBaseHtmlMail($mailCnt));
         $emailAdmin->send();
      }
      return;
      
      
      $userMailText = $adminMailText =
         "Objednávka z {STRANKY}<br /><br />
         {INFO}<br /><br />
         <h2>Zboží:</h2> {ZBOZI}<br /><br />
         <h2>Dodací adresa:</h2>{ADRESA_DODACI}<br /><br />
         <h2>Fakturační adresa:</h2>{ADRESA_FAKTURACNI}<br />";

      if (is_file($this->module()->getDataDir().'mail_tpl_user_'.Locales::getLang().'.html')) {
         $userMailText = file_get_contents($this->module()->getDataDir().'mail_tpl_user_'.Locales::getLang().'.html');
      }
      
      if (is_file($this->module()->getDataDir().'mail_tpl_admin.html')) {
         $adminMailText = file_get_contents($this->module()->getDataDir().'mail_tpl_admin.html');
      }
      
      /* info o objednávce */
      $orderInfo = "";
      $orderInfo .= 'Číslo objednávky / variabilní symbol: <strong>'.$order->{Shop_Model_Orders::COLUMN_ID}."</strong><br/>";
      $orderInfo .= 'Datum objednávky: '.  vve_date("%x");
      $orderInfo .= "";

      /* Zboží */
      $orderItems = '<table class="table-data">';
      $orderItems .= '<tr>'
         .'<th style="text-align: left;">'.$this->tr('Zboží').'</th>'
         .'<th style="text-align: left;">'.$this->tr('Množství').'</th>'
         .'<th style="text-align: left;">'.$this->tr('Cena').'</th>'
      ;
      $orderItems .= '</tr>';
      foreach ($cart as $item) {
         // 1 Ks Náhrdelník (zlatý) = 500 Kč
         $itemStr = $item->getName();
         if($item->getNote() != null){
            $itemStr .= '<br />('.$item->getNote().')';
         }
         $orderItems .= '<tr><td>'.$itemStr.'</td>'
            .'<td>'.$item->getQty().' '.$item->getUnit().'</td>'
            .'<td style="text-align: right;">'.Shop_Tools::getFormatedPrice($item->getPrice()).'</td></tr>';
      }
      $orderItems .= '<tr><td colspan="3"></td></tr>';
      $orderItems .= '<tr><td colspan="2">Mezisoučet:</td>'
         .'<td style="text-align: right;"><strong>'.Shop_Tools::getFormatedPrice($cart->getPrice())."</strong></td></tr>";
      $orderItems .= '<tr><td colspan="3"></td></tr>';
      // info k dopravě
      $orderItems .= '<tr><td colspan="2">Doprva: '.$order->{Shop_Model_Orders::COLUMN_SHIPPING_METHOD}.'</td>'
         .'<td style="text-align: right;"><strong>'.Shop_Tools::getFormatedPrice($order->{Shop_Model_Orders::COLUMN_SHIPPING_PRICE})."</strong></td></tr>";

      $modelS = new Shop_Model_Shippings;
      $ship = $modelS->record($order->{Shop_Model_Orders::COLUMN_SHIPPING_ID});
      if($ship != false){
         $orderItems .= preg_replace(array('/\n\n*/', '/\s{2,}/'),array("\n", " "), strip_tags($ship->{Shop_Model_Shippings::COLUMN_TEXT}))."\n";
      }
      // info k platbě
      $modelP = new Shop_Model_Payments();
      $payment = $modelP->record($order->{Shop_Model_Orders::COLUMN_PAYMENT_ID});
      $orderItems .= '<tr><td colspan="2">Platba: '.$order->{Shop_Model_Orders::COLUMN_PAYMENT_METHOD}.'</td>'
         .'<td style="text-align: right;"><strong>'.Shop_Tools::getFormatedPrice($order->{Shop_Model_Orders::COLUMN_PAYMENT_PRICE})."</strong></td></tr>";

      if($payment != false){
         $orderItems .= '<tr><td colspan="2"><em>'
            .preg_replace(array('/\n\n*/', '/\s{2,}/'),array("\n", " "), strip_tags($payment->{Shop_Model_Payments::COLUMN_TEXT})).'</em></td>'
            .'<td style="text-align: right;"></td></tr>';
      }
      // kompletní cena
      $orderItems .= '<tr><td colspan="2"><strong>Cena celkem:</strong></td>'.
         '<td><strong>'.Shop_Tools::getFormatedPrice(
            $cart->getPrice()+$order->{Shop_Model_Orders::COLUMN_PAYMENT_PRICE}+$order->{Shop_Model_Orders::COLUMN_SHIPPING_PRICE})."</strong></td></tr>";
      $orderItems .= '</table>';

      /* Adresa fakturační */

      $addressPayment = '';
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY} != null ? $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY}."<br />" : null;

      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_NAME}."<br />";
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_STREET}."<br />";
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_CITY}.' '.$order->{Shop_Model_Orders::COLUMN_CUSTOMER_POST_CODE}."<br />";
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COUNTRY}."<br />";
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_EMAIL}."<br />";
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_PHONE}."<br />";

      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_DIC} != null ?
         'DIČ: '.$order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_DIC}."<br />" : null;
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_IC} != null ?
         'IČ: '.$order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_IC}."<br /><br />" : null;

      /* adresa dodací */
      $addressShip = "";
      $addressShip .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_NAME}."<br />";
      $addressShip .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_STREET}."<br />";
      $addressShip .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_CITY}.' '.$order->{Shop_Model_Orders::COLUMN_DELIVERY_POST_CODE}."<br />";
      $addressShip .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_COUNTRY}."<br />";
      $addressShip .= '';
       
      /* poznamka */
      $note = null;
      if($order->{Shop_Model_Orders::COLUMN_NOTE} != null){
         $note = "".$order->{Shop_Model_Orders::COLUMN_NOTE}."";
      }
      
      $searchArr = array(
         '{STRANKY}',
         '{DATUM}',
         '{INFO}',
         '{ZBOZI}',
         '{ADRESA_OBCHOD}',
         '{ADRESA_DODACI}',
         '{ADRESA_FAKTURACNI}',
         '{POZNAMKA}',
         '{IP}',
      );
      $replaceArr = array(
         '<a href="'.Url_Link::getMainWebDir().'" title="'.VVE_WEB_NAME.'">'.VVE_WEB_NAME.'</a>',
         vve_date('%X %x'),
         $orderInfo,
         $orderItems,
         VVE_SHOP_STORE_ADDRESS,
         $addressShip,
         $addressPayment,
         $note,
         $_SERVER['REMOTE_ADDR'],
      );
      
      
      $userMailText = str_replace($searchArr, $replaceArr, $userMailText);
      $adminMailText = str_replace($searchArr, $replaceArr, $adminMailText);
      
      $emailUser = new Email(true);
//      $emailUser->addAddress($order->{Shop_Model_Orders::COLUMN_CUSTOMER_EMAIL});
//      $emailUser->setFrom(VVE_NOREPLAY_MAIL);
//      $emailUser->setSubject(sprintf('[NOVÁ OBJEDNÁVKA] číslo %s', $order->{Shop_Model_Orders::COLUMN_ID}));
//      $emailUser->setContent(Email::getBaseHtmlMail($userMailText));
//      $emailUser->send();

      if(VVE_SHOP_ORDER_MAIL != null){
//         $emailAdmin = new Email(true);
//         $emailAdmin->addAddress(VVE_SHOP_ORDER_MAIL);
//         $emailAdmin->setFrom(VVE_NOREPLAY_MAIL);
//         $emailAdmin->setSubject(sprintf('[NOVÁ OBJEDNÁVKA] číslo %s', $order->{Shop_Model_Orders::COLUMN_ID}));
//         $emailAdmin->setContent(Email::getBaseHtmlMail($adminMailText));
//         $emailAdmin->send();
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
