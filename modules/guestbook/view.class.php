<?php

/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class GuestBook_View extends View {

   public function mainView() {
      $this->template()->addTplFile('form.phtml');

      if ($this->category()->getRights()->isWritable()) {
         // toolbox pro item
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_DELETE);
         $toolRemove = new Template_Toolbox2_Tool_Form($this->formDel);
         $toolRemove->setIcon(Template_Toolbox2::ICON_DELETE)->setConfirmMeassage($this->tr('Opravdu smazat příspěvek?'));
//            $toolRemove->getForm()->id->setValues((int)$row->{GuestBook_Model_Detail::COL_ID});
         $toolbox->addTool($toolRemove);
         $this->toolboxItem = $toolbox;
         if($this->category()->getRights()->isControll()){
            $this->toolbox = new Template_Toolbox2();
            $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
            $toolEView = new Template_Toolbox2_Tool_PostRedirect('edit_view', $this->tr("Nastavení"),
            $this->link()->route(Routes::MODULE_SETTINGS));
            $toolEView->setIcon(Template_Toolbox2::ICON_WRENCH)->setTitle($this->tr('Upravit nastavení kategorie'));
            $this->toolbox->addTool($toolEView);
         }
      }

      // add tinymce
      if($this->category()->getParam(GuestBook_Controller::PARAM_WISIWIG_EDITOR, true) == true){
         $this->form->text->html()->addClass("mceEditor");
         $this->tinyMCE = new Component_TinyMCE();
         $this->tinyMCE->setConfig(Component_TinyMCE::CFG_ALLOW_INTERNAL_SOURCES, false);
         $this->tinyMCE->setConfig(Component_TinyMCE::CFG_ALLOW_INTERNAL_TPLS, false);
         $settings = new Component_TinyMCE_Settings_AdvSimple2();
         $settings->setSetting('editor_selector', 'mceEditor');
         $this->tinyMCE->setEditorSettings($settings);
         $this->tinyMCE->mainView();
      }
   }

   /* EOF mainView */
}
?>