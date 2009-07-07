<?php
class Actions_View extends View {
   public function mainView() {
      if($this->rights()->isWritable()){
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('add_action', $this->_("Přidat"),
            $this->link()->action($this->sys()->action()->addNewAction()),
            $this->_("Přidat akci"), "text_add.png");
         $this->template()->toolbox = $toolbox;
      }

      $this->template()->addTplFile("list.phtml");
      $this->template()->addCssFile("style.css");
   }

   public function showView(){
      $actionDetailM = new Actions_Model_Detail($this->sys());
      $this->template()->action = $actionDetailM->getActionDetailSelLang($this->sys()->article());
      
      if($this->rights()->isWritable()){
         // editační tlačítka
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_action', $this->_("Upravit"),
            $this->link()->action($this->sys()->action()->editAction()),
            $this->_("Upravit akci"), "text_edit.png")
         ->addTool(Actions_Controller::FORM_PREFIX.Actions_Controller::FORM_BUTTON_DELETE,
            $this->_("Smazat"), $this->link(),
            $this->_("Smazat akci"), "remove.png", Actions_Controller::FORM_PREFIX.Actions_Controller::FORM_INPUT_ID,
            $this->template()->action[Actions_Model_Detail::COLUMN_ACTION_ID],
            $this->_("Opravdu smazat akci")."?");
         $this->template()->toolbox = $toolbox;
      }
      
      $this->template()->addTplFile("actionDetail.phtml");
      $this->template()->addCssFile("style.css");
   }

   /**
    * Viewer pro přidání novinky
    */
   public function addNewActionView() {
      $this->template()->addTplFile('editAction.phtml');
      $this->template()->addCssFile("style.css");
      $this->template()->pageLabel = $this->_("Přidání akce");

      //Tabulkové uspořádání
      $jquery = new JsPlugin_JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);

      $tinyMce = new JsPlugin_TinyMce();
      $tinyMce->setTheme(JsPlugin_TinyMce::TINY_THEME_ADVANCED_SIMPLE);
      $this->template()->addJsPlugin($tinyMce);
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editActionView() {
      $this->template()->addTplFile('editAction.phtml');
      $this->template()->addCssFile("style.css");


      $actM = new Actions_Model_Detail($this->sys());
      $this->template()->action = $actM->getActionDetailAllLangs($this->sys()->article());
      $this->template()->editAction = true;

      $this->template()->pageLabel = $this->_("Úprava akce");

      //Taby - uspořádání
      $jquery = new JsPlugin_JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);

      $tinyMce = new JsPlugin_TinyMce();
      $tinyMce->setTheme(JsPlugin_TinyMce::TINY_THEME_ADVANCED_SIMPLE);
      $this->template()->addJsPlugin($tinyMce);
   }
}

?>