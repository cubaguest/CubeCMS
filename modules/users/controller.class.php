<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Users_Controller extends Controller {
   /**
    * Minimální délka hesla
    * @var integer
    */
   const PASSWORD_MIN_LENGHT = 5;

   public function mainController() {
      $this->checkControllRights();
      $model = new Model_Users();

      $this->view()->groups = $model->getGroups();

//      /*
//       * Odstranění uživatele
//      */
//      $formRemove = new Form('user_');
//      $elemId = new Form_Element_Hidden('id');
//      $formRemove->addElement($elemId);
//      $elemSub = new Form_Element_SubmitImage('remove', $this->_('Odstranit'));
//      $formRemove->addElement($elemSub);
//
//      if($formRemove->isValid()) {
//         $user = $model->getUserById($formRemove->id->getValues());
//         $model->deleteUser($formRemove->id->getValues());
//         $this->infoMsg()->addMessage($this->_(sprintf('Uživatel "%s" byl smazán', $user->{Model_Users::COLUMN_USERNAME})));
//         $this->link()->reload();
//      }
//
//      /*
//       * Odstranění skupiny
//      */
//      $formRemoveGr = new Form('group_');
//      $elemId = new Form_Element_Hidden('id');
//      $formRemoveGr->addElement($elemId);
//      $elemSub = new Form_Element_SubmitImage('remove', $this->_('Odstranit'));
//      $formRemoveGr->addElement($elemSub);
//
//      if($formRemoveGr->isValid()) {
//         $group = $model->getGroupById($formRemoveGr->id->getValues());
//         $model->deleteGroup($formRemoveGr->id->getValues());
//         // smazání všech práv k dané skupině
//         $rModel = new Model_Rights();
//         $rModel->deleteRightsByGrID($formRemoveGr->id->getValues());
//
//         /**
//          * Odstranění gloválních dat
//          */
//         if(VVE_USE_GLOBAL_ACCOUNTS === true) {
//            $array = explode(';', VVE_USE_GLOBAL_ACCOUNTS_TB_PREFIXES);
//            if(!empty ($array)) {
//               foreach ($array as $tblPrefix) {
//                  $rModel->deleteRightsByGrID($formRemoveGr->id->getValues(), $tblPrefix);
//               }
//            }
//         }
//
//         $this->infoMsg()->addMessage($this->_(sprintf('Skupina "%s" byla smazána', $group->{Model_Users::COLUMN_GROUP_NAME})));
//         $this->link()->reload();
//      }
//
//      /*
//       * Změna uživatelského statusu
//      */
//      $formEnable = new Form('userstatus_');
//
//      $elemId = new Form_Element_Hidden('id');
//      $formEnable->addElement($elemId);
//      $elemStat = new Form_Element_Hidden('status');
//      $formEnable->addElement($elemStat);
//
//      $elemSub = new Form_Element_SubmitImage('change', $this->_('Změnit'));
//      $formEnable->addElement($elemSub);
//
//      if($formEnable->isValid()) {
//         if($formEnable->status->getValues() == 'enable') {
//            $model->enableUser($formEnable->id->getValues());
//            $this->infoMsg()->addMessage($this->_('Uživatel byl aktivován'));
//         } else if($formEnable->status->getValues() == 'disable') {
//            $model->disableUser($formEnable->id->getValues());
//            $this->infoMsg()->addMessage($this->_('Uživatel byl deaktivován'));
//         }
//         $this->link()->reload();
//      }
   }

   public function groupsController() {
      $this->checkControllRights();

   }


   public function usersListController() {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Model_Users::COLUMN_ID);
      $modelUsers = new Model_Users();
      $modelUsers->order(array($jqGrid->request()->orderField => $jqGrid->request()->order))->join('tgroups');
      // search
      if ($jqGrid->request()->isSearch()) {
         switch ($jqGrid->request()->searchType()) {
            case Component_JqGrid_Request::SEARCH_EQUAL:
               $modelUsers->where(Model_Users::COLUMN_GROUP_ID.' = :idg AND '.$jqGrid->request()->searchField().' = :str',
                  array('str' => $jqGrid->request()->searchString(), 'idg' => (int)$this->getRequestParam('idgrp', 1)));
               break;
            case Component_JqGrid_Request::SEARCH_NOT_EQUAL:
               $modelUsers->where(Model_Users::COLUMN_GROUP_ID.' = :idg AND '.$jqGrid->request()->searchField().' != :str',
                  array('str' => $jqGrid->request()->searchString(), 'idg' => (int)$this->getRequestParam('idgrp', 1)));
               break;
            case Component_JqGrid_Request::SEARCH_NOT_CONTAIN:
               $modelUsers->where(Model_Users::COLUMN_GROUP_ID.' = :idg AND '.$jqGrid->request()->searchField().' NOT LIKE :str',
                  array('str' => '%'.$jqGrid->request()->searchString().'%', 'idg' => (int)$this->getRequestParam('idgrp', 1)));
               break;
            case Component_JqGrid_Request::SEARCH_CONTAIN:
            default:
               $modelUsers->where(Model_Users::COLUMN_GROUP_ID.' = :idg AND '.$jqGrid->request()->searchField().' LIKE :str',
                  array('str' => '%'.$jqGrid->request()->searchString().'%', 'idg' => (int)$this->getRequestParam('idgrp', 1)));
               break;
         }

//         $jqGrid->respond()->setRecords($modelUsers->count());
//         $users = $modelUsers->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)
//            ->records();
      } else { // list
         $modelUsers->where(Model_Users::COLUMN_GROUP_ID, (int)$this->getRequestParam('idgrp', 1));
      }
      $jqGrid->respond()->setRecords($modelUsers->count());
      $users = $modelUsers->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)->records();
      // out
      foreach ($users as $user) {
         if($user->{Model_Users::COLUMN_BLOCKED} == 1) $blstr = true;
         else $blstr = false;
         array_push($jqGrid->respond()->rows, array('id' => $user->{Model_Users::COLUMN_ID},
             'cell' => array(
                 $user->{Model_Users::COLUMN_ID},
                 $user->{Model_Users::COLUMN_NAME},
                 $user->{Model_Users::COLUMN_SURNAME},
                 $user->{Model_Users::COLUMN_USERNAME},
                 null, // pass
                 $user->{Model_Users::COLUMN_MAIL},
                 $blstr,
                 $user->{Model_Users::COLUMN_NOTE},
                 $user->{Model_Groups::COLUMN_NAME}
                 )));
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function groupsListController() {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Model_Users::COLUMN_ID);
      $modelGrps = new Model_Groups();
      // search
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
         $jqGrid->respond()->setRecords($modelGrps->count());
         $groups = $modelGrps->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)
            ->order(array($jqGrid->request()->orderField => $jqGrid->request()->order))
            ->records();
      }
      // out
      foreach ($groups as $grp) {
         array_push($jqGrid->respond()->rows, array('id' => $grp->{Model_Groups::COLUMN_ID},
             'cell' => array(
                 $grp->{Model_Groups::COLUMN_ID},
                 $grp->{Model_Groups::COLUMN_NAME},
                 $grp->{Model_Groups::COLUMN_LABEL}
                 )));
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function editUserController() {
      $this->checkWritebleRights();
      $model = new Model_Users();
      $this->view()->allOk = false;
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
            if($jqGridReq->{Model_Users::COLUMN_PASSWORD} == null){
               $this->errMsg()->addMessage($this->_('Při přidání uživatele heslo musí být zadáno'));
               return;
            }
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // kontrola položek
            if($jqGridReq->{Model_Users::COLUMN_USERNAME} == null
               OR $jqGridReq->{Model_Users::COLUMN_NAME} == null
               OR $jqGridReq->{Model_Users::COLUMN_SURNAME} == null){
               $this->errMsg()->addMessage($this->_('Nebyly zadány všechny povinné údaje'));
               return;
            }
            // validace názvu
            if(!preg_match('/^[a-zA-Z0-9@._-]+$/', $jqGridReq->{Model_Users::COLUMN_USERNAME})){
               $this->errMsg()->addMessage($this->_('Už. jméno obsahuje nepovolené znaky<br /> (mezera, diakritika)'));
               return;
            }
            // validace mailu
            $validatorMail = new Validator_EMail($jqGridReq->{Model_Users::COLUMN_MAIL});
            if ($jqGridReq->{Model_Users::COLUMN_MAIL} != null AND !$validatorMail->isValid()) {
               $this->errMsg()->addMessage($this->_('Špatně zadaný e-mail'));
               return;
            }
            // validace username
            $record = $model->record($jqGridReq->id);
            if($record->{Model_Users::COLUMN_USERNAME} != $jqGridReq->{Model_Users::COLUMN_USERNAME} AND $this->userExist($jqGridReq->{Model_Users::COLUMN_USERNAME})){
               $this->errMsg()->addMessage($this->_('Uživatelské jméno je již obsazeno'));
               return;
            }
            if($jqGridReq->{Model_Users::COLUMN_PASSWORD} == null){
               unset ($jqGridReq->{Model_Users::COLUMN_PASSWORD});
            } else {
               $jqGridReq->{Model_Users::COLUMN_PASSWORD} = Auth::cryptPassword($jqGridReq->{Model_Users::COLUMN_PASSWORD});
            }
            $record->mapArray($jqGridReq);
            $model->save($record);
            $this->infoMsg()->addMessage($this->_('Uživatel byl uložen'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               $model->delete($id);
            }
            $this->infoMsg()->addMessage($this->_('Vybraní uživatelé byli smazáni'));
            break;
         default:
            $this->errMsg()->addMessage($this->_('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }

   public function blockUserController() {
      $this->checkWritebleRights();
      $model = new Model_Users();
      $record = $model->record($this->getRequestParam('id'));
      if($record != false){
         if($this->getRequestParam('blocked') == 'true'){
            $record->{Model_Users::COLUMN_BLOCKED} = true;
         } else {
            $record->{Model_Users::COLUMN_BLOCKED} = false;
         }
         $model->save($record);
      }
      $this->infoMsg()->addMessage($this->_('Status byl změněn'));
   }

   public function editGroupController() {
      $this->checkWritebleRights();
      $model = new Model_Groups();
      $this->view()->allOk = false;
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // kontrola položek
            if($jqGridReq->{Model_Groups::COLUMN_NAME} == null){
               $this->errMsg()->addMessage($this->_('Nebyly zadány všechny povinné údaje'));
               return;
            }
            // validace názvu
            if(!preg_match('/^[a-zA-Z0-9_-]+$/', $jqGridReq->{Model_Groups::COLUMN_NAME})){
               $this->errMsg()->addMessage($this->_('Název obsahuje nepovolené znaky<br /> (pouze: a-z; A-Z; 0-9; _; -)'));
               return;
            }
            $record = $model->record($jqGridReq->id);
            $record->mapArray($jqGridReq);
            $model->save($record);
            $this->infoMsg()->addMessage($this->_('Skpina byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               $model->delete($id);
            }
            $this->infoMsg()->addMessage($this->_('Vybrané skupiny byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->_('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }

   public static function userExist($username) {
      $modelU = new Model_Users();
      
      return (bool)$modelU->where(Model_Users::COLUMN_USERNAME,$username)->record();
   }

   public static function createUniqueUsername($username) {

   }

}

?>