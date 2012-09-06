<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Mails_View extends View {
	public function mainView() {
      $this->template()->addTplFile('main.phtml');
      $this->page = 'main';
      Template_Module::setEdit(true);
      
      
      $settings = new Component_TinyMCE_Settings_Mail();
      $settings->setTemplatesList(Component_TinyMCE_Settings_Advanced::TPL_LIST_SYSTEM_MAIL);
      $this->setTinyMCE($this->form->text, $settings);
	}

   public function sendMailsQueueView()
   {
      $this->template()->addTplFile('mailsqueue.phtml');
      Template_Module::setEdit(true);
   }

   public function listMailsView() {
      $this->template()->addTplFile('main.phtml');
      Template_Module::setEdit(true);
   }

   public function sendMailsListView() {
      $this->template()->addTplFile('list_mails.phtml');
      $this->page = 'sendMailsList';
      Template_Module::setEdit(true);
   }

   public function searchMailView(){
//      echo json_encode($this->mails);
   }
}
?>