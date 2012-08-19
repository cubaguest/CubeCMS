<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Mails_View extends View {
	public function mainView() {
      $this->template()->addTplFile('main.phtml');
      $this->page = 'main';

      $this->form->text->html()->addClass("mceEditor");
      $this->tinyMCE = new Component_TinyMCE();
      $this->tinyMCE->setTplsList(Component_TinyMCE::TPL_LIST_SYSTEM_MAIL);
      $settings = new Component_TinyMCE_Settings_Advanced();
      $settings->setSetting('height', '600');
      $settings->setSetting('relative_urls', false);
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
      Template_Module::setEdit(true);
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