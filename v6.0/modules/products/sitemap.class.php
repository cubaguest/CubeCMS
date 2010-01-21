<?php
class Products_SiteMap extends SiteMap {
	public function run() {
      $productModel = new Products_Model_List($this->sys());

      // kategorie
      $this->addCategoryItem($productModel->getLastChange());
      $prodArr = $productModel->getListProducts();
      foreach ($prodArr as $product) {
         $this->addItem($this->link()->article($product[Products_Model_Detail::COLUMN_PRODUCT_LABEL],
               $product[Products_Model_Detail::COLUMN_PRODUCT_ID]),
            $product[Products_Model_Detail::COLUMN_PRODUCT_LABEL],
            $product[Products_Model_Detail::COLUMN_PRODUCT_EDIT_TIME]);
      }
	}
}
?>