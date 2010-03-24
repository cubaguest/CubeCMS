<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Text_View extends View {
   public function mainView() {
      $this->template()->addTplFile("text.phtml");

      if($this->rights()->isWritable()) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_text', $this->_("Upravit text"),
            $this->link()->route('edit'),
            $this->_("Upravit text"), "page_edit.png");
         $toolbox->addTool('edit_paneltext', $this->_("Upravit text panelu"),
            $this->link()->route('editpanel'),
            $this->_("Upravit text v panelu"), "page_edit.png");
         $this->toolbox = $toolbox;
      }
   }
   /*EOF mainView*/

   public function editView() {
      $this->template()->addTplFile("textedit.phtml");
   }

   public function editPanelView() {
      $this->template()->addTplFile("textpaneledit.phtml");
   }
   // EOF edittextView
   public function textHtmlView() {
      $model = new Text_Model_Detail();
      $text = $model->getText(Category::getSelectedCategory()->getId());
      if($text != false) {
         $text = $text->{Text_Model_Detail::COLUMN_TEXT};
      } else {
         $text = $this->_("Text nebyl definován, vytvoříte jej v administraci");
      }
      print ($text);
   }
}

?>