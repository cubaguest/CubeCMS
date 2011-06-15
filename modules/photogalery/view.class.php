<?php
class Photogalery_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://'.$this->category()->getParam(Photogalery_Controller::PARAM_TPL_MAIN, 'list.phtml'));
      if($this->category()->getRights()->isWritable()) {
         $this->toolboxText = new Template_Toolbox2();
         $this->toolboxText->setIcon(Template_Toolbox2::ICON_PEN);
         $toolEditText = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr("Upravit text"),
         $this->link()->route('edittext'));
         $toolEditText->setIcon('page_edit.png')->setTitle($this->tr('Upravit text galerie'));
         $this->toolboxText->addTool($toolEditText);

         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_IMAGE_WRENCH);
         $toolEditText = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr("Upravit fotky"),
         $this->link()->route('editphotos'));
         $toolEditText->setIcon('image_edit.png')->setTitle($this->tr('Upravit fotky'));
         $toolbox->addTool($toolEditText);

         if($this->category()->getRights()->isControll()){
            $this->toolboxText->setIcon(Template_Toolbox2::ICON_WRENCH);
            $toolEView = new Template_Toolbox2_Tool_PostRedirect('edit_view', $this->tr("Nastavení"),
            $this->link()->route(Routes::MODULE_SETTINGS));
            $toolEView->setIcon('wrench.png')->setTitle($this->tr('Upravit nastavení kategorie'));
            $this->toolboxText->addTool($toolEView);
         }
         
         if($this->text != false){
            $toolLangLoader = new Template_Toolbox2_Tool_LangLoader($this->text->{Text_Model::COLUMN_TEXT});
            $this->toolboxText->addTool($toolLangLoader);
         }
         
         if(isset ($_GET['l']) AND isset ($this->text[Text_Model::COLUMN_TEXT][$_GET['l']])){
            $l = $_GET['l'];
            $this->text->{Text_Model::COLUMN_TEXT} = $this->text[Text_Model::COLUMN_TEXT][$l];
            if($this->text[Text_Model::COLUMN_LABEL][$l] != null){
               $this->text->{Text_Model::COLUMN_LABEL} = $this->text[Text_Model::COLUMN_LABEL][$l];
            } else {
               $obj = Category::getSelectedCategory()->getCatDataObj();
               $this->text->{Text_Model::COLUMN_LABEL} = $obj[Model_Category::COLUMN_NAME][$l];
               unset ($obj);
            }
         }

         $this->toolboxImages = $toolbox;
      }
   }

   public function editphotosView() {
      Template_Module::setEdit(true);
      $this->template()->addPageTitle($this->tr('úprava obrázků'));
      $this->template()->addPageHeadline($this->tr('úprava obrázků'));
      $this->template()->addTplFile("editphotos.phtml");
   }

   public function uploadFileView() {

   }
   public function checkFileView() {

   }

   public function editphotoView() {
      Template_Module::setEdit(true);
      $this->template()->addTplFile("editphoto.phtml");
   }

   private function addTinyMCE() {
      $type = $this->category()->getParam(Photogalery_Controller::PARAM_EDITOR_TYPE, 'advanced');
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

   public function edittextView() {
      Template_Module::setEdit(true);
      $this->addTinyMCE();
      $this->template()->addFile("tpl://edittext.phtml");
   }
}

?>