<?php
class News_View extends Articles_View {
   public function mainView() {
      $feeds = new Component_Feed();
      $feeds->setConfig('feedLink', $this->link()->clear());
      $this->template()->feedsComp = $feeds;
      
      $this->template()->addTplFile("list.phtml");
      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('add_article', $this->_("Přidat novinku"),
            $this->link()->route('add'),
            $this->_("Přidat novou aktulitu"), "page_add.png");
         $this->toolbox = $toolbox;
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
                 $this->_("Upravit zobrazenou novinku"), "page_edit.png");
         $toolbox->addTool('article_delete', $this->_("Smazat"),
                 $this->link(), $this->_("Smazat zobrazenou novinku"), "page_delete.png",
                 'article_id', (int)$this->article->{Articles_Model_Detail::COLUMN_ID},
                 $this->_('Opravdu smazat aktualitu?'));
         $this->template()->toolbox = $toolbox;
      }
   }

   public function archiveView() {
      $this->template()->addTplFile("archive.phtml", 'articles');
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
}

?>
