<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class NewsLetter_View extends View {
	public function mainView() {
      $this->template()->addTplFile('reg_mail.phtml');
      // toolbox
      if($this->rights()->isWritable()){
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_text',$this->_('Upravit úvodní text'),
                 $this->link()->route('edittext'), $this->_('Upravit úvodní text'), 'page_edit.png');
         $toolbox->addTool('registered_mails',$this->_('Registrované adresy'),
                 $this->link()->route('list'), $this->_('Správa registrovaných emailových adres'), 'page_edit.png');
//         $toolbox->addTool('edit_mail_text',$this->_('Uprvit text e-mailu'),
//                 $this->link()->route('edit-mail-text'), $this->_('Úprava textu adesílaného emailu'), 'page_edit.png');

         $this->toolbox = $toolbox;
      }
	}

   public function editTextView() {
      $this->template()->addTplFile('edit_text.phtml');
   }

   public function listMailsView() {
      $this->template()->addTplFile('mails_list.phtml');
   }

   public function listMailsExportView() {
      $result = null;
      switch ($this->type) {
         case 'csv':
            $csv = new Component_CSV();
            foreach ($this->mails as $mail) {
               $csv->addRow($mail->{NewsLetter_Model_Mails::COLUMN_MAIL});
            }
            $csv->flush();
            break;
         case 'txt':
         default:
            foreach ($this->mails as $mail) {
               $result .= $mail->{NewsLetter_Model_Mails::COLUMN_MAIL}."\n";
            }
            break;
      }
      print($result);
   }

   public function unregistrationMailView() {
      $this->template()->addTplFile('unreg_mail.phtml');
   }
}
?>