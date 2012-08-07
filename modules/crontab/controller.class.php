<?php
class CronTab_Controller extends Controller {
   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
   
      $modelM = new Model_Module();
      
      
      $mTemp = $modelM->records();
      $modules = array('u' => $this->tr('Url adresa'));
      foreach ($mTemp as $m) {
         $modules[$m->{Model_Module::COLUMN_NAME}] = $m->{Model_Module::COLUMN_NAME};
      }
      
      $this->view()->modules = $modules;
      
      $this->view()->periods = array(
            Model_AutoRun::PERIOD_HOURLY => $this->tr('Hodinově'),
            Model_AutoRun::PERIOD_DAILY => $this->tr('Denně'),
            Model_AutoRun::PERIOD_WEEKLY => $this->tr('Týdně'),
            Model_AutoRun::PERIOD_MONTHLY => $this->tr('Měsíčně'),
            Model_AutoRun::PERIOD_YEARLY => $this->tr('Ročně'),
            );
   }

   public function tasksListController() {
      $this->checkReadableRights();
      
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Model_AutoRun::COLUMN_ID);
      // search
      
      $model = new Model_AutoRun();

      $order = Model_ORM::ORDER_ASC;
      if($jqGrid->request()->order == 'desc'){
         $order = Model_ORM::ORDER_DESC;
      }
      
      switch ($jqGrid->request()->orderField) {
         case Model_AutoRun::COLUMN_PERIOD:
            $model->order(array(Model_AutoRun::COLUMN_PERIOD => $order));
            break;
         case Model_AutoRun::COLUMN_MODULE_NAME:
            $model->order(array(Model_AutoRun::COLUMN_MODULE_NAME => $order));
            break;
         case Model_AutoRun::COLUMN_URL:
            $model->order(array(Model_AutoRun::COLUMN_URL => $order));
            break;
         case Model_AutoRun::COLUMN_ID:
         default:
            $model->order(array(Model_AutoRun::COLUMN_ID => $order));
            break;
      }
      
      if ($jqGrid->request()->isSearch()) {
         switch ($jqGrid->request()->searchType()) {
            case Component_JqGrid_Request::SEARCH_EQUAL:
               $model->where($jqGrid->request()->searchField().' = :str',
               array('str' => $jqGrid->request()->searchString() ));
               break;
            case Component_JqGrid_Request::SEARCH_NOT_EQUAL:
               $model->where($jqGrid->request()->searchField().' != :str',
               array('str' => $jqGrid->request()->searchString() ));
               break;
            case Component_JqGrid_Request::SEARCH_NOT_CONTAIN:
               $model->where($jqGrid->request()->searchField().' NOT LIKE :str',
               array('str' => '%'.$jqGrid->request()->searchString().'%' ));
               break;
            case Component_JqGrid_Request::SEARCH_CONTAIN:
            default:
               $model->where($jqGrid->request()->searchField().' LIKE :str',
               array('str' => '%'.$jqGrid->request()->searchString().'%' ));
               break;
         }     
      }
      
      $jqGrid->respond()->setRecords($model->count());
      
      $fromRow = ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage();
      
      $tasks = $model->limit($fromRow, $jqGrid->request()->rows)->records(PDO::FETCH_OBJ);
      // out
      foreach ($tasks as $task) {
         array_push($jqGrid->respond()->rows, 
            array(
                  'id' => $task->{Model_AutoRun::COLUMN_ID},
                  Model_AutoRun::COLUMN_ID => $task->{Model_AutoRun::COLUMN_ID},
                  Model_AutoRun::COLUMN_MODULE_NAME => $task->{Model_AutoRun::COLUMN_MODULE_NAME},
                  Model_AutoRun::COLUMN_URL => $task->{Model_AutoRun::COLUMN_URL},
                  Model_AutoRun::COLUMN_PERIOD => $task->{Model_AutoRun::COLUMN_PERIOD}
                     ));
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function taskEditController() {
      $this->checkWritebleRights();
      $model = new Model_AutoRun();
      $this->view()->allOk = false;
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            $record = $model->record($jqGridReq->id);
            if($jqGridReq->{Model_AutoRun::COLUMN_MODULE_NAME} == "u"){
               $jqGridReq->{Model_AutoRun::COLUMN_MODULE_NAME} = null;
            }
            $record->mapArray($jqGridReq);
            $grpId = $model->save($record);
            
            $this->infoMsg()->addMessage($this->tr('Úloha byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               $tast = $model->record($id);
               $model->delete($id);
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané úlohy byly smazány'));
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
?>