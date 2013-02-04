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
      
      if($form->isValid()){
         $this->storeSystemCfg('VVE_WEB_NAME', $form->name->getValues());
         $this->storeSystemCfg('VVE_SHOP_STORE_ADDRESS', $form->info->getValues());
         $this->storeSystemCfg('VVE_SHOP_ALLOW_BUY_NOT_IN_STOCK', $form->buyNotInStock->getValues() ?  "false" : "true");

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
      
      if($form->isValid()){
         $this->storeSystemCfg('VVE_SHOP_CURRENCY_NAME', $form->name->getValues());
         $this->storeSystemCfg('VVE_SHOP_CURRENCY_CODE', $form->code->getValues());
         
         $this->infoMsg()->addMessage($this->tr('Nastavení bylo uloženo'));
//         $this->link()->reload();
      }
      $this->view()->form = $form;
   }
   
   public function taxesListController() {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Shop_Model_Tax::COLUMN_NAME);
      // search
      $model = new Shop_Model_Tax();

      $order = Model_ORM::ORDER_ASC;
      if($jqGrid->request()->order == 'desc'){
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
         array_push($jqGrid->respond()->rows, 
            array('id' => $tax->{Shop_Model_Tax::COLUMN_ID},
                  Shop_Model_Tax::COLUMN_NAME => $tax->{Shop_Model_Tax::COLUMN_NAME},
                  Shop_Model_Tax::COLUMN_VALUE => $tax->{Shop_Model_Tax::COLUMN_VALUE},
                     ));
      }
      $this->view()->respond = $jqGrid->respond();
   }
   
   public function editTaxController() {
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
            if($jqGridReq->{Shop_Model_Tax::COLUMN_NAME} == null || $jqGridReq->{Shop_Model_Tax::COLUMN_VALUE} === null){
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }
            // validace hodnoty
            if(!is_numeric($jqGridReq->{Shop_Model_Tax::COLUMN_VALUE})){
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
               if($id != 1){
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
      
      if($form->isValid()){
         $this->storeSystemCfg('VVE_SHOP_FREE_SHIPPING', $form->freeShipping->getValues());
         $this->infoMsg()->addMessage($this->tr('Nastavení bylo uloženo'));
         $this->link()->reload();
      }
      $this->view()->form = $form;
   }
   
   public function paymentsListController()
   {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Shop_Model_Payments::COLUMN_NAME);
      // search
      $model = new Shop_Model_Payments();

      $order = Model_ORM::ORDER_ASC;
      if($jqGrid->request()->order == 'desc'){
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
            if($val != null){
               $name .= '['.$lang.']'.$val.'[/'.$lang.']'.PHP_EOL;
            } else {
               $name .= '['.$lang.']'.$payment->{Shop_Model_Payments::COLUMN_NAME}[Locales::getDefaultLang()].'[/'.$lang.']'.PHP_EOL;
            }
         }
         $text = null;
         foreach ($payment->{Shop_Model_Payments::COLUMN_TEXT} as $lang => $val) {
            if($val != null){
               $text .= '['.$lang.']'.$val.'[/'.$lang.']'.PHP_EOL;
            } else {
               $text .= '['.$lang.']'.$payment->{Shop_Model_Payments::COLUMN_TEXT}[Locales::getDefaultLang()].'[/'.$lang.']'.PHP_EOL;
            }
         }
         
         array_push($jqGrid->respond()->rows, 
            array('id' => $payment->{Shop_Model_Payments::COLUMN_ID},
                  Shop_Model_Payments::COLUMN_ID => $payment->{Shop_Model_Payments::COLUMN_ID},
                  Shop_Model_Payments::COLUMN_NAME => $name,
                  Shop_Model_Payments::COLUMN_PRICE_ADD => $payment->{Shop_Model_Payments::COLUMN_PRICE_ADD},
                  Shop_Model_Payments::COLUMN_TEXT => $text,
            ));
      }
      $this->view()->respond = $jqGrid->respond();
   }
   
   public function editPaymentController() {
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
            if($jqGridReq->{Shop_Model_Payments::COLUMN_NAME} == null){
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }
            
            $record = $model->record($jqGridReq->id);
            
            $matches = array(); 
            if(preg_match_all('/\[([a-z]{2})\]([^\[]+)\[\/[a-z]{2}\]/', $jqGridReq->{Shop_Model_Payments::COLUMN_NAME}, $matches) != 0){
               foreach ($matches[1] as $key => $lang) {
                  $record->{Shop_Model_Payments::COLUMN_NAME}[$lang] = $matches[2][$key];
               }
            } else {
               $this->errMsg()->addMessage($this->tr('Název není zadán ve správném formátu.'));
               return;
            }
            
            $matches = array(); 
            if(preg_match_all('/\[([a-z]{2})\]([^\[]+)\[\/[a-z]{2}\]/', $jqGridReq->{Shop_Model_Payments::COLUMN_TEXT}, $matches) != 0){
               foreach ($matches[1] as $key => $lang) {
                  $record->{Shop_Model_Payments::COLUMN_TEXT}[$lang] = $matches[2][$key];
               }
            }
            
            if($jqGridReq->{Shop_Model_Payments::COLUMN_PRICE_ADD} == null){
               $jqGridReq->{Shop_Model_Payments::COLUMN_PRICE_ADD} = 0;
            }
            $record->{Shop_Model_Payments::COLUMN_PRICE_ADD} = $jqGridReq->{Shop_Model_Payments::COLUMN_PRICE_ADD};
//            $this->view()->r = $record;
            $model->save($record);
          
            $this->infoMsg()->addMessage($this->_('Platba byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               if($id != 1){
                  $model->delete($id);
               }
            }
            $this->infoMsg()->addMessage($this->_('Vybrané platby byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->_('Nepodporovaný typ operace'));
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
      if($jqGrid->request()->order == 'desc'){
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
      
      $jqGrid->respond()->setRecords($model->count());
      $records = $model->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)->records();
      
      // out
      foreach ($records as $record) {
         $name = null;
         foreach ($record->{Shop_Model_Shippings::COLUMN_NAME} as $lang => $val) {
            if($val != null){
               $name .= '['.$lang.']'.$val.'[/'.$lang.']'.PHP_EOL;
            } else {
               $name .= '['.$lang.']'.$record->{Shop_Model_Shippings::COLUMN_NAME}[Locales::getDefaultLang()].'[/'.$lang.']'.PHP_EOL;
            }
         }
         $text = null;
         foreach ($record->{Shop_Model_Shippings::COLUMN_TEXT} as $lang => $val) {
            if($val != null){
               $text .= '['.$lang.']'.$val.'[/'.$lang.']'.PHP_EOL;
            } else {
               $text .= '['.$lang.']'.$record->{Shop_Model_Shippings::COLUMN_TEXT}[Locales::getDefaultLang()].'[/'.$lang.']'.PHP_EOL;
            }
         }
         
         array_push($jqGrid->respond()->rows, 
            array('id' => $record->{Shop_Model_Shippings::COLUMN_ID},
               Shop_Model_Shippings::COLUMN_ID => $record->{Shop_Model_Shippings::COLUMN_ID},
               Shop_Model_Shippings::COLUMN_NAME => $name,
               Shop_Model_Shippings::COLUMN_VALUE => $record->{Shop_Model_Shippings::COLUMN_VALUE},
               Shop_Model_Shippings::COLUMN_TEXT => $text,
               Shop_Model_Shippings::COLUMN_DISALLOWED_PAYMENTS => $record->{Shop_Model_Shippings::COLUMN_DISALLOWED_PAYMENTS},
               Shop_Model_Shippings::COLUMN_PERSONAL_PICKUP => $record->{Shop_Model_Shippings::COLUMN_PERSONAL_PICKUP},
            ));
      }
      $this->view()->respond = $jqGrid->respond();
   }
   
   public function editShippingController() {
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
            if($jqGridReq->{Shop_Model_Shippings::COLUMN_NAME} == null){
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }
            
            $record = $model->record($jqGridReq->id);
            
            $matches = array();
            if(preg_match_all('/\[([a-z]{2})\]([^\[]+)\[\/[a-z]{2}\]/', $jqGridReq->{Shop_Model_Shippings::COLUMN_NAME}, $matches) != 0){
               foreach ($matches[1] as $key => $lang) {
                  $record->{Shop_Model_Shippings::COLUMN_NAME}[$lang] = $matches[2][$key];
               }
            } else {
               $this->errMsg()->addMessage($this->tr('Název není zadán ve správném formátu.'));
               return;
            }
            
            $matches = array(); 
            $text = $record->{Shop_Model_Shippings::COLUMN_TEXT};
            if(preg_match_all('/\[([a-z]{2})\]([^\[]+)\[\/[a-z]{2}\]/', $jqGridReq->{Shop_Model_Shippings::COLUMN_TEXT}, $matches) != 0){
               foreach ($matches[1] as $key => $lang) {
                  $text[$lang] = $matches[2][$key];
               }
            }
            
            if($jqGridReq->{Shop_Model_Shippings::COLUMN_VALUE} == null){
               $jqGridReq->{Shop_Model_Shippings::COLUMN_VALUE} = 0;
            }
            $record->{Shop_Model_Shippings::COLUMN_VALUE} = $jqGridReq->{Shop_Model_Shippings::COLUMN_VALUE};
            $record->{Shop_Model_Shippings::COLUMN_PERSONAL_PICKUP} = $jqGridReq->{Shop_Model_Shippings::COLUMN_PERSONAL_PICKUP};
            $record->{Shop_Model_Shippings::COLUMN_DISALLOWED_PAYMENTS} = (string)$jqGridReq->{Shop_Model_Shippings::COLUMN_DISALLOWED_PAYMENTS};
            $record->save();
            $this->infoMsg()->addMessage($this->tr('Doprava byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               if($id != 1){
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

      $eUserMailText = new Form_Element_TextArea('notifyUserMail', $this->tr('Text e-mailu pro uživatele'));
      $eUserMailText->setLangs();
      // načtení hodnot pokud existují
      $values = array();
      foreach (Locales::getAppLangs() as $lang) {
         $values[$lang] = null;
         $file = $this->module()->getDataDir().'mail_tpl_user_'.$lang.'.html';
         if(is_file($file)){
            $values[$lang] = file_get_contents($file);
         }
      }
      $eUserMailText->setValues($values);
      $form->addElement($eUserMailText, $grpNotify);
      
      $eUserStatusText = new Form_Element_TextArea('userOrderStatusMail', $this->tr('Text e-mailu pro změnu stavu'));
      $eUserStatusText->setLangs();
      // načtení hodnot pokud existují
      $values = array();
      foreach (Locales::getAppLangs() as $lang) {
         $values[$lang] = null;
         $file = $this->module()->getDataDir().'mail_tpl_orderstatus_'.$lang.'.html';
         if(is_file($file)){
            $values[$lang] = file_get_contents($file);
         }
      }
      $eUserStatusText->setValues($values);
      $form->addElement($eUserStatusText, $grpNotify);
      
      $eAdminMailText = new Form_Element_TextArea('notifyAdminMail', $this->tr('Text e-mailu pro administrátora'));

      $file = $this->module()->getDataDir().'mail_tpl_admin.html';
      if(is_file($file)){
         $eAdminMailText->setValues(file_get_contents($file));
      }
      $form->addElement($eAdminMailText, $grpNotify);
      
      $grpStatus = $form->addGroup('status', $this->tr('Stavy objednávek'));
      
      //VVE_SHOP_ORDER_DEFAULT_STATUS
      $eDefaultStatus = new Form_Element_Text('statusDefault', $this->tr('Výchozí stav objednávky'));
      $eDefaultStatus->addValidation(new Form_Validator_NotEmpty());
      $eDefaultStatus->setSubLabel($this->tr('Počáteční stav při přijetí objednávky. Například "přijato".'));
      $eDefaultStatus->setValues(VVE_SHOP_ORDER_DEFAULT_STATUS);
      $form->addElement($eDefaultStatus, $grpStatus);
      
      $eStatus = new Form_Element_TextArea('status', $this->tr('Stavy objednávky'));
      $eStatus->addValidation(new Form_Validator_NotEmpty());
      $eStatus->setValues(VVE_SHOP_ORDER_STATUS);
      $eStatus->setSubLabel($this->tr('Předdefinované stavy objednávek (např. přijato;odesláno;vráceno) oddělené středníkem.'));
      $form->addElement($eStatus, $grpStatus);
      
      
      $eSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $form->addElement($eSave);
      
      if($form->isValid()){
         $this->storeSystemCfg('VVE_SHOP_ORDER_MAIL', $form->notifyMail->getValues());
         $this->storeSystemCfg('VVE_SHOP_ORDER_DEFAULT_STATUS', $form->statusDefault->getValues());
         $this->storeSystemCfg('VVE_SHOP_ORDER_STATUS', $form->status->getValues());
         
         // uložení mailů
         $usersTexts = $form->notifyUserMail->getValues();
         foreach ($usersTexts as $lang => $text) {
            if($text != null && !file_put_contents($this->module()->getDataDir().'mail_tpl_user_'.$lang.'.html', $text)){
               throw new UnexpectedValueException(sprintf($this->tr('Chyba při zápisu do souboru s mailem uživatele (jazyk: %s)'), $lang));
            }
         }
         
         $statusTexts = $form->userOrderStatusMail->getValues();
         foreach ($statusTexts as $lang => $text) {
            if($text != null && !file_put_contents($this->module()->getDataDir().'mail_tpl_orderstatus_'.$lang.'.html', $text)){
               throw new UnexpectedValueException(sprintf($this->tr('Chyba při zápisu do souboru s mailem změny stavu objednávky (jazyk: %s)'), $lang));
            }
         }
         
         if(!file_put_contents($this->module()->getDataDir().'mail_tpl_admin.html', $form->notifyAdminMail->getValues())){
            throw new UnexpectedValueException($this->tr('Chyba při zápisu do souboru s mailem administrátora'));
         }
         
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
      
      if($form->isValid()){
         $this->storeSystemCfg('VVE_WEB_NAME', $form->name->getValues());
         $this->storeSystemCfg('VVE_SHOP_STORE_ADDRESS', $form->info->getValues());
         
         $this->infoMsg()->addMessage($this->tr('Nastavení bylo uloženo'));
//         $this->link()->reload();
      }
      $this->view()->form = $form;
   }

   private function storeSystemCfg($constName, $value)
   {
      if($this->model == null){
         $this->model = new Model_Config();
      }
      // web name
      $cfg = $this->model->where(Model_Config::COLUMN_KEY.' = :key', array('key' => str_replace('VVE_', '', $constName)))->record();
      
      if($cfg != false){// vytvoř nový záznam, asi nebyl definován
         $cfg->{Model_Config::COLUMN_VALUE} = $value;
         $this->model->save($cfg);
      } else {
         
      }
   }

   public function mailVariablesController()
   {
      switch($this->getRequestParam('type', null)) {
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
