<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Text_View extends View {
   public function mainView() {
   }
   /*EOF mainView*/

   public function editView() {
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