<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class NewsLetter_View extends View {
	public function mainView() {
      $this->template()->addFile('tpl://reg_mail.phtml');
      // toolbox
      if($this->rights()->isWritable()){
         // main
         $this->toolbox = new Template_Toolbox2();
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('text_edit', $this->tr('Upravit úvodní text'), $this->link()->route('edittext'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr("Upravit úvodní text"));
         $this->toolbox->addTool($toolEdit);
      }
	}

   public function editTextView() {
      Template_Navigation::addItem($this->tr('Úprava úvodního textu'));
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://edit_text.phtml');
      $this->setTinyMCE($this->form->text, 'advanced');
   }
   
   public function unregistrationMailView() {
      Template_Navigation::addItem($this->tr('Zrušení registrace e-mailu'), $this->link());
      $this->template()->addTplFile('unreg_mail.phtml');
   }
}
?>