<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class ShopPrAttributes_Controller extends Controller {
   
   protected function init()
   {
      $this->category()->getModule()->setDataDir('shop');
   }
   
   public function mainController() 
   {
      //		Kontrola práv
      $this->checkReadableRights();
   }
   
   public function attrGroupsListController() 
   {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Shop_Model_AttrGroups::COLUMN_NAME);
      // search
      
      $model = new Shop_Model_AttrGroups();

      $order = Model_ORM::ORDER_ASC;
      if($jqGrid->request()->order == 'desc'){
         $order = Model_ORM::ORDER_DESC;
      }
      
      switch ($jqGrid->request()->orderField) {
         case 'id':
            $model->order(array(Shop_Model_AttrGroups::COLUMN_ID => $order));
            break;
         case 'name':
         default:
            $model->order(array(Shop_Model_AttrGroups::COLUMN_NAME => $order));
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
      
      $jqGrid->respond()->sql = $model->getSQLQuery();
      
      $jqGrid->respond()->setRecords($model->count());
      
      $fromRow = ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage();
      
      $groups = $model->limit($fromRow, $jqGrid->request()->rows)
         ->records(PDO::FETCH_OBJ);
      // out
      foreach ($groups as $grp) {
         array_push($jqGrid->respond()->rows, 
            array('id' => $grp->{Shop_Model_AttrGroups::COLUMN_ID},
                  Shop_Model_AttrGroups::COLUMN_NAME => $grp->{Shop_Model_AttrGroups::COLUMN_NAME},
                  Shop_Model_AttrGroups::COLUMN_ID => $grp->{Shop_Model_AttrGroups::COLUMN_ID}
            ));
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function editAttrGroupController()
   {
      $this->checkWritebleRights();
      $model = new Shop_Model_AttrGroups();
      $this->view()->allOk = false;
      
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // kontrola položek
            if($jqGridReq->{Shop_Model_AttrGroups::COLUMN_NAME} == null){
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }
            
            $record = $model->record($jqGridReq->id);
            $record->mapArray($jqGridReq);
            $model->save($record);
            $this->infoMsg()->addMessage($this->tr('Skupina byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               $model->delete($id);
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané skupiny včetně atributů byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }
   
   public function attrListController() 
   {
      $this->checkReadableRights();
      
      $gid = $this->getRequestParam('gid', null);
      if($gid == null){
         throw new UnexpectedValueException($this->tr('ID skupiny nebylo předáno'));
      }
      
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Shop_Model_Attributes::COLUMN_NAME);
      
      $model = new Shop_Model_Attributes();
      $model->where(Shop_Model_Attributes::COLUMN_ID_GROUP.' = :gid', array('gid' => $gid));

      $order = Model_ORM::ORDER_ASC;
      if($jqGrid->request()->order == 'desc'){
         $order = Model_ORM::ORDER_DESC;
      }
      
      switch ($jqGrid->request()->orderField) {
         case 'id':
            $model->order(array(Shop_Model_Attributes::COLUMN_ID => $order));
            break;
         case 'name':
         default:
            $model->order(array(Shop_Model_Attributes::COLUMN_NAME => $order));
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
      
      $jqGrid->respond()->sql = $model->getSQLQuery();
      
      $jqGrid->respond()->setRecords($model->count());
      
      $fromRow = ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage();
      
      $attribs = $model->limit($fromRow, $jqGrid->request()->rows)
         ->records(PDO::FETCH_OBJ);
      // out
      foreach ($attribs as $attr) {
         array_push($jqGrid->respond()->rows, 
            array('id' => $attr->{Shop_Model_Attributes::COLUMN_ID},
               Shop_Model_Attributes::COLUMN_NAME => $attr->{Shop_Model_Attributes::COLUMN_NAME},
               Shop_Model_Attributes::COLUMN_ID => $attr->{Shop_Model_Attributes::COLUMN_ID}
            ));
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function editAttrController()
   {
      $this->checkWritebleRights();
      $model = new Shop_Model_Attributes;
      $this->view()->allOk = false;
      
      if($this->getRequestParam('gid', null) == null){
         throw new UnexpectedValueException($this->tr('ID skupiny nebylo předáno'));
      }
      
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // kontrola položek
            if($jqGridReq->{Shop_Model_Attributes::COLUMN_NAME} == null){
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }
            $record = $model->record($jqGridReq->id);
            if($record == false || $record->isNew()){
               $record = $model->newRecord();
               $record->{Shop_Model_Attributes::COLUMN_ID_GROUP} = $this->getRequestParam('gid');
            }
            $record->mapArray($jqGridReq);
            $model->save($record);
            $this->infoMsg()->addMessage($this->tr('Atribut byl uložen'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               $model->delete($id);
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané atributy byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }


   public function settings(&$settings, Form &$form) {
      $fGrpViewSet = $form->addGroup('view', $this->tr('Nastavení vzhledu'));
   }
}

?>