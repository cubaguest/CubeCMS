<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class MailsAddressBook_Controller extends Controller {
   const EXPORT_CSV = 'cvs';
   const EXPORT_TXT = 'txt';
   const EXPORT_JSON = 'json';
   const EXPORT_VCARD = 'vcard';

   const NUM_ROWS_IN_TABLE = 15;
   const NUM_ROWS_IN_GRPTABLE = 5;
   const NUM_MAIL_IN_LIST = 30;

   const MSG_FILE_NAME = 'mmails_msg.cache';

   protected function init()
   {
      $this->checkControllRights();
   }
   
   public function mainController() 
   {
      $modelGrps = new MailsAddressBook_Model_Groups();
      $this->view()->groups = $modelGrps->records();
   }
   
   public function groupsController() 
   {
   }
   
   public function addressListController() {
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(MailsAddressBook_Model_Addressbook::COLUMN_MAIL);
      $modelAddresBook = new MailsAddressBook_Model_Addressbook();
      
      $idGrp = (int)$this->getRequestParam('idgrp', MailsAddressBook_Model_Groups::GROUP_ID_ALL);
      
      // search
      if ($jqGrid->request()->isSearch()) {
         switch ($jqGrid->request()->searchType()) {
            case Component_JqGrid_Request::SEARCH_EQUAL:
               $modelAddresBook->where(MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP.' = :idg AND '
                  .$jqGrid->request()->searchField().' = :str',
                  array('str' => $jqGrid->request()->searchString(), 'idg' => (int)$this->getRequestParam('idgrp', 1)));
               break;
            case Component_JqGrid_Request::SEARCH_NOT_EQUAL:
               $modelAddresBook->where(MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP.' = :idg AND '
                  .$jqGrid->request()->searchField().' != :str',
                  array('str' => $jqGrid->request()->searchString(), 'idg' => (int)$this->getRequestParam('idgrp', 1)));
               break;
            case Component_JqGrid_Request::SEARCH_NOT_CONTAIN:
               $modelAddresBook->where(MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP.' = :idg AND '
                  .$jqGrid->request()->searchField().' NOT LIKE :str',
                  array('str' => '%'.$jqGrid->request()->searchString().'%', 'idg' => (int)$this->getRequestParam('idgrp', 1)));
               break;
            case Component_JqGrid_Request::SEARCH_CONTAIN:
            default:
               $modelAddresBook->where(MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP.' = :idg AND '
                  .$jqGrid->request()->searchField().' LIKE :str',
                  array('str' => '%'.$jqGrid->request()->searchString().'%', 'idg' => (int)$this->getRequestParam('idgrp', 1)));
               break;
         }
      } else {
         // list
         if($idGrp != 0){
            $modelAddresBook->where(MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP, $idGrp);
         }
      }
      
      $jqGrid->respond()->setRecords($modelAddresBook->count());
      $items = $modelAddresBook
         ->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)
         ->joinFK(MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP)
         ->order(array($jqGrid->request()->orderField => $jqGrid->request()->order))
         ->records();
      
      // out
      foreach ($items as $mail) {
         array_push($jqGrid->respond()->rows,
               array(
                     'id' => $mail->{MailsAddressBook_Model_Addressbook::COLUMN_ID},
                     MailsAddressBook_Model_Addressbook::COLUMN_ID => $mail->{MailsAddressBook_Model_Addressbook::COLUMN_ID},
                     MailsAddressBook_Model_Addressbook::COLUMN_MAIL => $mail->{MailsAddressBook_Model_Addressbook::COLUMN_MAIL},
                     MailsAddressBook_Model_Addressbook::COLUMN_NAME => $mail->{MailsAddressBook_Model_Addressbook::COLUMN_NAME},
                     MailsAddressBook_Model_Addressbook::COLUMN_SURNAME => $mail->{MailsAddressBook_Model_Addressbook::COLUMN_SURNAME},
                     MailsAddressBook_Model_Addressbook::COLUMN_NOTE => $mail->{MailsAddressBook_Model_Addressbook::COLUMN_NOTE},
                     MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP => $mail->{MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP},
                     MailsAddressBook_Model_Groups::COLUMN_NAME => $mail->{MailsAddressBook_Model_Groups::COLUMN_NAME},
                     ));
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function groupsListController() {
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(MailsAddressBook_Model_Groups::COLUMN_ID);
      $model = new MailsAddressBook_Model_Groups();
      
      // search
      if ($jqGrid->request()->isSearch()) {
         switch ($jqGrid->request()->searchType()) {
            case Component_JqGrid_Request::SEARCH_EQUAL:
               $model->where($jqGrid->request()->searchField().' = :str',
                  array('str' => $jqGrid->request()->searchString()) );
               break;
            case Component_JqGrid_Request::SEARCH_NOT_EQUAL:
               $model->where($jqGrid->request()->searchField().' != :str',
                  array('str' => $jqGrid->request()->searchString()));
               break;
            case Component_JqGrid_Request::SEARCH_NOT_CONTAIN:
               $model->where($jqGrid->request()->searchField().' NOT LIKE :str',
                  array('str' => '%'.$jqGrid->request()->searchString().'%'));
               break;
            case Component_JqGrid_Request::SEARCH_CONTAIN:
            default:
               $model->where($jqGrid->request()->searchField().' LIKE :str',
                  array('str' => '%'.$jqGrid->request()->searchString().'%'));
               break;
         }
      } 
      
      $jqGrid->respond()->setRecords($model->count());
      $items = $model
         ->columns(array('*', 'emailsCount' => 'COUNT(tmails.'.MailsAddressBook_Model_Addressbook::COLUMN_ID.')'))
         ->join(MailsAddressBook_Model_Groups::COLUMN_ID, array('tmails' => 'MailsAddressBook_Model_Addressbook' ), MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP, array())
         ->limit(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(), $jqGrid->request()->rows)
         ->order(array($jqGrid->request()->orderField => $jqGrid->request()->order))
         ->groupBy(array(MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP))
         ->records();
      
      // out
      foreach ($items as $mail) {
         array_push($jqGrid->respond()->rows,
            array(
               'id' => $mail->{MailsAddressBook_Model_Groups::COLUMN_ID},
               MailsAddressBook_Model_Groups::COLUMN_ID => $mail->{MailsAddressBook_Model_Groups::COLUMN_ID},
               MailsAddressBook_Model_Groups::COLUMN_NAME => $mail->{MailsAddressBook_Model_Groups::COLUMN_NAME},
               MailsAddressBook_Model_Groups::COLUMN_NOTE => $mail->{MailsAddressBook_Model_Groups::COLUMN_NOTE},
               'emails_count' => $mail->emailsCount,
            ));
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function listMailsExportController() {
      $this->checkWritebleRights();
      $model = new MailsAddressBook_Model_Addressbook();
      $this->view()->mails = $model->records();
      $this->view()->type = $this->getRequest('output', 'txt');
   }

   public function editMailController() {
      $this->checkWritebleRights();
      $this->view()->allOk = false;
      $model = new MailsAddressBook_Model_Addressbook();
      $record = $model->newRecord();
      
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            $record = $model->record($jqGridReq->id);
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            // validace mailu
            $validatorMail = new Validator_EMail($jqGridReq->{MailsAddressBook_Model_Addressbook::COLUMN_MAIL});
            if (!$validatorMail->isValid()) {
               $this->errMsg()->addMessage($this->tr('Špatně zadaný e-mail'));
               return;
            }
            
            $record->{MailsAddressBook_Model_Addressbook::COLUMN_NAME} 
               = $jqGridReq->{MailsAddressBook_Model_Addressbook::COLUMN_NAME};
            $record->{MailsAddressBook_Model_Addressbook::COLUMN_SURNAME} 
               = $jqGridReq->{MailsAddressBook_Model_Addressbook::COLUMN_SURNAME};
            $record->{MailsAddressBook_Model_Addressbook::COLUMN_MAIL} 
               = $jqGridReq->{MailsAddressBook_Model_Addressbook::COLUMN_MAIL};
            $record->{MailsAddressBook_Model_Addressbook::COLUMN_NOTE} 
               = $jqGridReq->{MailsAddressBook_Model_Addressbook::COLUMN_NOTE};
            // id skupiny se vrací jako název, v tabulce je zobrazeno jako název ale z formu leze id   
            $record->{MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP} 
               = $jqGridReq->{MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP};
            
            $model->save($record);
            $this->infoMsg()->addMessage($this->tr('Konatkt byl uložen'));
            
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               $model->delete((int)$id);
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané kontakty byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }

   public function editGroupController() {
      $this->checkWritebleRights();
      $this->view()->allOk = false;
      $model = new MailsAddressBook_Model_Groups();
      $record = $model->newRecord();
      
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            $record = $model->record($jqGridReq->id);
            if((int)$jqGridReq->id == 0){
               $this->errMsg()->addMessage($this->tr('Skupinu "všechny" nelze upravovat'));
               return;
            }
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $record->{MailsAddressBook_Model_Groups::COLUMN_NAME} = $jqGridReq->{MailsAddressBook_Model_Groups::COLUMN_NAME};
            $record->{MailsAddressBook_Model_Groups::COLUMN_NOTE} = $jqGridReq->{MailsAddressBook_Model_Groups::COLUMN_NOTE};
            $model->save($record);
            
            $this->infoMsg()->addMessage($this->tr('Skupina byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            if((int)$jqGridReq->id != 0){
               foreach ($jqGridReq->getIds() as $id) {
                  $model->delete((int)$id);
               }
               $this->infoMsg()->addMessage($this->tr('Vybrané skupiny byly smazány'));
            } else {
               $this->errMsg()->addMessage($this->tr('Skupinu všechny nelze smazat'));
            }
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      } 
   }

   public function addressBookController() {
      $this->checkWritebleRights();

      $modelGrps = new MailsAddressBook_Model_Groups();
      $grps = $modelGrps->getGroups();
      $this->view()->idSelGrp = $grps[0]->{MailsAddressBook_Model_Groups::COLUMN_ID}; // kvůli načtení mailů z první skupiny

      $this->view()->linkBack = $this->link()->route()->rmParam();
   }

   public function sendMailsListController() {
      $this->checkControllRights();
      $model = new Mails_Model_SendMails();

      $compScroll = new Component_Scroll();
      $compScroll->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS,
              $model->getCount());
      $compScroll->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
              self::NUM_MAIL_IN_LIST);
      $this->view()->mails = $model->getMails($compScroll->getStartRecord(), $compScroll->getRecordsOnPage());
      $this->view()->scrollComp = $compScroll;
      $this->view()->linkBack = $this->link()->route()->rmParam();
   }

   public function searchMailController() {
      $model = new MailsAddressBook_Model_Addressbook();

      $this->view()->mails = $model->searchMail($this->getRequestParam('q'));
   }
   
   public function toolsController()
   {
      $this->checkWritebleRights();
      
      $modelGrps = new MailsAddressBook_Model_Groups();
      $grps = $modelGrps->getGroups();
      
      /* IMPORT */
      $formImport = new Form('mails_import_');
      $formImportGrpBasic = $formImport->addGroup('basic', $this->tr('Základní'));
      $formImportGrpAdv = $formImport->addGroup('advanced', $this->tr('Pokročilé'));

      $eFile = new Form_Element_File('file', $this->tr('Soubor (*.csv)'));
      $eFile->addValidation(new Form_Validator_FileExtension('csv'));
      $formImport->addElement($eFile, $formImportGrpBasic);


      $eGroup = new Form_Element_Select('group', $this->tr('Skupina'));
      $formImport->addElement($eGroup, $formImportGrpBasic);

      $eImport = new Form_Element_Submit('import', $this->tr('Nahrát'));
      $formImport->addElement($eImport, $formImportGrpBasic);

      foreach ($grps as $grp) {
         $formImport->group->setOptions(array($grp->{MailsAddressBook_Model_Groups::COLUMN_NAME} => $grp->{MailsAddressBook_Model_Groups::COLUMN_ID}), true);
      }

      if ($formImport->isValid()) {
         $modelMails = new MailsAddressBook_Model_Addressbook();
         $file = $formImport->file->createFileObject('Filesystem_File_Text');
         $mails = $file->getContent();
         
         $mailsArr = explode("\n", $mails);
         $numImports = 0;
         foreach ($mailsArr as $mail) {
            if(empty($mail)) continue;
            
            $rec = $modelMails->newRecord();
            $rec->{MailsAddressBook_Model_Addressbook::COLUMN_MAIL} = $mail;
            $rec->{MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP} = $formImport->group->getValues();
            
            $modelMails->save($rec);
            $numImports++;
         }
         $this->infoMsg()->addMessage(sprintf($this->tr('Adresy byly importovány. Celkem bylo importováno %s e-mailů.'),$numImports));
         $this->link()->reload();
      }
      $this->view()->formImport = $formImport;

      /* EXPORT */
      $formExport = new Form('mails_export_');
      $formExportGrpBasic = $formExport->addGroup('basic',  $this->tr('Základní'));
      $formExportGrpAdv = $formExport->addGroup('advanced',  $this->tr('Pokročilé'));

      $eGroup = new Form_Element_Select('group', $this->tr('Skupina adresáře'));

      $eGroup->setOptions(array($this->tr('Vše') => MailsAddressBook_Model_Groups::GROUP_ID_ALL), true);
      foreach ($grps as $grp) {
         $eGroup->setOptions(array($grp->{MailsAddressBook_Model_Groups::COLUMN_NAME} => $grp->{MailsAddressBook_Model_Groups::COLUMN_ID}), true);
      }
      $formExport->addElement($eGroup, $formExportGrpBasic);


      $eSubmit = new Form_Element_Submit('export', $this->tr('Export'));
      $formExport->addElement($eSubmit);

      $eAddHeaders = new Form_Element_Checkbox('addheader', $this->tr('Přidat názvy sloupců'));
      $eAddHeaders->setValues(true);
      $formExport->addElement($eAddHeaders, $formExportGrpAdv);

      $eExportType = new Form_Element_Select('type', $this->tr('Typ'));
      $eExportType->setOptions(array('Excel (xls)' => 'xls', 'Excel (csv)' => 'csv', 'Prostý text (txt)' => 'txt'));
      $formExport->addElement($eExportType, $formExportGrpAdv);

      $eCsvSep = new Form_Element_Text('csvsep', $this->tr('Oddělovač hodnot'));
      $eCsvSep->setSubLabel($this->tr('Pouze csv formát'));
      $eCsvSep->setValues(',');
      $formExport->addElement($eCsvSep, $formExportGrpAdv);

      if($formExport->isSend() && $formExport->type->getValues() == "csv"){
         $formExport->csvsep->addValidation(new Form_Validator_NotEmpty());
      }
      if($formExport->isValid()){
         $modelMails = new MailsAddressBook_Model_Addressbook();
         $idg = $formExport->group->getValues();
         if($idg != 0){
            $modelMails->where(MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP." = :idg", array( 'idg' => $idg ));
         }
         
         $mails = $modelMails->records();
         switch ($formExport->type->getValues()) {
            case 'csv':
               $comCsv = new Component_CSV();
               $comCsv->setConfig(Component_CSV::CFG_CELL_SEPARATOR, $formExport->csvsep->getValues());
               $comCsv->setConfig(Component_CSV::CFG_FLUSH_FILE, 'mails-'.date('Y-m-d').'.csv');
               if($formExport->addheader->getValues() == true){
                  $comCsv->setCellLabels(array($this->tr('Jméno'),$this->tr('Přijmení'),$this->tr('E-mail'),$this->tr('Poznámka')));
               }
               foreach ($mails as $mail) {
                  $comCsv->addRow(array(
                     $mail->{MailsAddressBook_Model_Addressbook::COLUMN_NAME},
                     $mail->{MailsAddressBook_Model_Addressbook::COLUMN_SURNAME},
                     $mail->{MailsAddressBook_Model_Addressbook::COLUMN_MAIL},
                     $mail->{MailsAddressBook_Model_Addressbook::COLUMN_NOTE}
                  ));
               }
               $comCsv->flush();
               break;
            case 'xls':
               include_once AppCore::getAppLibDir().'lib/nonvve/phpexcel/PHPExcel.php';
               $excelDoc = new PHPExcel();
               $excelDoc->setActiveSheetIndex(0);
               $list = $excelDoc->getActiveSheet();
               $currLine = 1;
               
               $list->getColumnDimension('A')->setWidth(10);
               $list->getColumnDimension('B')->setWidth(10);
               $list->getColumnDimension('C')->setWidth(45);
               
               if($formExport->addheader->getValues() == true){
                  $list->getStyle('A1:D1')->getFont()->setBold(true);
                  
                  $list->setCellValueByColumnAndRow(0, $currLine, $this->tr('Jméno'));
                  $list->setCellValueByColumnAndRow(1, $currLine, $this->tr('Přijmení'));
                  $list->setCellValueByColumnAndRow(2, $currLine, $this->tr('E-mail'));
                  $list->setCellValueByColumnAndRow(3, $currLine, $this->tr('Poznámka'));
                  $currLine++;
               }
               
               foreach ($mails as $mail) {
                  $list->setCellValueByColumnAndRow(0, $currLine, $mail->{MailsAddressBook_Model_Addressbook::COLUMN_NAME});
                  $list->setCellValueByColumnAndRow(1, $currLine, $mail->{MailsAddressBook_Model_Addressbook::COLUMN_SURNAME});
                  $list->setCellValueByColumnAndRow(2, $currLine, $mail->{MailsAddressBook_Model_Addressbook::COLUMN_MAIL});
                  $list->setCellValueByColumnAndRow(3, $currLine, $mail->{MailsAddressBook_Model_Addressbook::COLUMN_NOTE});
                  $currLine++;
               }
               $newExcelWriter = new PHPExcel_Writer_Excel5($excelDoc);
//               $newExcelWriter->save(AppCore::getAppCacheDir()."mail_export.xls");
               Template_Output::factory('xls');
               Template_Output::setDownload('mails-'.date('Y-m-d').'.xls');
               Template_Output::sendHeaders();
               header('Content-type: application/vnd.ms-excel');
               $newExcelWriter->save('php://output');
               exit();
               break;
            case 'txt':
               $buffer = null;
               foreach ($mails as $mail) {
                  $buffer .= $mail->{MailsAddressBook_Model_Addressbook::COLUMN_MAIL}."\r\n";
               }
               Template_Output::factory('txt');
               Template_Output::setDownload('mails-'.date('Y-m-d').'.txt');
               Template_Output::sendHeaders();
               echo $buffer;
               flush();
               exit();
               break;
            default:
               $this->errMsg()->addMessage($this->tr('Nepodporovaný typ exportu'));
               break;
         }
      }
      $this->view()->formExport = $formExport;

      $formRemoveDuplicity = new Form('remduplicity');
      $eSubmit = new Form_Element_Submit('remove', $this->tr('Odstranit'));
      $formRemoveDuplicity->addElement($eSubmit);

      if($formRemoveDuplicity->isValid()){

      }
      $this->view()->formRemoveDuplicity = $formRemoveDuplicity;
   }

   /**
    * Metoda pro nastavení modulu
    */
   public function settingsController(&$settings, Form &$form) {
      $form->addGroup('server', 'Nastavení serveru');
   }

}
?>
