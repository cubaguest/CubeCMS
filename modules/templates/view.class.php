<?php
class Templates_View extends View {
   public static $tpl = null;

   public function mainView() {
      $this->template()->addTplFile("list.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('add_template', $this->_("Přidat šablonu"),
                 $this->link()->route('add'),
                 $this->_("Přidat novou šablonu"), "page_add.png");
         $this->template()->toolbox = $toolbox;
      }
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      $this->template()->addTplFile("edit.phtml");
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->addView();
   }

   public function previewView() {
      $this->template()->addTplFile('preview.phtml');
   }

   public static function templateView(){
      echo (Templates_View::$tpl->{Templates_Model::COLUMN_CONTENT});
   }
}

?>
