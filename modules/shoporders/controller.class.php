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
      
      $modelSt = new Shop_Model_OrderStatus();
      $modelOrders->columns(array(Shop_Model_Orders::COLUMN_ID, Shop_Model_Orders::COLUMN_CUSTOMER_NAME, 
         Shop_Model_Orders::COLUMN_IS_NEW, Shop_Model_Orders::COLUMN_TIME_ADD, Shop_Model_Orders::COLUMN_TOTAL,
         Shop_Model_Orders::COLUMN_PICKUP_DATE,
         'status' => 
         '(SELECT '.Shop_Model_OrderStatus::COLUMN_NAME.' FROM '.$modelSt->getTableName()
         .' WHERE '.Shop_Model_OrderStatus::COLUMN_ID_ORDER.' = '.$modelOrders->getTableShortName().'.'.Shop_Model_Orders::COLUMN_ID
         .' ORDER BY '.Shop_Model_OrderStatus::COLUMN_TIME_ADD.' DESC LIMIT 0,1)'));
      
      $jqGrid->respond()->sql = $modelOrders->getSQLQuery();
      
      $jqGrid->respond()->setRecords($modelOrders->count());
      
      $fromRow = ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage();
      
      $orders = $modelOrders->limit($fromRow, $jqGrid->request()->rows)->records(PDO::FETCH_OBJ);
      // out
      foreach ($orders as $order) {
         $pickupDate = "";
         if($order->{Shop_Model_Orders::COLUMN_PICKUP_DATE} != null){
            $pickupDate = vve_date("%x", new DateTime($order->{Shop_Model_Orders::COLUMN_PICKUP_DATE}));
         }
         array_push($jqGrid->respond()->rows, 
            array('id' => $order->{Shop_Model_Orders::COLUMN_ID},
                  'name' => $order->{Shop_Model_Orders::COLUMN_CUSTOMER_NAME},
                  'neworder' => $order->{Shop_Model_Orders::COLUMN_IS_NEW},
                  'time' => vve_date('%x %X', new DateTime($order->{Shop_Model_Orders::COLUMN_TIME_ADD})),
                  'pickupdate' => $pickupDate,
                  'status' => $order->status,
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
         
      $modelStatus = new Shop_Model_OrderStatus();
      
      $this->view()->statuses = $modelStatus
         ->where(Shop_Model_OrderStatus::COLUMN_ID_ORDER.' = :ido', array('ido' => $order->{Shop_Model_Orders::COLUMN_ID}))
         ->order(array(Shop_Model_OrderStatus::COLUMN_TIME_ADD => Model_ORM::ORDER_ASC))
         ->records();
      
      $this->changeStatusController($order->{Shop_Model_Orders::COLUMN_ID});
   }

   public function changeStatusController($id = null)
   {
      $formChangeStatus = new Form('status');
      $formChangeStatus->setProtected(false);
      $formChangeStatus->setAction($this->link()->route('changeStatus'));
      
      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $formChangeStatus->addElement($eName);
      
      $eNameSel = new Form_Element_Select('nameSel', $this->tr('Název'));
      
      $status = explode(';', VVE_SHOP_ORDER_STATUS);
      
      if(empty ($status)){
         $eNameSel->setOptions(
            array(
            $this->tr('přijato') => $this->tr('přijato'),
            $this->tr('odesláno') => $this->tr('odesláno'),
            $this->tr('zrušeno') => $this->tr('zrušeno'),
            $this->tr('zaplaceno') => $this->tr('zaplaceno'),
            $this->tr('zabaleno') => $this->tr('zabaleno'),
            $this->tr('vráceno') => $this->tr('vráceno'),
         ));
      } else {
         foreach ($status as $value) {
            $eNameSel->setOptions(array($value => $value), true);
         }
      }
      
      $formChangeStatus->addElement($eNameSel);
      
      $eId = new Form_Element_Hidden('id');
      if($id != null){
         $eId->setValues($id);
      }
      $formChangeStatus->addElement($eId);
      
      $eNote = new Form_Element_TextArea('note', $this->tr('Popis'));
      $formChangeStatus->addElement($eNote);
      
      $eAdd = new Form_Element_Submit('add', $this->tr('Přidat'));
      $formChangeStatus->addElement($eAdd);
      
      $eInformCustomer = new Form_Element_Checkbox('infoCust', $this->tr('Informovat zákazníka e-mailem'));
      $formChangeStatus->addElement($eInformCustomer);
      // checboxy o upozornění
      
      if($formChangeStatus->isValid()){
         $modelStatus = new Shop_Model_OrderStatus();
         $status = $modelStatus->newRecord();
         
         $status->{Shop_Model_OrderStatus::COLUMN_ID_ORDER} = $formChangeStatus->id->getValues();
         if($formChangeStatus->name->getValues() != null){
            $status->{Shop_Model_OrderStatus::COLUMN_NAME} = $formChangeStatus->name->getValues();
         } else {
            $status->{Shop_Model_OrderStatus::COLUMN_NAME} = $formChangeStatus->nameSel->getValues();
         }
         $status->{Shop_Model_OrderStatus::COLUMN_NOTE} = $formChangeStatus->note->getValues();
         
         $modelStatus->save($status);
         
         if($formChangeStatus->infoCust->getValues() == true){
            $modelOrder = new Shop_Model_Orders();
            $email = new Email();
            $order = $modelOrder->record($status->{Shop_Model_OrderStatus::COLUMN_ID_ORDER});
            
            $email->setSubject(sprintf($this->tr('Změna stavu objednávky č. %s'), $order->{Shop_Model_Orders::COLUMN_ID}));
            
            $MailText = "Dobrý den\n\n"
               ."Vaší objednávce číslo: {CISLO} v obchodě {STRANKY} byl upraven stav.\n\n"
               ."Nový stav Vaší objednávky je: \"{STAV}\"\n\n"
               ."Poznámmka: {POZN} ";
            
            $file = $this->module()->getDataDir().'mail_tpl_orderstatus_'.Locales::getLang().'.txt';
            if (is_file($file)) {
               $MailText = file_get_contents($file);
            }
            
            $MailText = str_replace(array(
               "{CISLO}",
               "{STRANKY}",
               "{ADRESA_OBCHOD}",
               "{DATUM_ZMENY}",
               "{STAV}",
               "{POZN}",
            ), array(
               $order->{Shop_Model_Orders::COLUMN_ID},
               VVE_WEB_NAME,
               VVE_SHOP_STORE_ADDRESS,
               vve_date("%x %X", new DateTime($status->{Shop_Model_OrderStatus::COLUMN_TIME_ADD})),
               $status->{Shop_Model_OrderStatus::COLUMN_NAME},
               $status->{Shop_Model_OrderStatus::COLUMN_NOTE},
            ), $MailText);
            
            
            $email->setContent($MailText);
            $email->addAddress($order->{Shop_Model_Orders::COLUMN_CUSTOMER_EMAIL}, $order->{Shop_Model_Orders::COLUMN_CUSTOMER_NAME});
            $email->send();
         }
         
         $this->view()->newStatus = array(
            'date' => vve_date('%x %X'),
            'name' => $status->{Shop_Model_OrderStatus::COLUMN_NAME},
            'note' => $status->{Shop_Model_OrderStatus::COLUMN_NOTE},
         );
         
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

   public function settings(&$settings, Form &$form) {
      $fGrpViewSet = $form->addGroup('view', $this->tr('Nastavení vzhledu'));

      $componentTpls = new Component_ViewTpl();
      $componentTpls->setConfig(Component_ViewTpl::PARAM_MODULE, 'text');

      $elemTplMain = new Form_Element_Select('tplMain', $this->tr('Hlavní šablona'));
      $elemTplMain->setOptions(array_flip($componentTpls->getTpls()));
      if(isset($settings[self::PARAM_TPL_MAIN])) {
         $elemTplMain->setValues($settings[self::PARAM_TPL_MAIN]);
      }
      $form->addElement($elemTplMain, $fGrpViewSet);
      unset ($componentTpls);

      $fGrpEditSet = $form->addGroup('editSettings', $this->tr('Nastavení úprav'));

      $elemEditorType = new Form_Element_Select('editor_type', $this->tr('Typ editoru'));
      $elemEditorType->setOptions(array(
         $this->tr('žádný (pouze textová oblast)') => 'none',
         $this->tr('jednoduchý (Wysiwyg)') => 'simple',
         $this->tr('pokročilý (Wysiwyg)') => 'advanced',
         $this->tr('kompletní (Wysiwyg)') => 'full'
      ));
      $elemEditorType->setValues('advanced');
      if(isset($settings[self::PARAM_EDITOR_TYPE])) {
         $elemEditorType->setValues($settings[self::PARAM_EDITOR_TYPE]);
      }

      $form->addElement($elemEditorType, $fGrpEditSet);

      $elemAllowScripts = new Form_Element_Checkbox('allow_script', $this->tr('Povolit scripty v textu'));
      $elemAllowScripts->setSubLabel($this->tr('Umožňuje vkládání javascriptů přímo do textu. POZOR! Lze tak vložit útočníkův kód do stránek. (Filtrují se všechny javascripty.)'));
      $elemAllowScripts->setValues(false);
      if(isset($settings[self::PARAM_ALLOW_SCRIPT_IN_TEXT])) {
         $elemAllowScripts->setValues($settings[self::PARAM_ALLOW_SCRIPT_IN_TEXT]);
      }
      $form->addElement($elemAllowScripts, $fGrpEditSet);

      $fGrpPrivate = $form->addGroup('privateZone', $this->tr('Privátní zóna'), $this->tr("Privátní zóna povoluje
         vložení textů, které jsou viditelné pouze vybraným uživatelům. U každého článku tak
         vznikne další textové okno s výběrem uživatelů majících přístup k těmto textům."));

      $elemAllowPrivateZone = new Form_Element_Checkbox('allow_private_zone',
              $this->tr('Povolit privátní zónu'));
      $form->addElement($elemAllowPrivateZone, $fGrpPrivate);
      if(isset($settings[self::PARAM_ALLOW_PRIVATE])) {
         $form->allow_private_zone->setValues((bool)$settings[self::PARAM_ALLOW_PRIVATE]);
      }
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings[self::PARAM_ALLOW_PRIVATE] = $form->allow_private_zone->getValues();
         $settings[self::PARAM_EDITOR_TYPE] = $form->editor_type->getValues();
         $settings[self::PARAM_ALLOW_SCRIPT_IN_TEXT] = $form->allow_script->getValues();
         $settings[self::PARAM_TPL_MAIN] = $form->tplMain->getValues();
      }
   }
}

?>