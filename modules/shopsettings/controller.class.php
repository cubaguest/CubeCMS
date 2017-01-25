<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */
class ShopSettings_Controller extends Controller {

   private $model = null;

   protected function init()
   {
      $this->category()->getModule()->setDataDir('shop');
   }

   public function mainController()
   {
      //		Kontrola práv
      $this->checkReadableRights();

      $form = new Form('base', true);

      $grpInfo = $form->addGroup('shop', $this->tr('Informace o obchod'));
      $grpStock = $form->addGroup('shop_stock', $this->tr('Nastavení skladu'));

      $eStoreName = new Form_Element_Text('name', $this->tr('Název obchodu'));
      $eStoreName->setValues(VVE_WEB_NAME);
      $form->addElement($eStoreName, $grpInfo);

      $eStoreInfo = new Form_Element_TextArea('info', $this->tr('Adresa obchodu'));
      $eStoreInfo->setValues(VVE_SHOP_STORE_ADDRESS);
      $form->addElement($eStoreInfo, $grpInfo);

      $elemAllowBuyNotInStock = new Form_Element_Checkbox('buyNotInStock', $this->tr('Řízení skladu'));
      $elemAllowBuyNotInStock->setSubLabel($this->tr('Při zapnutí omezí nákup produktů pouze na produkty které jsou skladem. Zboží, které není skladem nelze zakoupit.'));
      $elemAllowBuyNotInStock->setValues(!VVE_SHOP_ALLOW_BUY_NOT_IN_STOCK);
      $form->addElement($elemAllowBuyNotInStock, $grpStock);

      $eSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $form->addElement($eSave);

      if ($form->isValid()) {
         $this->storeSystemCfg('VVE_WEB_NAME', $form->name->getValues());
         $this->storeSystemCfg('VVE_SHOP_STORE_ADDRESS', $form->info->getValues());
         $this->storeSystemCfg('VVE_SHOP_ALLOW_BUY_NOT_IN_STOCK', $form->buyNotInStock->getValues() ? "false" : "true");

         $this->infoMsg()->addMessage($this->tr('Nastavení bylo uloženo'));
         $this->link()->reload();
      }
      $this->view()->form = $form;
   }

   public function currencyAndTaxesController()
   {
      //		Kontrola práv
      $this->checkReadableRights();

      $form = new Form('currency_', true);

      $grpCurrencies = $form->addGroup('currencies', $this->tr('Měny'));

      $eCurrencyName = new Form_Element_Text('name', $this->tr('Název měny'));
      $eCurrencyName->setValues(VVE_SHOP_CURRENCY_NAME);
      $eCurrencyName->setSubLabel($this->tr('Například Kč'));
      $form->addElement($eCurrencyName, $grpCurrencies);

      $eCurrencyCode = new Form_Element_Text('code', $this->tr('Kód měny'));
      $eCurrencyCode->setValues(VVE_SHOP_CURRENCY_CODE);
      $eCurrencyCode->setSubLabel($this->tr('Například CZK nebo EUR'));
      $form->addElement($eCurrencyCode, $grpCurrencies);

      $eSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $form->addElement($eSave);

      if ($form->isValid()) {
         $this->storeSystemCfg('VVE_SHOP_CURRENCY_NAME', $form->name->getValues());
         $this->storeSystemCfg('VVE_SHOP_CURRENCY_CODE', $form->code->getValues());

         $this->infoMsg()->addMessage($this->tr('Nastavení bylo uloženo'));
//         $this->link()->reload();
      }
      $this->view()->form = $form;
   }

   public function taxesListController()
   {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Shop_Model_Tax::COLUMN_NAME);
      // search
      $model = new Shop_Model_Tax();

      $order = Model_ORM::ORDER_ASC;
      if ($jqGrid->request()->order == 'desc') {
         $order = Model_ORM::ORDER_DESC;
      }

      switch ($jqGrid->request()->orderField) {
         case Shop_Model_Tax::COLUMN_VALUE:
            $model->order(array(Shop_Model_Tax::COLUMN_VALUE => $order));
            break;
         case Shop_Model_Tax::COLUMN_NAME:
         default:
            $model->order(array(Shop_Model_Tax::COLUMN_NAME => $order));
            break;
      }
      $taxes = $model->records();

      // out
      foreach ($taxes as $tax) {
         array_push($jqGrid->respond()->rows, array('id' => $tax->{Shop_Model_Tax::COLUMN_ID},
             Shop_Model_Tax::COLUMN_NAME => $tax->{Shop_Model_Tax::COLUMN_NAME},
             Shop_Model_Tax::COLUMN_VALUE => $tax->{Shop_Model_Tax::COLUMN_VALUE},
         ));
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function editTaxController()
   {
      $this->checkWritebleRights();
      $model = new Shop_Model_Tax();
      $this->view()->allOk = false;
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // kontrola položek
            if ($jqGridReq->{Shop_Model_Tax::COLUMN_NAME} == null || $jqGridReq->{Shop_Model_Tax::COLUMN_VALUE} === null) {
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }
            // validace hodnoty
            if (!is_numeric($jqGridReq->{Shop_Model_Tax::COLUMN_VALUE})) {
               $this->errMsg()->addMessage($this->tr('V hodnotě nebylo zadáno číslo'));
               return;
            }

            $record = $model->record($jqGridReq->id);
            $record->mapArray($jqGridReq);
            $model->save($record);

            $this->infoMsg()->addMessage($this->tr('Daň byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               if ($id != 1) {
                  $model->delete($id);
               }
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané daně byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }

   public function shipAndPayController()
   {
      //		Kontrola práv
      $this->checkReadableRights();

      $form = new Form('ship_and_pay');

      $grpShipping = $form->addGroup('payment', $this->tr('Platby'));

      $eFreeShipping = new Form_Element_Text('freeShipping', $this->tr('Doprava zdarma od'));
      $eFreeShipping->setValues(VVE_SHOP_FREE_SHIPPING);
      $eFreeShipping->setSubLabel($this->tr('Například od 2000 Kč zadat 2000. -1 pro vypnutí.'));
      $form->addElement($eFreeShipping, $grpShipping);

      $eSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $form->addElement($eSave);

      if ($form->isValid()) {
         $this->storeSystemCfg('VVE_SHOP_FREE_SHIPPING', $form->freeShipping->getValues());
         $this->infoMsg()->addMessage($this->tr('Nastavení bylo uloženo'));
         $this->link()->reload();
      }
      $this->view()->form = $form;
      $emptyZone = Shop_Model_Zones::getNewRecord();
      $emptyZone->{Shop_Model_Zones::COLUMN_NAME} = $this->tr('--- bez omezení ---');
      $emptyZone->{Shop_Model_Zones::COLUMN_ID} = 0;
      $this->view()->zones = array_merge(array($emptyZone), Shop_Model_Zones::getAllRecords());
      
      $empty = Shop_Model_OrdersStates::getNewRecord();
      $empty->{Shop_Model_OrdersStates::COLUMN_NAME} = $this->tr('-- není nastaveno --');
      $empty->{Shop_Model_OrdersStates::COLUMN_ID} = 0;
      $this->view()->orderStates = array_merge(array($empty), Shop_Model_OrdersStates::getActiveStates());
   }

   public function paymentsListController()
   {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Shop_Model_Payments::COLUMN_NAME);
      // search
      $model = new Shop_Model_Payments();
      $model->joinFK(Shop_Model_Payments::COLUMN_ID_STATE);

      $order = Model_ORM::ORDER_ASC;
      if ($jqGrid->request()->order == 'desc') {
         $order = Model_ORM::ORDER_DESC;
      }

      switch ($jqGrid->request()->orderField) {
         case Shop_Model_Payments::COLUMN_PRICE_ADD:
            $model->order(array(Shop_Model_Payments::COLUMN_PRICE_ADD => $order));
            break;
         case Shop_Model_Payments::COLUMN_ID:
            $model->order(array(Shop_Model_Payments::COLUMN_ID => $order));
            break;
         case Shop_Model_Payments::COLUMN_TEXT:
            $model->order(array(Shop_Model_Payments::COLUMN_TEXT => $order));
            break;
         case Shop_Model_OrdersStates::COLUMN_NAME:
            $model->order(array(Shop_Model_OrdersStates::COLUMN_NAME => $order));
            break;
         case Shop_Model_Payments::COLUMN_NAME:
         default:
            $model->order(array(Shop_Model_Payments::COLUMN_NAME => $order));
            break;
      }
      $jqGrid->respond()->setRecords($model->count());
      $payments = $model->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)->records();

      // out
      foreach ($payments as $payment) {
         $name = null;
         foreach ($payment->{Shop_Model_Payments::COLUMN_NAME} as $lang => $val) {
            if ($val != null) {
               $name .= '[' . $lang . ']' . $val . '[/' . $lang . ']' . PHP_EOL;
            } else {
               $name .= '[' . $lang . ']' . $payment->{Shop_Model_Payments::COLUMN_NAME}[Locales::getDefaultLang()] . '[/' . $lang . ']' . PHP_EOL;
            }
         }
         $text = null;
         foreach ($payment->{Shop_Model_Payments::COLUMN_TEXT} as $lang => $val) {
            if ($val != null) {
               $text .= '[' . $lang . ']' . $val . '[/' . $lang . ']' . PHP_EOL;
            } else {
               $text .= '[' . $lang . ']' . $payment->{Shop_Model_Payments::COLUMN_TEXT}[Locales::getDefaultLang()] . '[/' . $lang . ']' . PHP_EOL;
            }
         }

         array_push($jqGrid->respond()->rows, array(
             'id' => $payment->{Shop_Model_Payments::COLUMN_ID},
             Shop_Model_Payments::COLUMN_ID => $payment->{Shop_Model_Payments::COLUMN_ID},
             Shop_Model_Payments::COLUMN_NAME => $name,
             Shop_Model_Payments::COLUMN_PRICE_ADD => $payment->{Shop_Model_Payments::COLUMN_PRICE_ADD},
             Shop_Model_Payments::COLUMN_TEXT => $text,
             Shop_Model_Payments::COLUMN_ID_STATE => $payment->{Shop_Model_Payments::COLUMN_ID_STATE},
             Shop_Model_OrdersStates::COLUMN_NAME=> (string)$payment->{Shop_Model_OrdersStates::COLUMN_NAME} != null 
               ? (string)$payment->{Shop_Model_OrdersStates::COLUMN_NAME}
               : $this->tr('není nastaveno'),
         ));
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function editPaymentController()
   {
      $this->checkWritebleRights();
      $model = new Shop_Model_Payments();
      $this->view()->allOk = false;
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // kontrola položek
            if ($jqGridReq->{Shop_Model_Payments::COLUMN_NAME} == null) {
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }

            $record = $model->record($jqGridReq->id);

            $matches = array();
            if (preg_match_all('/\[([a-z]{2})\]([^\[]+)\[\/[a-z]{2}\]/', $jqGridReq->{Shop_Model_Payments::COLUMN_NAME}, $matches) != 0) {
               foreach ($matches[1] as $key => $lang) {
                  $record->{Shop_Model_Payments::COLUMN_NAME}[$lang] = $matches[2][$key];
               }
            } else {
               $this->errMsg()->addMessage($this->tr('Název není zadán ve správném formátu.'));
               return;
            }

            $matches = array();
            if (preg_match_all('/\[([a-z]{2})\]([^\[]+)\[\/[a-z]{2}\]/', $jqGridReq->{Shop_Model_Payments::COLUMN_TEXT}, $matches) != 0) {
               foreach ($matches[1] as $key => $lang) {
                  $record->{Shop_Model_Payments::COLUMN_TEXT}[$lang] = $matches[2][$key];
               }
            }

            if ($jqGridReq->{Shop_Model_Payments::COLUMN_PRICE_ADD} == null) {
               $jqGridReq->{Shop_Model_Payments::COLUMN_PRICE_ADD} = 0;
            }
            $record->{Shop_Model_Payments::COLUMN_PRICE_ADD} = $jqGridReq->{Shop_Model_Payments::COLUMN_PRICE_ADD};
            $record->{Shop_Model_Payments::COLUMN_ID_STATE} = $jqGridReq->{Shop_Model_OrdersStates::COLUMN_NAME};
            $this->view()->r = $record;
            $model->save($record);

            $this->infoMsg()->addMessage($this->tr('Platba byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               if ($id != 1) {
                  $model->delete($id);
               }
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané platby byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }

   public function shippingsListController()
   {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Shop_Model_Shippings::COLUMN_NAME);
      // search
      $model = new Shop_Model_Shippings();

      $order = Model_ORM::ORDER_ASC;
      if ($jqGrid->request()->order == 'desc') {
         $order = Model_ORM::ORDER_DESC;
      }

      switch ($jqGrid->request()->orderField) {
         case Shop_Model_Shippings::COLUMN_VALUE:
            $model->order(array(Shop_Model_Shippings::COLUMN_VALUE => $order));
            break;
         case Shop_Model_Shippings::COLUMN_ID:
            $model->order(array(Shop_Model_Payments::COLUMN_ID => $order));
            break;
         case Shop_Model_Shippings::COLUMN_TEXT:
            $model->order(array(Shop_Model_Shippings::COLUMN_TEXT => $order));
            break;
         case Shop_Model_Shippings::COLUMN_NAME:
         default:
            $model->order(array(Shop_Model_Shippings::COLUMN_NAME => $order));
            break;
      }

      $model->joinFK(Shop_Model_Shippings::COLUMN_ID_ZONE);
      
      $jqGrid->respond()->setRecords($model->count());
      $records = $model->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)->records();

      // out
      foreach ($records as $record) {
         $name = null;
         foreach ($record->{Shop_Model_Shippings::COLUMN_NAME} as $lang => $val) {
            if ($val != null) {
               $name .= '[' . $lang . ']' . $val . '[/' . $lang . ']' . PHP_EOL;
            } else {
               $name .= '[' . $lang . ']' . $record->{Shop_Model_Shippings::COLUMN_NAME}[Locales::getDefaultLang()] . '[/' . $lang . ']' . PHP_EOL;
            }
         }
         $text = null;
         foreach ($record->{Shop_Model_Shippings::COLUMN_TEXT} as $lang => $val) {
            if ($val != null) {
               $text .= '[' . $lang . ']' . $val . '[/' . $lang . ']' . PHP_EOL;
            } else {
               $text .= '[' . $lang . ']' . $record->{Shop_Model_Shippings::COLUMN_TEXT}[Locales::getDefaultLang()] . '[/' . $lang . ']' . PHP_EOL;
            }
         }

         array_push($jqGrid->respond()->rows, array('id' => $record->{Shop_Model_Shippings::COLUMN_ID},
             Shop_Model_Shippings::COLUMN_ID => $record->{Shop_Model_Shippings::COLUMN_ID},
             Shop_Model_Shippings::COLUMN_NAME => $name,
             Shop_Model_Shippings::COLUMN_VALUE => $record->{Shop_Model_Shippings::COLUMN_VALUE},
             Shop_Model_Shippings::COLUMN_TEXT => $text,
             Shop_Model_Shippings::COLUMN_DISALLOWED_PAYMENTS => $record->{Shop_Model_Shippings::COLUMN_DISALLOWED_PAYMENTS},
             Shop_Model_Shippings::COLUMN_PERSONAL_PICKUP => $record->{Shop_Model_Shippings::COLUMN_PERSONAL_PICKUP},
             Shop_Model_Shippings::COLUMN_ID_ZONE => $record->{Shop_Model_Shippings::COLUMN_ID_ZONE},
             Shop_Model_Zones::COLUMN_NAME => $record->{Shop_Model_Shippings::COLUMN_ID_ZONE} != 0 
                  ? (string)$record->{Shop_Model_Zones::COLUMN_NAME}
                  : $this->tr('bez omezení'),
         ));
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function editShippingController()
   {
      $this->checkWritebleRights();
      $model = new Shop_Model_Shippings();
      $this->view()->allOk = false;
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // kontrola položek
            if ($jqGridReq->{Shop_Model_Shippings::COLUMN_NAME} == null) {
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }

            $record = $model->record($jqGridReq->id);

            $matches = array();
            if (preg_match_all('/\[([a-z]{2})\]([^\[]+)\[\/[a-z]{2}\]/', $jqGridReq->{Shop_Model_Shippings::COLUMN_NAME}, $matches) != 0) {
               foreach ($matches[1] as $key => $lang) {
                  $record->{Shop_Model_Shippings::COLUMN_NAME}[$lang] = $matches[2][$key];
               }
            } else {
               $this->errMsg()->addMessage($this->tr('Název není zadán ve správném formátu.'));
               return;
            }

            $matches = array();
            $text = $record->{Shop_Model_Shippings::COLUMN_TEXT};
            if (preg_match_all('/\[([a-z]{2})\]([^\[]+)\[\/[a-z]{2}\]/', $jqGridReq->{Shop_Model_Shippings::COLUMN_TEXT}, $matches) != 0) {
               foreach ($matches[1] as $key => $lang) {
                  $text[$lang] = $matches[2][$key];
               }
            }

            if ($jqGridReq->{Shop_Model_Shippings::COLUMN_VALUE} == null) {
               $jqGridReq->{Shop_Model_Shippings::COLUMN_VALUE} = 0;
            }
            $record->{Shop_Model_Shippings::COLUMN_VALUE} = $jqGridReq->{Shop_Model_Shippings::COLUMN_VALUE};
            $record->{Shop_Model_Shippings::COLUMN_PERSONAL_PICKUP} = $jqGridReq->{Shop_Model_Shippings::COLUMN_PERSONAL_PICKUP};
            $record->{Shop_Model_Shippings::COLUMN_DISALLOWED_PAYMENTS} = (string) $jqGridReq->{Shop_Model_Shippings::COLUMN_DISALLOWED_PAYMENTS};
            $record->{Shop_Model_Shippings::COLUMN_ID_ZONE} = (int) $jqGridReq->{Shop_Model_Zones::COLUMN_NAME};
            $record->save();
            $this->infoMsg()->addMessage($this->tr('Doprava byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               if ($id != 1) {
                  $model->delete($id);
               }
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané typy doprav byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }

   public function ordersController()
   {
      $this->checkReadableRights();

      $this->view()->replace = array(
          '{STRANKY}' => $this->tr('Název stránek (obchodu)'),
          '{DATUM}' => $this->tr('Datum objednávky (obsahuje i čas)'),
          '{IP}' => $this->tr('IP adresa uživatele'),
          '{INFO}' => $this->tr('Informace o uživateli'),
          '{ZBOZI}' => $this->tr('Výpis zboží'),
          '{ADRESA_OBCHOD}' => $this->tr('Adresa obchodu'),
          '{ADRESA_DODACI}' => $this->tr('Adresa dodací'),
          '{ADRESA_FAKTURACNI}' => $this->tr('Adresa fakturační'),
          '{POZNAMKA}' => $this->tr('Poznámka uživatele'),
      );

      $this->view()->replaceStatus = array(
          "{CISLO}" => $this->tr('Číslo objednávky'),
          '{STRANKY}' => $this->tr('Název stránek (obchodu)'),
          '{ADRESA_OBCHOD}' => $this->tr('Adresa obchodu'),
          "{DATUM_ZMENY}" => $this->tr('Datum objednávky (obsahuje i čas)'),
          "{STAV}" => $this->tr('Nový stav'),
          "{POZN}" => $this->tr('Poznámka ke změně stavu'),
      );

      $form = new Form('orders_set_', true);

      $grpNotify = $form->addGroup('notifycations', $this->tr('Oznámení'));

      $eNotifyMail = new Form_Element_Text('notifyMail', $this->tr('E-mail administrátora'));
      $eNotifyMail->addValidation(new Form_Validator_NotEmpty());
      $eNotifyMail->addValidation(new Form_Validator_Email());
      $eNotifyMail->setValues(VVE_SHOP_ORDER_MAIL);
      $eNotifyMail->setSubLabel($this->tr('na tento e-mail budou chodit oznámení o nových objednávkách'));
      $form->addElement($eNotifyMail, $grpNotify);
      
      $grpStatus = $form->addGroup('status', $this->tr('Stavy objednávek'));

      $eDefaultStatus = new Form_Element_Text('statusDefault', $this->tr('ID výchozího stavu objednávky'));
      $eDefaultStatus->addValidation(new Form_Validator_NotEmpty());
      $eDefaultStatus->setSubLabel($this->tr('Počáteční stav při přijetí objednávky. ID ze stavů.'));
      $eDefaultStatus->setValues(VVE_SHOP_ORDER_DEFAULT_STATUS);
      $form->addElement($eDefaultStatus, $grpStatus);

      $eSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $form->addElement($eSave);

      if ($form->isValid()) {
         $this->storeSystemCfg('VVE_SHOP_ORDER_MAIL', $form->notifyMail->getValues());
         $this->storeSystemCfg('VVE_SHOP_ORDER_DEFAULT_STATUS', $form->statusDefault->getValues());

         $this->infoMsg()->addMessage($this->tr('Nastavení bylo uloženo'));
         $this->link()->reload();
      }
      $this->view()->form = $form;
   }

   public function customersController()
   {
      //		Kontrola práv
      $this->checkReadableRights();

      $form = new Form('base', true);

      $grpInfo = $form->addGroup('shop', $this->tr('Informace o obchod'));

      $eStoreName = new Form_Element_Text('name', $this->tr('Název obchodu'));
      $eStoreName->setValues(VVE_WEB_NAME);
      $form->addElement($eStoreName, $grpInfo);

      $eStoreInfo = new Form_Element_TextArea('info', $this->tr('Adresa obchodu'));
      $eStoreInfo->setValues(VVE_SHOP_STORE_ADDRESS);
      $form->addElement($eStoreInfo, $grpInfo);

      $eSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $form->addElement($eSave);

      if ($form->isValid()) {
         $this->storeSystemCfg('VVE_WEB_NAME', $form->name->getValues());
         $this->storeSystemCfg('VVE_SHOP_STORE_ADDRESS', $form->info->getValues());

         $this->infoMsg()->addMessage($this->tr('Nastavení bylo uloženo'));
//         $this->link()->reload();
      }
      $this->view()->form = $form;
   }

   public function zonesListController()
   {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Shop_Model_Zones::COLUMN_NAME);
      // search
      $model = new Shop_Model_Zones();

      $order = Model_ORM::ORDER_ASC;
      if ($jqGrid->request()->order == 'desc') {
         $order = Model_ORM::ORDER_DESC;
      }

      switch ($jqGrid->request()->orderField) {
         case Shop_Model_Zones::COLUMN_ID:
            $model->order(array(Shop_Model_Zones::COLUMN_ID => $order));
            break;
         case Shop_Model_Zones::COLUMN_NAME:
         default:
            $model->order(array(Shop_Model_Zones::COLUMN_NAME => $order));
            break;
      }

      $jqGrid->respond()->setRecords($model->count());
      $records = $model->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)->records();

      // out
      foreach ($records as $record) {
         array_push($jqGrid->respond()->rows, array('id' => $record->{Shop_Model_Zones::COLUMN_ID},
             Shop_Model_Zones::COLUMN_ID => $record->{Shop_Model_Zones::COLUMN_ID},
             Shop_Model_Zones::COLUMN_NAME => $record->{Shop_Model_Zones::COLUMN_NAME},
         ));
      }
      $this->view()->respond = $jqGrid->respond();
   }
   
   public function editZoneController()
   {
      $this->checkWritebleRights();
      $model = new Shop_Model_Zones();
      $this->view()->allOk = false;
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // kontrola položek
            if ($jqGridReq->{Shop_Model_Zones::COLUMN_NAME} == null) {
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }

            $record = $model->record($jqGridReq->id);
            $record->{Shop_Model_Zones::COLUMN_NAME} = $jqGridReq->{Shop_Model_Zones::COLUMN_NAME};
            $model->save($record);
//            $record->save();
            $this->infoMsg()->addMessage($this->tr('Zóna byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               if ($id != 1) {
                  $model->delete($id);
               }
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané typy zón byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
//      var_dump(CoreErrors::getErrors());
   }
   
   public function orderStatesController()
   {
      $mStates = new Shop_Model_OrdersStates();
      
      $fDel = new Form('orderstatedel');
      
      $fDel->addElement(new Form_Element_Hidden('id'));
      $fDel->addElement(new Form_Element_Submit('delete', $this->tr('Smazat')));
      
      if($fDel->isValid()){
         $state = Shop_Model_OrdersStates::getRecord($fDel->id->getValues());
         $state->{Shop_Model_OrdersStates::COLUMN_DELETED} = true;
         $state->save();
         $this->infoMsg()->addMessage($this->tr('Stav byl smazán'));
         $this->link()->redirect();
      }
      
      $this->view()->formDeleteOrderState = $fDel;
      $this->view()->orderStates = $mStates
              ->joinFK(Shop_Model_OrdersStates::COLUMN_ID_TEMPLATE)
              ->where(Shop_Model_OrdersStates::COLUMN_DELETED, 0)
              ->records();
   }
   
   public function editOrderStateController($id)
   {
      $state = Shop_Model_OrdersStates::getRecord($id);
      $form = $this->createOrderStateForm($state);
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route('orderStates')->redirect();
      }
      
      if($form->isValid()){
         $state->{Shop_Model_OrdersStates::COLUMN_NAME} = $form->name->getValues();
         $state->{Shop_Model_OrdersStates::COLUMN_COMPLETE} = $form->complete->getValues();
         $state->{Shop_Model_OrdersStates::COLUMN_ID_TEMPLATE} = $form->idTpl->getValues();
         $state->{Shop_Model_OrdersStates::COLUMN_COLOR} = $form->color->getValues();
         $state->{Shop_Model_OrdersStates::COLUMN_NOTE} = $form->note->getValues();
         
         $state->save();
         $this->infoMsg()->addMessage($this->tr('Stav byl uložen'));
         $this->link()->route('orderStates')->redirect();
      }
      $this->view()->formEditOrderState = $form;
   }
   
   public function addOrderStateController()
   {
      $form = $this->createOrderStateForm();
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route('orderStates')->redirect();
      }
      
      if($form->isValid()){
         $state = Shop_Model_OrdersStates::getNewRecord();
         
         $state->{Shop_Model_OrdersStates::COLUMN_NAME} = $form->name->getValues();
         $state->{Shop_Model_OrdersStates::COLUMN_COMPLETE} = $form->complete->getValues();
         $state->{Shop_Model_OrdersStates::COLUMN_ID_TEMPLATE} = $form->idTpl->getValues();
         $state->{Shop_Model_OrdersStates::COLUMN_COLOR} = $form->color->getValues();
         $state->{Shop_Model_OrdersStates::COLUMN_NOTE} = $form->note->getValues();
         
         $state->save();
         $this->infoMsg()->addMessage($this->tr('Stav byl uložen'));
         $this->link()->route('orderStates')->redirect();
      }
      $this->view()->formEditOrderState = $form;
   }
   
   protected function createOrderStateForm(Model_ORM_Record $state = null)
   {
      $f = new Form('editorderstate');
      
      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $f->addElement($eName);
      
      $f->addElement(new Form_Element_Text('color', $this->tr('Barva stavu')));
      
      $tplModel = new Templates_Model();
      $tpls = $tplModel->where(Templates_Model::COLUMN_TYPE, Templates_Model::TEMPLATE_TYPE_MAIL)->records();
      $eTpls = new Form_Element_Select('idTpl', $this->tr('Šablona e-mailu'));
      $eTpls->addOption($this->tr('--- žádná ---'), 0);
      foreach ($tpls as $tpl) {
         $eTpls->addOption($tpl->{Templates_Model::COLUMN_NAME}, $tpl->getPK());
      }
      $f->addElement($eTpls);
      
      $f->addElement(new Form_Element_Text('note', $this->tr('Poznámka')));
      
      $f->addElement(new Form_Element_Checkbox('complete', $this->tr('Označit objednávku jako vyřízenou')));
      
      $f->addElement(new Form_Element_SaveCancel('save'));
      
      if($state){
         $f->name->setValues($state->{Shop_Model_OrdersStates::COLUMN_NAME});
         $f->color->setValues($state->{Shop_Model_OrdersStates::COLUMN_COLOR});
         $f->complete->setValues($state->{Shop_Model_OrdersStates::COLUMN_COMPLETE});
         $f->idTpl->setValues($state->{Shop_Model_OrdersStates::COLUMN_ID_TEMPLATE});
         $f->note->setValues($state->{Shop_Model_OrdersStates::COLUMN_NOTE});
      }
      
      return $f;
   }
   

   private function storeSystemCfg($constName, $value)
   {
      if ($this->model == null) {
         $this->model = new Model_Config();
      }
      // web name
      $cfg = $this->model->where(Model_Config::COLUMN_KEY . ' = :key', array('key' => str_replace('VVE_', '', $constName)))->record();

      if ($cfg != false) {// vytvoř nový záznam, asi nebyl definován
         $cfg->{Model_Config::COLUMN_VALUE} = $value;
         $this->model->save($cfg);
      } else {
         
      }
   }

   public function mailVariablesController()
   {
      switch ($this->getRequestParam('type', null)) {
         case "userMail":
         case "adminMail":
            $this->view()->variables = array(
                '{STRANKY}' => $this->tr('Název stránek (obchodu)'),
                '{DATUM}' => $this->tr('Datum objednávky s časem'),
                '{IP}' => $this->tr('IP adresa uživatele'),
                '{INFO}' => $this->tr('Informace o objednávce'),
                '{ZBOZI}' => $this->tr('Výpis zboží, dopravy a platby i s cenami'),
                '{ADRESA_OBCHOD}' => $this->tr('Adresa obchodu'),
                '{ADRESA_DODACI}' => $this->tr('Adresa dodací'),
                '{ADRESA_FAKTURACNI}' => $this->tr('Adresa fakturační'),
                '{POZNAMKA}' => $this->tr('Poznámka uživatele'),
            );
            break;
         case "orderStatus":
            $this->view()->variables = array(
                '{STRANKY}' => $this->tr('Název stránek (obchodu)'),
                '{CISLO}' => $this->tr('Číslo objednávky'),
                '{ADRESA_OBCHOD}' => $this->tr('Adresa obchodu'),
                '{DATUM_ZMENY}' => $this->tr('Datum změny stavu'),
                '{STAV}' => $this->tr('Nový stav'),
                '{POZN}' => $this->tr('Poznámka stavu'),
            );
            break;
      }
   }

}
