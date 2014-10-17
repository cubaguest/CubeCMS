<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class AdminIPBlock_Controller extends Controller {
   public function mainController() 
   {
      $this->checkControllRights();
   }
   
   public function listIPController() {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Model_IPBlocks::COLUMN_ID);
      $model = new Model_IPBlocks();
      $model->order(array($jqGrid->request()->orderField => $jqGrid->request()->order));
      // search
      if ($jqGrid->request()->isSearch()) {
         switch ($jqGrid->request()->searchType()) {
            case Component_JqGrid_Request::SEARCH_EQUAL:
               $model->where(Model_IPBlocks::COLUMN_ID.' = :str',
                  array('str' => inet_pton($jqGrid->request()->searchString())));
               break;
            case Component_JqGrid_Request::SEARCH_CONTAIN:
            default:
               $model->where(Model_IPBlocks::COLUMN_ID.' LIKE :str',
                  array('str' => '%'.inet_pton($jqGrid->request()->searchString()).'%'));
               break;
         }
      } 
      
      $jqGrid->respond()->setRecords($model->count());
      $ips = $model->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)->records();
      // out
      if($ips){
         foreach ($ips as $ip) {
            array_push($jqGrid->respond()->rows, array('id' => inet_ntop($ip->getPK()),
                'cell' => array(
                    $ip->getIP(),
                    Utils_DateTime::fdate('%x %X', $ip->{Model_IPBlocks::COLUMN_TIME}),
                    )));
         }
      }
      $this->view()->respond = $jqGrid->respond();
   }
   
   public function editIPController() {
      $this->checkWritebleRights();
      $model = new Model_IPBlocks();
      $this->view()->allOk = false;
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            $ip = $jqGridReq->{Model_IPBlocks::COLUMN_ID};
            // kontrola položek
            if ($ip == null) {
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) == false &&
                filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) == false) {
               $this->errMsg()->addMessage($this->tr('IP adresa není validní'));
               return;
            }

            // kontrola obsazení
            if (Model_IPBlocks::isBlocked($ip)) {
               $this->errMsg()->addMessage($this->tr('IP adresa již exituje'));
               return;
            }
            
            if($jqGridReq->id == null){
               $record = $model->newRecord();
               $record->{Model_IPBlocks::COLUMN_ID} = inet_pton($ip);
               $record->save();
            } else {
               $record = $model->record(inet_pton($jqGridReq->id));
               $model->where(Model_IPBlocks::COLUMN_ID." = :idp", array('idp' => inet_pton($jqGridReq->id)))
                   ->update(array(Model_IPBlocks::COLUMN_ID => inet_pton($ip)));
            }
                

            $this->infoMsg()->addMessage($this->tr('IP adresa byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            $ips = $jqGridReq->getIds();
            foreach ($ips as $ip) {
               $id = inet_pton($ip);
               $model->delete($id);
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané IP adresy byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }
}
