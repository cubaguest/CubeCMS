<?php
class Contact_View extends View {
   public function mainView() {
      if($this->rights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_PEN);

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('contact_edit', $this->tr("Upravit kontakt"),
                 $this->link()->route('edit'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit kontakt'));
         $toolbox->addTool($toolEdit);

         $this->toolbox = $toolbox;
      }
      $this->template()->addTplFile("main.phtml");
      $this->markers = $this->category()->getParam(Contact_Controller::PARAM_MAP_POINTS);
      $this->urlParams = $this->category()->getParam(Contact_Controller::PARAM_MAP_URL_PARAMS);


//      $googleMapsFile = new JsPlugin_JsFile("http://maps.google.com/maps");
//      $googleMapsFile->setParam("file", "api");
//      $googleMapsFile->setParam("v", "2");
//      $googleMapsFile->setParam("key", self::GOOGLE_MAP_KEY);
//      $this->template()->addJsFile($googleMapsFile);


//      $this->template()->datadir = $this->sys()->module()->getDir()->getDataDir(true);

//      $this->template()->addJsPlugin(new JsPlugin_LightBox());
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->template()->addTplFile('edit.phtml');
      $this->formEdit->text->html()->addClass("mceEditor");
      $this->addTinyMCE('mceEditor');
      $this->formEdit->textPanel->html()->addClass("mceEditorSimple");
      $this->addTinyMCE('mceEditorSimple');
   }

   private function addTinyMCE($selector) {
      $this->tinyMCE = new Component_TinyMCE();
      switch ($selector) {
         case 'mceEditorSimple':
            $settings = new Component_TinyMCE_Settings_AdvSimple();
            $settings->setSetting('editor_selector', $selector);
            break;
         case 'mceEditor':
         default:
            $settings = new Component_TinyMCE_Settings_Advanced();
            $settings->setSetting('height', '600');
            break;
      }
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
   }
}
?>