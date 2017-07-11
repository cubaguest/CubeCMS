<?php
class ShopProductGeneral_Search extends Search {
   public function runSearch() {
      $model = new Shop_Model_Product();
      
      $result = $model
              ->where(Shop_Model_Product::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $this->category()->getId()))
              ->search($this->getSearchString());
      if($result){
         foreach ($result as $res) {
            if((string)$res->{Shop_Model_Product::COLUMN_TEXT_SHORT} != null){
               $text = $res->{Shop_Model_Product::COLUMN_TEXT_SHORT};
            } else {
               $text = $res->{Shop_Model_Product::COLUMN_TEXT};
            }
            $this->addResult(
               $res->{Shop_Model_Product::COLUMN_NAME},
               $this->link()->route('detail', array('urlkey' => $res->{Shop_Model_Product::COLUMN_URLKEY})),
               $text, $res->{Search::COLUMN_RELEVATION});
         }
      }
   }
}