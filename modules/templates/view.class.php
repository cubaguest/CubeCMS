<?php
class Templates_View extends View {
   public static $tpl = null;

   public function mainView() {
      $this->template()->addTplFile("list.phtml");

      if($this->rights()->isControll()) {
         $toolbox = new Template_Toolbox2();
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_template', $this->tr("Přidat šablonu"),
         $this->link()->route('add'));
         $toolAdd->setIcon('page_add.png')->setTitle($this->tr('Přidat novou šablonu'));
         $toolbox->addTool($toolAdd);

         $this->toolbox = $toolbox;
      }
      Template_Module::setEdit(true);
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      $this->template()->addTplFile("edit.phtml");
      // Editor
      $settings = new Component_TinyMCE_Settings_Full();
      $this->setTinyMCE($this->form->content, $settings);
      Template_Module::setEdit(true);
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->addView();
   }

   public function previewView() {
      $this->template()->addTplFile('preview.phtml');
      Template_Module::setEdit(true);
   }

   public static function templateView(){
//       Template_Output::addHeader('Access-Control-Allow-Origin: '.Url_Request::getBaseWebDir(true));
      Template_Output::addHeader('Access-Control-Allow-Origin: *');
      Template_Output::sendHeaders();
      echo (Templates_View::$tpl->{Templates_Model::COLUMN_CONTENT});
   }
}

?>
