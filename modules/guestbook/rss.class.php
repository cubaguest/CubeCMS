<?php
class Guestbook_Rss extends Rss {
   public function  runController() {
      $model = new GuestBook_Model();
      $items = $model->where(GuestBook_Model::COLUMN_ID_CAT.' = :idc AND '.GuestBook_Model::COLUMN_DELETED.' = 0',
         array('idc' => $this->category()->getId()))
         ->order(array(GuestBook_Model::COLUMN_DATE_ADD => Model_ORM::ORDER_DESC))
         ->limit(0, VVE_FEED_NUM)->records();

      foreach ($items as $item) {
         $this->getRssComp()->addItem(null, $item->{GuestBook_Model::COLUMN_TEXT},
               $this->link(), new DateTime($item->{GuestBook_Model::COLUMN_DATE_ADD}),
               $item->{GuestBook_Model::COLUMN_NICK}, null, null,
               $item->{GuestBook_Model::COLUMN_ID});
      }
   }
}
?>
