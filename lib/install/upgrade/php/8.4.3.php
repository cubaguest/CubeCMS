<?php

// nové stavy objednávek u eshopu
if(defined('CUBE_CMS_SHOP') && CUBE_CMS_SHOP == true){
   
   $mStates = new Shop_Model_OrderStatus();
   $mNewStates = new Shop_Model_OrdersStates();
   $mNewStates->truncate();
   $mHistory = new Shop_Model_OrdersHistory();
   $mHistory->truncate();
   
   $oldStatuses = $mStates->groupBy(array(Shop_Model_OrderStatus::COLUMN_NAME))->records();
   
   $storedStates = array();
   
   foreach ($oldStatuses as $status) {
      $newState = $mNewStates->newRecord();
      $newState->{Shop_Model_OrdersStates::COLUMN_NAME} = array(Locales::getDefaultLang() => $status->{Shop_Model_OrderStatus::COLUMN_NAME});
      $newState->save();
      $storedStates[(string)$status->{Shop_Model_OrderStatus::COLUMN_NAME}] = $newState->getPK();
   }
   
   // přiřazení uložených stavů
   $allStatuses = Shop_Model_OrderStatus::getAllRecords();
   foreach ($allStatuses as $status) {
      if($status->{Shop_Model_OrderStatus::COLUMN_ID_ORDER} == 0){
         continue;
      }
      $history = Shop_Model_OrdersHistory::getNewRecord();
      $history->{Shop_Model_OrdersHistory::COLUMN_ID_ORDER} = $status->{Shop_Model_OrderStatus::COLUMN_ID_ORDER};
      $history->{Shop_Model_OrdersHistory::COLUMN_ID_STATE} = $storedStates[(string)$status->{Shop_Model_OrderStatus::COLUMN_NAME}];
      $history->{Shop_Model_OrdersHistory::COLUMN_TIME_ADD} = $status->{Shop_Model_OrderStatus::COLUMN_TIME_ADD};
      $history->{Shop_Model_OrdersHistory::COLUMN_NOTE} = $status->{Shop_Model_OrderStatus::COLUMN_NOTE};
      $history->save();
   }
   $idBaseState = $storedStates[CUBE_CMS_SHOP_ORDER_DEFAULT_STATUS];
   $langs = Locales::getAppLangs();
   foreach ($langs as $lang) {
      if(is_file(AppCore::getAppDataDir() . 'shop' . DIRECTORY_SEPARATOR . 'mail_tpl_user_' . $lang . '.html')){
         $cnt = file_get_contents(AppCore::getAppDataDir() . 'shop' . DIRECTORY_SEPARATOR . 'mail_tpl_user_' . $lang . '.html');
         $tpl = Templates_Model::getNewRecord();
         $tpl->{Templates_Model::COLUMN_CONTENT} = $cnt;
         $tpl->{Templates_Model::COLUMN_NAME} = 'Šablona přijaté objednávky';
         $tpl->{Templates_Model::COLUMN_LANG} = $lang;
         $tpl->{Templates_Model::COLUMN_TYPE} = Templates_Model::TEMPLATE_TYPE_MAIL;
         $tpl->save();
         
         $stateBase = Shop_Model_OrdersStates::getRecord($idBaseState);
         $stateBase->{Shop_Model_OrdersStates::COLUMN_ID_TEMPLATE} = $tpl->getPK();
         $stateBase->save();
      }
   }
   
   // vytvoření stavů u objednávek, které nejsou v historii
   $orders = Shop_Model_Orders::getAllRecords();
   foreach ($orders as $order) {
      if(!$order->getLastState()){
         $history = Shop_Model_OrdersHistory::getNewRecord();
         $history->{Shop_Model_OrdersHistory::COLUMN_ID_ORDER} = $order->getPK();
         $history->{Shop_Model_OrdersHistory::COLUMN_ID_STATE} = $idBaseState;
         $history->{Shop_Model_OrdersHistory::COLUMN_TIME_ADD} = $order->{Shop_Model_Orders::COLUMN_TIME_ADD};
         $history->save();
      }
   }
   
   Model_Config::setValue('SHOP_ORDER_DEFAULT_STATUS', $idBaseState);
}