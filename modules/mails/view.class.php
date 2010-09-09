<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Mails_View extends View {
	public function mainView() {
      $this->template()->addTplFile('main.phtml');
      // toolbox
//      if($this->rights()->isWritable()){
//         $toolbox = new Template_Toolbox();
//         $toolbox->addTool('add_mail',$this->_('Přidat mail'),
//                 $this->link()->route('add_address'), $this->_('Přidat mail do knihy adres'), 'mail_add.png');
//         $this->toolbox = $toolbox;
//      }
      $this->page = 'main';
	}

   public function listMailsView() {
      $this->template()->addTplFile('main.phtml');
   }

   public function listMailsExportView() {
      $result = null;
      switch ($this->type) {
         case Mails_Controller::EXPORT_CSV:
            $csv = new Component_CSV();
            $csv->setCellLabels(array('email', 'jméno', 'přijmení'));
            foreach ($this->mails as $mail) {
               $csv->addRow(array($mail['mail'], $mail['name'], $mail['surname']));
            }
            Template_Output::setOutputType('csv');
            Template_Output::setDownload('list.csv');
            Template_Output::sendHeaders();
            $csv->flush();
            break;
         case Mails_Controller::EXPORT_JSON:
            $result = json_encode($this->mails);
            break;
         case Mails_Controller::EXPORT_VCARD:
         case Mails_Controller::EXPORT_TXT:
         default:
            Template_Output::setOutputType('txt');
//            Template_Output::setDownload('list.txt');
            Template_Output::sendHeaders();
            foreach ($this->mails as $mail) {
               $result .= $mail->{Mails_Model_Addressbook::COLUMN_MAIL};
               if($mail->{Mails_Model_Addressbook::COLUMN_NAME} != null) $result .= ' '.$mail->{Mails_Model_Addressbook::COLUMN_NAME};
               if($mail->{Mails_Model_Addressbook::COLUMN_SURNAME} != null) $result .= ' '.$mail->{Mails_Model_Addressbook::COLUMN_SURNAME};
               if($mail->{Mails_Model_Addressbook::COLUMN_NOTE} != null) $result .= ' '.$mail->{Mails_Model_Addressbook::COLUMN_NOTE};
               $result .= "\n";
            }
            break;
      }
      echo($result);
      flush();
      exit();
   }

   public function addressBookView() {
      $this->template()->addTplFile('addressbook.phtml');
      $this->page = 'addressbook';
   }

   public function sendMailsListView() {
      $this->template()->addTplFile('list_mails.phtml');
      $this->page = 'sendMailsList';
   }

   public function addressListView(){
      echo json_encode($this->respond);
   }
   public function groupsListView(){
      echo json_encode($this->respond);
   }

   public function searchMailView(){
//      echo json_encode($this->mails);
   }
}
?>