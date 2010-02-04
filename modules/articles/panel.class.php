<?php
class Articles_Panel extends Panel {

   public function panelController() {
   }

   public function panelView() {
      $artM = new Articles_Model_List();
      switch ($this->panelObj()->{Model_Panel::COLUMN_TPL}) {
         case 'panel_top.phtml':
            $this->template()->topArticles = $artM->getListTop($this->category()->getId(), 0, 3);
            break;
         case 'panel.phtml':
         default:
            $this->template()->newArticles = $artM->getList($this->category()->getId(), 0, 3);
            break;
      }
      $this->template()->rssLink = $this->link()->route('export', array('type' => 'rss'));

      $this->template()->addTplFile($this->panelObj()->{Model_Panel::COLUMN_TPL});
   }
}
?>