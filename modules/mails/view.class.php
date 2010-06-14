<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Mails_View extends View {
	public function mainView() {
      $this->template()->addTplFile('mails_list.phtml');
      // toolbox
      if($this->rights()->isWritable()){
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('add_mail',$this->_('Přidat mail'),
                 $this->link()->route('add_address'), $this->_('Přidat mail do knihy adres'), 'mail_add.png');
         $this->toolbox = $toolbox;
      }
	}

   public function listMailsView() {
      $this->template()->addTplFile('mails_list.phtml');
   }

   public function addMailView() {
      $this->template()->addTplFile('edit_mail.phtml');
   }

   public function editMailView() {
      $this->addMailView();
      $this->edit = true;
   }

   public function exportView() {
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
               $result .= $mail['mail'];
               if($mail['name'] != null) $result .= ' '.$mail['name'];
               if($mail['surname'] != null) $result .= ' '.$mail['surname'];
               $result .= "\n";
            }
            break;
      }
      echo($result);
      flush();
      exit();
   }

   public function deleteMailsView() {}

   public function composeMailView() {
      $this->template()->addTplFile('compose_mail.phtml');
   }
}
?>