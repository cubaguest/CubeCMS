<?php
class ShopProductGeneral_SiteMap extends SiteMap {
	public function run() {
      $mPord = new Shop_Model_Product();
      $lastEdit = Shop_Model_Product::getLastChange($this->category()->getId());
      $this->setCategoryLink($lastEdit ? $lastEdit : new DateTime());
      
      $records = $mPord->where(Shop_Model_Product::COLUMN_ACTIVE.' = 1 AND '.Shop_Model_Product::COLUMN_ID_CATEGORY.' = :idc', 
                      array('idc' => $this->category()->getId()))
              ->records();

      foreach ($records as $record) {
         $this->addItem($this->link()->route('detail', array('urlkey' => $record->{Shop_Model_Product::COLUMN_URLKEY})),
            $record->{Shop_Model_Product::COLUMN_NAME},
            new DateTime($record->{Shop_Model_Product::COLUMN_DATE_EDIT}), 'monthly');
      }
	}
}