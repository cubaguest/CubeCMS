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

   public function showView() {
      $this->template()->addTplFile("detail.phtml", 'articles');

      if($this->category()->getRights()->isControll() OR
              ($this->category()->getRights()->isWritable() AND
                      $this->article->{Articles_Model_Detail::COLUMN_ID_USER} == Auth::getUserId())) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_article', $this->_("Upravit"),
                 $this->link()->route('edit'),
                 $this->_("Upravit zobrazený článek"), "page_edit.png");
         $toolbox->addTool('article_delete', $this->_("Smazat"),
                 $this->link(), $this->_("Smazat zobrazený článek"), "page_delete.png",
                 'article_id', (int)$this->article->{Articles_Model_Detail::COLUMN_ID},
                 $this->_('Opravdu smazat článek?'));
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
