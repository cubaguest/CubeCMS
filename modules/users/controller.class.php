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

      $this->view()->groups = $this->getAllowedGroups();
   }

   public function groupsController() {
      $this->checkControllRights();
      $model = new Model_Sites();
      $sites = array();
      $allowedSite = Auth::getUserSites();
      foreach ($model->records() as $site) {
         if(isset ($allowedSite[$site->{Model_Sites::COLUMN_DOMAIN}]) OR empty ($allowedSite)){
            $sites[$site->{Model_Sites::COLUMN_ID}] = $site->{Model_Sites::COLUMN_DOMAIN};
         }
      }
      $this->view()->sites = $sites;
   }

   public function usersListController() {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Model_Users::COLUMN_ID);
      $modelUsers = new Model_Users();
      $modelUsers->order(array($jqGrid->request()->orderField => $jqGrid->request()->order))->joinFK(Model_Users::COLUMN_ID_GROUP);
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
         $groups = $this->getAllowedGroups($jqGrid);
      }

      // out
      foreach ($groups as $grp) {
         array_push($jqGrid->respond()->rows, array('id' => $grp->{Model_Groups::COLUMN_ID},
             'cell' => array(
                 $grp->{Model_Groups::COLUMN_ID},
                 $grp->{Model_Groups::COLUMN_NAME},
                 $grp->{Model_Groups::COLUMN_LABEL},
                 $grp->{Model_Groups::COLUMN_IS_ADMIN},
                 $grp->domains,
                 )));
      }
      $this->view()->respond = $jqGrid->respond();
   }
   
   private function getAllowedGroups($jqGrid = null)
   {
      $userSites = Auth::getUserSites();

      $modelSubSites = new Model_Sites();
      $sites = $modelSubSites->join(Model_Sites::COLUMN_ID, 'Model_SitesGroups', Model_SitesGroups::COLUMN_ID_SITE)->records();
      if($sites == false){
         throw new UnexpectedValueException('Nebyly nalezeny poddomény');
      }
      
      $allowedGroups = array();
      foreach ($sites as $site) {
         if(!isset($allowedGroups[$site->{Model_SitesGroups::COLUMN_ID_GROUP}])){
            $allowedGroups[$site->{Model_SitesGroups::COLUMN_ID_GROUP}] = array();
         }
         if(empty ($userSites) OR in_array($site->{Model_Sites::COLUMN_ID}, $userSites)){
            array_push($allowedGroups[$site->{Model_SitesGroups::COLUMN_ID_GROUP}], $site->{Model_Sites::COLUMN_DOMAIN});
         } else {
            unset($allowedGroups[$site->{Model_SitesGroups::COLUMN_ID_GROUP}]);
         }
      }
      // skupiny s připojeným modelem subsites
      $modelSG = new Model_Groups();
      if(!empty ($userSites)){
         $modelSG->where(Model_Groups::COLUMN_ID.' IN ('.implode(',', array_keys($allowedGroups)).')', array());
      }
/*      SELECT `t_grp`.`id_group`, `t_grp`.`name` AS `gname`, `t_grp`.`label`, `t_grp`.`admin`,
GROUP_CONCAT(ts.domain) as domains
FROM `cube_cms`.`vypecky_groups` AS t_grp
LEFT JOIN `cube_cms`.`vypecky_sites_groups` AS tsg ON `tsg`.`id_group` = t_grp.id_group
LEFT JOIN `cube_cms`.`vypecky_sites` AS ts ON `ts`.`id_site` = tsg.id_site
GROUP BY `t_grp`.`id_group`*/
      $modelSG->columns(array('*', 'domains' => 'GROUP_CONCAT('.Model_Sites::COLUMN_DOMAIN.')'))
      ->join(Model_Groups::COLUMN_ID, array('tsg' => 'Model_SitesGroups'), Model_SitesGroups::COLUMN_ID_GROUP, false)
      ->join(array('tsg' => Model_SitesGroups::COLUMN_ID_SITE), 'Model_Sites', Model_Sites::COLUMN_ID, false)
      ->groupBy(array(Model_Groups::COLUMN_ID));
      
      if($jqGrid instanceof Component_JqGrid ){
         $jqGrid->respond()->setRecords($modelSG->count());
         $modelSG->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)
            ->order(array($jqGrid->request()->orderField => $jqGrid->request()->order));
      }
      return $modelSG->records();
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
               $this->errMsg()->addMessage($this->tr('Při přidání uživatele heslo musí být zadáno'));
               return;
            }
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // kontrola položek
            if($jqGridReq->{Model_Users::COLUMN_USERNAME} == null
               OR $jqGridReq->{Model_Users::COLUMN_NAME} == null
               OR $jqGridReq->{Model_Users::COLUMN_SURNAME} == null){
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }
            // validace názvu
            if(!preg_match('/^[a-zA-Z0-9@._-]+$/', $jqGridReq->{Model_Users::COLUMN_USERNAME})){
               $this->errMsg()->addMessage($this->tr('Už. jméno obsahuje nepovolené znaky<br /> (mezera, diakritika)'));
               return;
            }
            // validace mailu
            $validatorMail = new Validator_EMail($jqGridReq->{Model_Users::COLUMN_MAIL});
            if ($jqGridReq->{Model_Users::COLUMN_MAIL} != null AND !$validatorMail->isValid()) {
               $this->errMsg()->addMessage($this->tr('Špatně zadaný e-mail'));
               return;
            }
            // validace username
            $record = $model->record($jqGridReq->id);
            if($record->{Model_Users::COLUMN_USERNAME} != $jqGridReq->{Model_Users::COLUMN_USERNAME} AND $this->userExist($jqGridReq->{Model_Users::COLUMN_USERNAME})){
               $this->errMsg()->addMessage($this->tr('Uživatelské jméno je již obsazeno'));
               return;
            }
            if($jqGridReq->{Model_Users::COLUMN_PASSWORD} == null){
               unset ($jqGridReq->{Model_Users::COLUMN_PASSWORD});
            } else {
               $jqGridReq->{Model_Users::COLUMN_PASSWORD} = Auth::cryptPassword($jqGridReq->{Model_Users::COLUMN_PASSWORD});
            }
            $record->mapArray($jqGridReq);
            $model->save($record);
            $this->infoMsg()->addMessage($this->tr('Uživatel byl uložen'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               $model->delete($id);
            }
            $this->infoMsg()->addMessage($this->tr('Vybraní uživatelé byli smazáni'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
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
      $this->infoMsg()->addMessage($this->tr('Status byl změněn'));
   }

   public function editGroupController() {
      $this->checkWritebleRights();
      $model = new Model_Groups();
      $modelR = new Model_Rights();
      $modelSitesGroups = new Model_SitesGroups();
      $this->view()->allOk = false;
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // kontrola položek
            if($jqGridReq->{Model_Groups::COLUMN_NAME} == null){
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }
            // validace názvu
            if(!preg_match('/^[a-zA-Z0-9_-]+$/', $jqGridReq->{Model_Groups::COLUMN_NAME})){
               $this->errMsg()->addMessage($this->tr('Název obsahuje nepovolené znaky<br /> (pouze: a-z; A-Z; 0-9; _; -)'));
               return;
            }
            $record = $model->record($jqGridReq->id);
            $record->mapArray($jqGridReq);
            $grpId = $model->save($record);

            // smazání starých skupin <--> site
            $modelSitesGroups->where(Model_SitesGroups::COLUMN_ID_GROUP.' = :idg', array('idg' => $jqGridReq->id))->delete(); // SECURITY ISSUE pokud vloží nesprávníé id vytvoří superadmin

            $userSites = Auth::getUserSites();
            
            if($jqGridReq->sites != null){
               $subdomainsIds = explode(',',$jqGridReq->sites);

               $recordSA = $modelSitesGroups->newRecord();
               $recordSA->{Model_SitesGroups::COLUMN_ID_GROUP} = $grpId;
               foreach($subdomainsIds as $sId) {
                  $recordSA->{Model_SitesGroups::COLUMN_ID_SITE} = $sId;
                  $modelSitesGroups->save($recordSA);
//                  $this->infoMsg()->addMessage('saved id '.$sId);
               }
            } else if(!empty ($userSites)) {
               $recordSA = $modelSitesGroups->newRecord();
               $recordSA->{Model_SitesGroups::COLUMN_ID_GROUP} = $grpId;
               foreach ($userSites as $domain => $ids) {
                  $recordSA->{Model_SitesGroups::COLUMN_ID_SITE} = $ids;
                  $modelSitesGroups->save($recordSA);
               }
            }
            
            $this->infoMsg()->addMessage($this->tr('Skpina byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            $modelU = new Model_Users();
            foreach ($jqGridReq->getIds() as $id) {
               $model->delete($id);
               $modelR->where(Model_Rights::COLUMN_ID_GROUP, $id)->delete(); // delete all rights
               $modelSitesGroups->where(Model_SitesGroups::COLUMN_ID_GROUP.' = :idg', array('idg' => $id))->delete(); // smazání vazeb na subdomény a skupiny
               // Smazání uživatelů
               $modelU->where(Model_Users::COLUMN_GROUP_ID.' = :idg', array('idg' => $id))->delete();
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané skupiny byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
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