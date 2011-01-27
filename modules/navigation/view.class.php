<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Navigation_View extends View {
   public function mainView()
   {
      $this->template()->addFile('tpl://main.phtml');
      if($this->text != null){
         $this->text = $this->template()->filter((string)$this->text, array('anchors','filesicons'));
      }
      
      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_PEN);

         $toolET = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr('Upravit text'),
                 $this->link()->route('editText'));
         $toolET->setIcon('page_edit.png')->setTitle($this->tr("Upravit text"));
         $toolbox->addTool($toolET);

         $this->toolbox = $toolbox;

         if($this->category()->getRights()->isControll()){
            $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
            $toolEView = new Template_Toolbox2_Tool_PostRedirect('edit_view', $this->tr("Nastavení"),
            $this->link()->route(Routes::MODULE_SETTINGS));
            $toolEView->setIcon('wrench.png')->setTitle($this->tr('Upravit nastavení kategorie'));
            $this->toolbox->addTool($toolEView);
         }
      }
   }

   public function editTextView()
   {
      Template_Module::setEdit(true);
      $this->addTinyMCE();
      $this->template()->addTplFile("textedit.phtml");
   }

   private function addTinyMCE()
   {
      $type = $this->category()->getParam(Text_Controller::PARAM_EDITOR_TYPE, 'advanced');
      if($type == 'none') return;
      $this->form->text->html()->addClass("mceEditor");
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