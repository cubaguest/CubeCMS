<?php

class UserReg_View extends View {

   public function mainView() {
      if ($this->rights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('text_edit', $this->tr("Upravit text"),
               $this->link()->route('editText'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit úvodní text'));
         $toolbox->addTool($toolEdit);
         
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('welcome_edit', $this->tr("Upravit uvítací text"),
               $this->link()->route('editWelcome'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit text po úspěšné registraci'));
         $toolbox->addTool($toolEdit);

         $toolEditM = new Template_Toolbox2_Tool_PostRedirect('mailreg_edit', $this->tr("Upravit registrační e-mail"),
               $this->link()->route('editRegMail'));
         $toolEditM->setIcon('page_edit.png')->setTitle($this->tr('Upravit registrační e-mail'));
         $toolbox->addTool($toolEditM);

         $this->toolbox = $toolbox;
      }
      $this->template()->addTplFile("registration.phtml");
   }

   public function completeRegView() {
      $this->template()->addTplFile("regerror.phtml");
   }

   public function welcomeView() {
      $this->template()->addTplFile("welcome.phtml");
      Template_Navigation::addItem($this->tr('Uvítání'), $this->link());
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editTextView() {
      $this->headline = $this->tr('Úprava úvodního textu');
      $this->template()->addTplFile('edit_text.phtml');
      $this->setTinyMCE($this->formEdit->text, 'advanced');
      Template_Module::setEdit(true);
      Template_Navigation::addItem($this->tr('Úprava úvodního textu'), $this->link());
   }

   public function editWelcomeView() {
      $this->headline = $this->tr('Úprava uvítacího textu');
      $this->template()->addTplFile('edit_text.phtml');
      $this->setTinyMCE($this->formEdit->text, 'advanced');
      Template_Module::setEdit(true);
      Template_Navigation::addItem($this->tr('Úprava uvítacího textu'), $this->link());
   }

   public function editRegMailView() {
      $this->template()->addTplFile('edit_mail.phtml');
      
      $this->tinyMCE = new Component_TinyMCE();
      $this->tinyMCE->setTplsList(Component_TinyMCE::TPL_LIST_SYSTEM_MAIL);
      $settings = new Component_TinyMCE_Settings_Advanced();
      $settings->setSetting('height', '600');
      $settings->setSetting('relative_urls', false);
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
      $this->form->text_mail->html()->addClass("mceEditor");
      Template_Module::setEdit(true);
      Template_Navigation::addItem($this->tr('Úprava textu v e-mailu'), $this->link());
   }

   public function varListView() {
      echo json_encode($this->vars);
   }
   
   private function addTinyMCE($form, $type = 'advanced') {
      if($type == 'none') return;
      $form->text->html()->addClass("mceEditor");
      $this->tinyMCE = new Component_TinyMCE();
      switch ($type) {
         case 'simple':
            $settings = new Component_TinyMCE_Settings_AdvSimple();
            $settings->setSetting('editor_selector', 'mceEditor');
            break;
         case 'full':
            // TinyMCE
            $settings = new Component_TinyMCE_Settings_Full();
            break;
         case 'advanced':
         default:
            $settings = new Component_TinyMCE_Settings_Advanced();
            break;
      }
      $settings->setSetting('height', '600');
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
   }
}
?>