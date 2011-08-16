<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class ShopOrders_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://list.phtml');
      Template_Module::setEdit(true);

//      $toolbox = new Template_Toolbox2();
//      $toolbox->setIcon(Template_Toolbox2::ICON_PEN);
//
//      $toolET = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr('Upravit text'),
//                 $this->link()->route('edit'));
//      $toolET->setIcon('page_edit.png')->setTitle($this->tr("Upravit text"));
//      $toolbox->addTool($toolET);
//
//      $this->toolbox = $toolbox;
   }
   
   public function ordersListView(){
      echo json_encode($this->respond);
   }

   public function viewOrderView()
   {
      $this->template()->addFile('tpl://detail.phtml');
   }
   
   private function addTinyMCE() {
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