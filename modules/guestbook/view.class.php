<?php

/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class GuestBook_View extends View {

   public function mainView() {
      $this->template()->addTplFile('form.phtml');
      $this->template()->addTplFile('list.phtml');

      if ($this->category()->getRights()->isWritable()) {
         // toolbox pro item
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_DELETE);
         $toolRemove = new Template_Toolbox2_Tool_Form($this->formDel);
         $toolRemove->setIcon(Template_Toolbox2::ICON_DELETE)->setConfirmMeassage($this->_('Opravdu smazat příspěvek?'));
//            $toolRemove->getForm()->id->setValues((int)$row->{GuestBook_Model_Detail::COL_ID});
         $toolbox->addTool($toolRemove);
         $this->toolboxItem = $toolbox;
      }
   }

   /* EOF mainView */

   public function exportFeedView() {
      $feed = new Component_Feed(true);

      $feed->setConfig('type', $this->type);
      $feed->setConfig('css', 'rss.css');
      $feed->setConfig('title', $this->category()->getName());
      $feed->setConfig('desc', $this->category()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION});
      $feed->setConfig('link', $this->link());

      $model = new GuestBook_Model_Detail();
      $items = $model->getList($this->category()->getId(), 0, VVE_FEED_NUM);

      while ($item = $items->fetch()) {
         $feed->addItem(null, $item->{GuestBook_Model_Detail::COL_TEXT},
            null, new DateTime($item->{GuestBook_Model_Detail::COL_DATE_ADD}),
            $item->{GuestBook_Model_Detail::COL_NICK}, null, null,
            $item->{GuestBook_Model_Detail::COL_ID});
      }

      $feed->flush();
   }

}
?>