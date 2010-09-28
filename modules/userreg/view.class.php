<?php

class UserReg_View extends View {

   public function mainView() {
      if ($this->rights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('text_edit', $this->_("Upravit text"),
               $this->link()->route('editText'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->_('Upravit úvodní text'));
         $toolbox->addTool($toolEdit);
         $this->toolboxText = $toolbox;

         $toolbox = new Template_Toolbox2();
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('welcome_edit', $this->_("Upravit uvítací text"),
               $this->link()->route('editWelcome'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->_('Upravit text po úspěšné registraci'));
         $toolbox->addTool($toolEdit);

         $toolEditM = new Template_Toolbox2_Tool_PostRedirect('mailreg_edit', $this->_("Upravit registrační e-mail"),
               $this->link()->route('editRegMail'));
         $toolEditM->setIcon('page_edit.png')->setTitle($this->_('Upravit registrační e-mail'));
         $toolbox->addTool($toolEditM);

         $this->toolboxForm = $toolbox;
      }
      $this->template()->addTplFile("registration.phtml");
   }

   public function completeRegView() {
      $this->template()->addTplFile("regerror.phtml");
   }

   public function welcomeView() {
      $this->template()->addTplFile("welcome.phtml");
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editTextView() {
      $this->headline = $this->_('Úprava úvodního textu');
      $this->template()->addTplFile('edit_text.phtml');
   }

   public function editWelcomeView() {
      $this->headline = $this->_('Úprava uvítacího textu');
      $this->template()->addTplFile('edit_text.phtml');
   }

   public function editRegMailView() {
      $this->template()->addTplFile('edit_mail.phtml');
   }

   public function varListView() {
      echo json_encode($this->vars);
   }
}
?>